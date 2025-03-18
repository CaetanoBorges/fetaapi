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

   
    public function verDetalhes($id){

        $r = [];

        $query=$this->conexao->prepare("SELECT * FROM levantamentosemcartao WHERE identificador = :id");
        $query->bindValue(':id', $id);
        $query->execute();
        $resPrincipal = $query->fetch(\PDO::FETCH_ASSOC);
        //var_dump($resPrincipal);
        return ["ok"=>true, "payload"=> $resPrincipal];
    }
    public function verTodos($conta){
        
        $dataLimite = strtotime(date('Y-m-d h:i:s',strtotime("-1 days")));
        $query=$this->conexao->prepare("SELECT * FROM levantamentosemcartao WHERE cliente_identificador = :de AND tempo > :quando");
        $query->bindValue(':de', $conta);
        $query->bindValue(':quando', $dataLimite);
        $query->execute();
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_DESC, $res);
        
        return ["ok"=>true, "payload"=> $res];
    }

    public function cancelarLevantamentoSemCartao($id, $conta){
        #ini_set('display_errors', 1);
        #ini_set('display_startup_errors', 1);
        #error_reporting(E_ALL);

       $levantamento = $this->verDetalhes($id)["payload"];
        $quando = date("d-m-Y h:i:s");
        
        $query=$this->conexao->prepare("INSERT INTO anulado (conta, operacao, dados, quando) VALUES (:id, :pid, :dados, :quando)");
        $query->bindValue(':id', $conta);
        $query->bindValue(':pid', $id);
        $query->bindValue(':dados', json_encode($levantamento));
        $query->bindValue(':quando', $quando);
        $query->execute();

        $queryT=$this->conexao->prepare("DELETE FROM levantamentosemcartao WHERE identificador = :id");
        $queryT->bindValue(':id', $id);
        $queryT->execute();

        return ["ok"=>true, "payload"=> "Cancelou o levantamento sem cartão"];
    }

    public function novoLevantamento($clienteIdentificador,$total,$codigo, $telefone){
        $id = $this->funcoes::chaveDB();
        $quando = date("d-m-Y h:i:s A");
        $usuario = $this->contaBalancoTipo($telefone);
        if($usuario["saldo"]<$total){
            return ["ok"=>false, "payload"=> "Saldo insuficiente"];
        }
        $referencia = $this->geraReferencia();
        $query=$this->conexao->prepare("INSERT INTO levantamentosemcartao (identificador, cliente_identificador, total, tempo, quando, dia, mes, ano, codigo, referencia) VALUES (:identificador, :clienteIdentificador, :total, :tempo, :quando, :dia, :mes, :ano, :codigo, :referencia)");
        $query->bindValue(':identificador', $id);
        $query->bindValue(':clienteIdentificador', $clienteIdentificador);
        $query->bindValue(':total', $total);
        $query->bindValue(':tempo', strtotime(date('Y-m-d h:i:s')));
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