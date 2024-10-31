<?php 
namespace Classes;

class Pendente {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

   
    public function verDetalhes($pid){

        $r = [];

        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $resPrincipal = $query->fetch(\PDO::FETCH_ASSOC);
        //var_dump($resPrincipal);

        if($resPrincipal["tipo"] == "recorrente"){

            $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            #$transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res/*, ["pagamentos"=>$transacoes]*/);
            return ["ok"=>true, "payload"=> $r];

        }
        if($resPrincipal["tipo"] == "parcelado"){
            
            $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            #$transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res /*, ["pagamentos"=>$transacoes]*/);
            return ["ok"=>true, "payload"=> $r];
        }
        return ["ok"=>true, "payload"=> $resPrincipal];
    }
    public function verTodos($conta){
                
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE de = :de AND executado = :executado");
        $query->bindValue(':de', $conta);
        $query->bindValue(':executado', '0');
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE para = :para AND executado = :executado AND pedido = :pedido");
        $query->bindValue(':para', $conta);
        $query->bindValue(':executado', '0');
        $query->bindValue(':pedido', '1');
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);
        $res = array_merge($resUm,$resDois);

        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_ASC, $res);
        
        return ["ok"=>true, "payload"=> $res];
    }

    public function cancelarPendente($pid){
        #ini_set('display_errors', 1);
        #ini_set('display_startup_errors', 1);
        #error_reporting(E_ALL);

        $commits = [];
        $transacao = $this->verDetalhes($pid)["payload"];
        $quando = date("d-m-Y h:i:s");
        
        $queryDe=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados, quando) VALUES (:id, :pid, :dados, :quando)");
        $queryDe->bindValue(':id', $transacao["de"]);
        $queryDe->bindValue(':pid', $pid);
        $queryDe->bindValue(':dados', json_encode($transacao));
        $queryDe->bindValue(':quando', $quando);
        array_push($commits, $queryDe);

        
        $queryPara=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados, quando) VALUES (:id, :pid, :dados, :quando)");
        $queryPara->bindValue(':id', $transacao["para"]);
        $queryPara->bindValue(':pid', $pid);
        $queryPara->bindValue(':dados', json_encode($transacao));
        $queryPara->bindValue(':quando', $quando);
        array_push($commits, $queryPara);

        $queryT=$this->conexao->prepare("DELETE FROM transacao WHERE pid = :pid");
        $queryT->bindValue(':pid', $pid);
        array_push($commits, $queryT);
        if ($transacao["tipo"] == "recorrente") {
            $queryRecorrente = $this->conexao->prepare("DELETE FROM recorrente WHERE identificador = :id");
            $queryRecorrente->bindValue(':id', $transacao["identificador"]);
            array_push($commits, $queryRecorrente);
        }
        if ($transacao["tipo"] == "parcelado") {
            $queryParcelado = $this->conexao->prepare("DELETE FROM parcelado WHERE identificador = :id");
            $queryParcelado->bindValue(':id', $transacao["identificador"]);
            array_push($commits, $queryParcelado);
        }
         try {

            $this->conexao->beginTransaction();
            foreach ($commits as $query) {
                $query->execute();
            }
            $this->conexao->commit();
            $commits = [];
            return ["ok"=>true, "payload"=> "Cancelou"];
        } catch (\PDOException $e) {
            $this->conexao->rollBack();
            return ["ok"=>false, "payload"=> $e->getMessage()];
        }
    }

}