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


        if($resPrincipal["tipo"] == "recorrente"){

            $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE JSON_CONTAINS(transacao_pid, :pid)");
            $query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes]);
            return $r;

        }
        if($resPrincipal["tipo"] == "parcelado"){
            
            $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE JSON_CONTAINS(transacao_pid, :pid)");
            $query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes]);
            return $r;
        }

        return $resPrincipal;
    }
    public function verTodos($conta){
                
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE executado = :executado AND de = :de");
        $query->bindValue(':executado', '0');
        $query->bindValue(':de', $conta);
        $query->execute();
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }
    
}