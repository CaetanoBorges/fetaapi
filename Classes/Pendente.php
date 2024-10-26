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
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes]);
            return $r;

        }
        if($resPrincipal["tipo"] == "parcelado"){
            
            $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes]);
            return $r;
        }

        return $resPrincipal;
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
        return $res;
    }

    public function cancelarPendente($pid){
        $commits = [];
        $transacao = $this->verDetalhes($pid);

        $queryDe=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados) VALUES (:id, :pid, :dados)");
        $queryDe->bindValue(':id', $transacao["de"]);
        $queryDe->bindValue(':pid', $pid);
        $queryDe->bindValue(':dados', json_encode($transacao));
        array_push($commits, $queryDe);

        
        $queryPara=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados) VALUES (:id, :pid, :dados)");
        $queryPara->bindValue(':id', $transacao["para"]);
        $queryPara->bindValue(':pid', $pid);
        $queryPara->bindValue(':dados', json_encode($transacao));
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
            return json_encode(["message" => "Cancelou", "ok" => true]);
        } catch (\PDOException $e) {
            $this->conexao->rollBack();
            return json_encode(["message" => $e->getMessage(), "ok" => false]);
        }
    }

}