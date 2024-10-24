<?php 
namespace Classes;

class Recorrente {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function verDetalhes($pid){

        $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE identificador = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $parcelado = $query->fetch(\PDO::FETCH_ASSOC);
        if($parcelado){
            $r = [];
            foreach(json_decode($parcelado["transacao_pid"]) as $k => $v){
                $query=$this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid");
                $query->bindValue(':pid', $v);
                $query->execute();
                $res = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($r, $res);
            }
            $parcelado["transacoes"] = $r;
            return $parcelado;
        }

        $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE identificador = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $recorrente = $query->fetch(\PDO::FETCH_ASSOC);
        if($recorrente){
            $r = [];
             foreach(json_decode($recorrente["transacao_pid"]) as $k => $v){
                $query=$this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid");
                $query->bindValue(':pid', $v);
                $query->execute();
                $res = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($r, $res);
            }

            $recorrente["transacoes"] = $r;
            return $recorrente;
        }

        return [];
    }
    public function verTodos($conta){
        $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE ativo = :ativo AND para = :para");
        $query->bindValue(':ativo', '1');
        $query->bindValue(':para', $conta);
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE ativo = :ativo AND de = :de");
        $query->bindValue(':ativo', '1');
        $query->bindValue(':de', $conta);
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);

        $parcelado = array_merge($resUm, $resDois);
        
        $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE ativo = :ativo AND para = :para");
        $query->bindValue(':ativo', '1');
        $query->bindValue(':para', $conta);
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE ativo = :ativo AND de = :de");
        $query->bindValue(':ativo', '1');
        $query->bindValue(':de', $conta);
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);

        $recorrente = array_merge($resUm, $resDois);

        $res = array_merge($parcelado, $recorrente);
        return $res;
    }
    
 
    
}