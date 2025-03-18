<?php

namespace Classes;
use Exception;
class DepositoLevantamento
{
    protected $funcoes;
    protected $conexao;
    protected $commits = [];

    public function __construct($conexao, $funcoes)
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

   public function verDetalhes($pid){

        $r = [];

        $query=$this->conexao->prepare("SELECT *, pid AS identificador FROM transacao WHERE pid = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $resPrincipal = $query->fetch(\PDO::FETCH_ASSOC);


        if($resPrincipal["tipo"] == "recorrente"){

            $query=$this->conexao->prepare("SELECT * FROM recorrente WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            
            $trans = [];
            foreach(json_decode($res["transacao_pid"]) as $k => $v){
                $query=$this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid ");
                $query->bindValue(':pid', $pid);
                $query->execute();
                $tr = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($trans, $tr);
            }

            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes, "transacoes"=> $trans] );
            return ["ok"=>true, "payload"=> $r];

        }
        if($resPrincipal["tipo"] == "parcelado"){
            
            $query=$this->conexao->prepare("SELECT * FROM parcelado WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));

            $trans = [];
            foreach(json_decode($res["transacao_pid"]) as $k => $v){
                $query=$this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid ");
                $query->bindValue(':pid', $pid);
                $query->execute();
                $tr = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($trans, $tr);
            }

            $r = array_merge($resPrincipal,$res, ["pagamentos"=>$transacoes, "transacoes"=> $trans] );
            return ["ok"=>true, "payload"=> $r];
        }

        return ["ok"=>true, "payload"=> $resPrincipal];
    }
    public function verTodos($agente,$mes, $ano){
        
        $query=$this->conexao->prepare("SELECT * FROM deposito WHERE agente = :agente AND mes = :mes AND ano = :ano");
        $query->bindValue(':agente', $agente);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $resDeposito = $query->fetchAll(\PDO::FETCH_ASSOC);
         foreach($resDeposito as $k => $v){
            $query=$this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $v["cliente_identificador"]);
            $query->execute();
            $cliente = $query->fetch(\PDO::FETCH_COLUMN);
            $resDeposito[$k]["cliente"] = $cliente;
            $resDeposito[$k]["tipo"] = "deposito";
        }

        $query=$this->conexao->prepare("SELECT * FROM levantamento WHERE agente = :agente AND mes = :mes AND ano = :ano");
        $query->bindValue(':agente', $agente);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $resLevantamento = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach($resLevantamento as $k => $v){
            $query=$this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $v["cliente_identificador"]);
            $query->execute();
            $cliente = $query->fetch(\PDO::FETCH_COLUMN);
            $resLevantamento[$k]["cliente"] = $cliente;
            $resLevantamento[$k]["tipo"] = "levantamento";
        }

        $res = array_merge($resDeposito, $resLevantamento);
        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_DESC, $res);

        return ["ok"=>true, "payload"=> $res];
    }
    public function verTodosInit($agente){
        $ano = date("Y");
        $mes = date("m");
        $r = [];
        $query=$this->conexao->prepare("SELECT ano FROM deposito WHERE agente = :agente GROUP BY ano");
        $query->bindValue(':agente', $agente);
        $query->execute();
        $anoDeposito = $query->fetchAll(\PDO::FETCH_COLUMN);

        $query=$this->conexao->prepare("SELECT ano FROM levantamento WHERE agente = :agente GROUP BY ano");
        $query->bindValue(':agente', $agente);
        $query->execute();
        $anoLevantamento = $query->fetchAll(\PDO::FETCH_COLUMN);
        $anos = array_unique(array_merge($anoDeposito,$anoLevantamento));
        //return $anos;
        foreach($anos as $k => $v){
            //echo $k;
            $query=$this->conexao->prepare("SELECT mes FROM deposito WHERE agente = :agente AND ano = :ano GROUP BY mes");
            $query->bindValue(':agente', $agente);
            $query->bindValue(':ano', $v);
            $query->execute();
            $mesDeposito = $query->fetchAll(\PDO::FETCH_COLUMN);

            $query=$this->conexao->prepare("SELECT mes FROM levantamento WHERE agente = :agente AND ano = :ano GROUP BY mes");
            $query->bindValue(':agente', $agente);
            $query->bindValue(':ano', $v);
            $query->execute();
            $mesLevantamento = $query->fetchAll(\PDO::FETCH_COLUMN);

            $meses = array_unique(array_merge($mesDeposito,$mesLevantamento));
            $r["datas"][$k]["ano"] = $v;
            $r["datas"][$k][$v] =  ($meses);//$meses;
            //var_dump($r);
        }

        $query=$this->conexao->prepare("SELECT * FROM deposito WHERE agente = :agente AND mes = :mes AND ano = :ano");
        $query->bindValue(':agente', $agente);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $resDeposito = $query->fetchAll(\PDO::FETCH_ASSOC);
         foreach($resDeposito as $k => $v){
            $query=$this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $v["cliente_identificador"]);
            $query->execute();
            $cliente = $query->fetch(\PDO::FETCH_COLUMN);
            $resDeposito[$k]["cliente"] = $cliente;
            $resDeposito[$k]["tipo"] = "deposito";
        }

        $query=$this->conexao->prepare("SELECT * FROM levantamento WHERE agente = :agente AND mes = :mes AND ano = :ano");
        $query->bindValue(':agente', $agente);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $resLevantamento = $query->fetchAll(\PDO::FETCH_ASSOC);
        foreach($resLevantamento as $k => $v){
            $query=$this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $v["cliente_identificador"]);
            $query->execute();
            $cliente = $query->fetch(\PDO::FETCH_COLUMN);
            $resLevantamento[$k]["cliente"] = $cliente;
            $resLevantamento[$k]["tipo"] = "levantamento";
        }

        $res = array_merge($resDeposito, $resLevantamento);
        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_DESC, $res);

        $r["atual"]["res"] = $res;
        $r["atual"]["mes"] = $mes;
        $r["atual"]["ano"] = $ano;

        return ["ok"=>true, "payload"=> $r];
    }    
    
}
