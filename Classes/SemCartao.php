<?php 
namespace Classes;

class SemCartao {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

   
    public function verDetalhes($pid){

        $r = [];

        $query=$this->conexao->prepare("SELECT * FROM levantamentosemcartao WHERE identificador = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $resPrincipal = $query->fetch(\PDO::FETCH_ASSOC);
        //var_dump($resPrincipal);
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


        foreach($res as $k => $v){
            $pid = $v["pid"];
            if($v["tipo"]=="parcelado"){
                $query=$this->conexao->prepare("SELECT periodicidade, parcelas, valor_parcela, valor_total FROM parcelado WHERE transacao_pid LIKE '%$pid%'");
                $query->execute();
                $resParcelado = $query->fetch(\PDO::FETCH_ASSOC);
                $res[$k]["tipo"] = "Parcelado, ".$resParcelado["periodicidade"]." | ".$resParcelado["valor_total"].", em ".$resParcelado["parcelas"]."x de ".$resParcelado["valor_parcela"];
            }
            if($v["tipo"]=="recorrente"){

                $query=$this->conexao->prepare("SELECT periodicidade, valor FROM recorrente WHERE transacao_pid LIKE '%$pid%'");
                $query->execute();
                $resRecorrente = $query->fetch(\PDO::FETCH_ASSOC);
                $res[$k]["tipo"] = "Recorrente, ".$resRecorrente["periodicidade"]." | de ".$resRecorrente["valor"];
            }
        }

        array_multisort(array_map(function($element) {
            return $element['pid'];
        }, $res), SORT_DESC, $res);
        
        return ["ok"=>true, "payload"=> $res];
    }

    public function cancelarPendente($id){
        #ini_set('display_errors', 1);
        #ini_set('display_startup_errors', 1);
        #error_reporting(E_ALL);

       $levantamento = $this->verDetalhes($id)["payload"];
        $quando = date("d-m-Y h:i:s");
        
        $query=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados, quando) VALUES (:id, :pid, :dados, :quando)");
        $query->bindValue(':id', $levantamento["identificador"]);
        $query->bindValue(':pid', $id);
        $query->bindValue(':dados', json_encode($levantamento));
        $query->bindValue(':quando', $quando);
        $query->execute();

        $queryT=$this->conexao->prepare("DELETE FROM levantamentosemcartao WHERE identificador = :id");
        $queryT->bindValue(':id', $id);
        $query->execute();

        return ["ok"=>true, "payload"=> "Cancelou o levantamento se cartão"];
    }

    public function novoLevantamento($clienteIdentificador,$total,$codigo, $telefone){
        $quando = date("d-m-Y h:i:s A");
        $usuario = $this->contaBalancoTipo($telefone);
        if($usuario["saldo"]<$total){
            return ["ok"=>false, "payload"=> "Saldo insuficiente"];
        }
        $referencia = $this->geraReferencia();
        $query=$this->conexao->prepare("INSERT INTO levantamentosemcartao (cliente_identificador, total, quando, dia, mes, ano, codigo, referencia) VALUES (:clienteIdentificador, :total, :quando, :dia, :mes, :ano, :codigo, :referencia)");
        $query->bindValue(':clienteIdentificador', $clienteIdentificador);
        $query->bindValue(':total', $total);
        $query->bindValue(':quando', $quando);
        $query->bindValue(':dia', date("d"));
        $query->bindValue(':mes', date("m"));
        $query->bindValue(':ano', date("Y"));
        $query->bindValue(':codigo', $codigo);
        $query->bindValue(':referencia', $referencia);
        $query->execute();
        return ["ok"=>true, "payload"=> $referencia];
    }
    public function geraReferencia(){
        return $this->funcoes::tresDigitos()." ".$this->funcoes::tresDigitos()." ".$this->funcoes::tresDigitos()." ".$this->funcoes::umDigito();
    }
     public function contaBalancoTipo($telefone)
    {

        $query = $this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :tel");
        $query->bindValue(':tel', $telefone);
        $query->execute();
        $cliente_identificador = $query->fetch(\PDO::FETCH_COLUMN);

        $query = $this->conexao->prepare("SELECT balanco, identificador FROM cliente WHERE identificador = :id");
        $query->bindValue(':id', $cliente_identificador);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_ASSOC);
        return ["cliente_identificador" => $res["identificador"], "saldo" => $res["balanco"]];
    }

}