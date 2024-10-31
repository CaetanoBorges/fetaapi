<?php 
namespace Classes;

class Estatistica {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function verTodos($conta,$mes, $ano){
       
        $dados = [];
        $query=$this->conexao->prepare("SELECT dia FROM extrato WHERE identificador_conta = :conta AND mes = :mes AND ano = :ano GROUP BY dia");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $dias = $query->fetchAll(\PDO::FETCH_COLUMN);
        
        $query=$this->conexao->prepare("SELECT SUM(movimento) AS total_entrada, COUNT(*) AS qtd_entrada FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND mes = :mes AND ano = :ano");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':entrada', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $entrada = $query->fetch(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT SUM(movimento) AS total_saida, COUNT(*) AS qtd_saida FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND mes = :mes AND ano = :ano");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':entrada', '0');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $saida = $query->fetch(\PDO::FETCH_ASSOC);
        
        $res = array_merge($entrada, $saida);

       

        foreach($dias as $k => $v){
            $query=$this->conexao->prepare("SELECT COUNT(movimento) AS entrada FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND dia = :dia AND mes = :mes AND ano = :ano");
            $query->bindValue(':conta', $conta);
            $query->bindValue(':entrada', '1');
            $query->bindValue(':dia', $v);
            $query->bindValue(':mes', $mes);
            $query->bindValue(':ano', $ano);
            $query->execute();
            $ent = $query->fetch(\PDO::FETCH_ASSOC);
            
            $query=$this->conexao->prepare("SELECT COUNT(movimento) AS saida FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND dia = :dia AND mes = :mes AND ano = :ano");
            $query->bindValue(':conta', $conta);
            $query->bindValue(':entrada', '0');
            $query->bindValue(':dia', $v);
            $query->bindValue(':mes', $mes);
            $query->bindValue(':ano', $ano);
            $query->execute();
            $sai = $query->fetch(\PDO::FETCH_ASSOC);

            array_push($dados,[(int) $ent["entrada"], (int) $sai["saida"], (int) $y]);
        }

        
        $res["dados"] = $dados;
        
        return ["ok"=>true, "payload"=>$res];
    }
    public function verTodosInit($conta){
        $ano = date("Y");
        $mes = date("m");
        $dados = [];
        $r = [];
        $query=$this->conexao->prepare("SELECT ano FROM extrato WHERE identificador_conta = :conta GROUP BY ano");
        $query->bindValue(':conta', $conta);
        $query->execute();
        $anos = $query->fetchAll(\PDO::FETCH_COLUMN);
        //return $anos;

        foreach($anos as $k => $v){
            //echo $k;
            $query=$this->conexao->prepare("SELECT mes FROM extrato WHERE identificador_conta = :conta AND ano = :ano GROUP BY mes");
            $query->bindValue(':conta', $conta);
            $query->bindValue(':ano', $v);
            $query->execute();
            $meses = $query->fetchAll(\PDO::FETCH_COLUMN);

            $r["datas"][$k]["ano"] = $v;
            $r["datas"][$k][$v] = array_unique($meses);
            //var_dump($r);
        }

        $query=$this->conexao->prepare("SELECT dia FROM extrato WHERE identificador_conta = :conta AND mes = :mes AND ano = :ano GROUP BY dia");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $dias = $query->fetchAll(\PDO::FETCH_COLUMN);
        
        $query=$this->conexao->prepare("SELECT SUM(movimento) AS total_entrada, COUNT(*) AS qtd_entrada FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND mes = :mes AND ano = :ano");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':entrada', '1');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $entrada = $query->fetch(\PDO::FETCH_ASSOC);
        
        $query=$this->conexao->prepare("SELECT SUM(movimento) AS total_saida, COUNT(*) AS qtd_saida FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND mes = :mes AND ano = :ano");
        $query->bindValue(':conta', $conta);
        $query->bindValue(':entrada', '0');
        $query->bindValue(':mes', $mes);
        $query->bindValue(':ano', $ano);
        $query->execute();
        $saida = $query->fetch(\PDO::FETCH_ASSOC);
        
        $res = array_merge($entrada, $saida);

       

        foreach($dias as $k => $v){
            $query=$this->conexao->prepare("SELECT COUNT(movimento) AS entrada FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND dia = :dia AND mes = :mes AND ano = :ano");
            $query->bindValue(':conta', $conta);
            $query->bindValue(':entrada', '1');
            $query->bindValue(':dia', $v);
            $query->bindValue(':mes', $mes);
            $query->bindValue(':ano', $ano);
            $query->execute();
            $ent = $query->fetch(\PDO::FETCH_ASSOC);
            
            $query=$this->conexao->prepare("SELECT COUNT(movimento) AS saida FROM extrato WHERE identificador_conta = :conta AND entrada = :entrada AND dia = :dia AND mes = :mes AND ano = :ano");
            $query->bindValue(':conta', $conta);
            $query->bindValue(':entrada', '0');
            $query->bindValue(':dia', $v);
            $query->bindValue(':mes', $mes);
            $query->bindValue(':ano', $ano);
            $query->execute();
            $sai = $query->fetch(\PDO::FETCH_ASSOC);

            array_push($dados,[(int) $ent["entrada"],(int) $sai["saida"], (int) $v]);
        }

        
        $res["dados"] = $dados;
        $r["atual"]["res"] = $res;
        $r["atual"]["mes"] = $mes;
        $r["atual"]["ano"] = $ano;

        return ["ok"=>true, "payload"=>$r];
    }
    
}