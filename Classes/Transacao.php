<?php 
namespace Classes;

class Transacao {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function adicionar($array){

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
    }
    public function verTodos($conta,$mes, $ano){
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE executado = :executado AND mes = :mes AND ano = :ano AND para = :para");
        $query->bindValue(':executado', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->bindValue(':para', $conta);
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE executado = :executado AND mes = :mes AND ano = :ano AND de = :de");
        $query->bindValue(':executado', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->bindValue(':de', $conta);
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);

        $res = array_merge($resUm, $resDois);

        return $res;
    }
    public function verTodosInit($conta){
        $ano = date("Y");
        $mes = date("m");
        $r = [];
        $query=$this->conexao->prepare("SELECT ano FROM transacao WHERE de = :de OR para = :para AND executado = :executado GROUP BY ano");
        $query->bindValue(':de', $conta);
        $query->bindValue(':para', $conta);
        $query->bindValue(':executado', '1');
        $query->execute();
        $anos = $query->fetchAll(\PDO::FETCH_COLUMN);
        //return $anos;

        foreach($anos as $k => $v){
            //echo $k;
            $query=$this->conexao->prepare("SELECT mes FROM transacao WHERE executado = :executado AND ano = :ano AND de = :de GROUP BY mes");
            $query->bindValue(':executado', '1');
            $query->bindValue(':ano', $v);
            $query->bindValue(':de', $conta);
            $query->execute();
            $mesUm = $query->fetchAll(\PDO::FETCH_COLUMN);

            $query=$this->conexao->prepare("SELECT mes FROM transacao WHERE executado = :executado AND ano = :ano AND para = :para GROUP BY mes");
            $query->bindValue(':executado', '1');
            $query->bindValue(':ano', $v);
            $query->bindValue(':para', $conta);
            $query->execute();
            $mesDois = $query->fetchAll(\PDO::FETCH_COLUMN);

            $meses = array_merge($mesUm,$mesDois);
            $r["datas"][$k]["ano"] = $v;
            $r["datas"][$k][$v] = $meses;
            //var_dump($r);
        }

        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE executado = :executado AND mes = :mes AND ano = :ano AND para = :para");
        $query->bindValue(':executado', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->bindValue(':para', $conta);
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT * FROM transacao WHERE executado = :executado AND mes = :mes AND ano = :ano AND de = :de");
        $query->bindValue(':executado', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->bindValue(':de', $conta);
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);

        $res = array_merge($resUm, $resDois);
        $r["atual"]["res"] = $res;
        $r["atual"]["mes"] = $mes;
        $r["atual"]["ano"] = $ano;

        return $r;
    }
    public function Estatistica($array){
        
    }
    
}