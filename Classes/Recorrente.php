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
            $parcelado["pagamentos"] = count(json_decode($parcelado["transacao_pid"]));
            return ["ok"=>true, "payload"=> $parcelado];
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
            $recorrente["pagamentos"] = count(json_decode($recorrente["transacao_pid"]));
            return ["ok"=>true, "payload"=> $recorrente];
        }

        return ["ok"=>true, "payload"=> []];
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
        $parceladoT=[];
        foreach($parcelado as $k => $v){
            $parcelado[$k]["tipo"] = "parcelado";
        }

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
        foreach($recorrente as $k => $v){
            $recorrente[$k]["tipo"] = "recorrente";
        }

        $res = array_merge($parcelado, $recorrente);

        foreach($res as $k => $v){
            $res[$k]["transacoes"] = [];
            foreach(json_decode($v["transacao_pid"]) as $key => $val){
                $query=$this->conexao->prepare("SELECT *, descricao as tipo FROM transacao WHERE pid = :pid");
                $query->bindValue(':pid', $val);
                $query->execute();
                $tr = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($res[$k]["transacoes"], $tr);
                if($key==0){    
                    $res[$k]["onde"] = $tr["onde"];
                    $res[$k]["descricao"] = $tr["descricao"];
                }
            }
            if($v["de"]==$conta){
                $res[$k]["enviar"] = 1;
            }else{
                $res[$k]["enviar"] = 0;
            }
        }
        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_ASC, $res);
        return ["ok"=>true, "payload"=> $res];
    }
    
 
    
    
}