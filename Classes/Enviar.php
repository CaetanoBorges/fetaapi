<?php 
namespace Classes;

class Enviar {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }
    public function verificaSaldo($empresa, $conta, $valor){
        if($empresa){
            $query=$this->conexao->prepare("SELECT balanco FROM empresa WHERE identificador = :conta");
            $query->bindValue(':conta', $conta);
            $query->execute();
            $saldo = $query->fetch(\PDO::FETCH_COLUMN);
            if($saldo <= $valor){
                return ["ok"=>false, "saldo"=>$saldo];
            }
            return ["ok"=>true, "saldo"=>$saldo];
        }
        
        if(!$empresa){
            $query=$this->conexao->prepare("SELECT balanco FROM particular WHERE identificador = :conta");
            $query->bindValue(':conta', $conta);
            $query->execute();
            $saldo = $query->fetch(\PDO::FETCH_COLUMN);
            if($saldo <= $valor){
                return ["ok"=>false, "saldo"=>$saldo];
            }
            return ["ok"=>true, "saldo"=>$saldo];
        }      
    }
    public function pegaReceptor($telefone){

        $query=$this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :tel");
        $query->bindValue(':tel', $telefone);
        $query->execute();
        $cliente_identificador = $query->fetch(\PDO::FETCH_COLUMN);

        $query=$this->conexao->prepare("SELECT empresa FROM cliente WHERE identificador = :id");
        $query->bindValue(':id', $cliente_identificador);
        $query->execute();
        $empresa = $query->fetch(\PDO::FETCH_COLUMN);

        if($empresa){
            $query=$this->conexao->prepare("SELECT balanco, identificador FROM empresa WHERE cliente_identificador = :id");
            $query->bindValue(':id', $cliente_identificador);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            return ["empresa"=>$empresa, "identificador_conta"=>$res["identificador"],"saldo"=>$res["balanco"]];
            
        }
        if(!$empresa){
            $query=$this->conexao->prepare("SELECT balanco, identificador FROM particular WHERE cliente_identificador = :id");
            $query->bindValue(':id', $cliente_identificador);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            return ["empresa"=>$empresa, "identificador_conta"=>$res["identificador"],"saldo"=>$res["balanco"]];
        }   

    }
    
    public function nova($empresaEmissor, $contaEmissor, $de, $para, $tipo, $onde, $valor, $descricao, $executado=false){

        $pid = $this->funcoes::chaveDB();
        $quando = date("d-m-Y h:i:s");

        $ok = $this->verificaSaldo($empresa, $conta, $valor);
        if(!$ok["ok"]){
            return;
        }

        $receptor = $this->pegaReceptor($para);

        if($tipo=="normal"){
            $queryTransacao=$this->transacao($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $extratoEmissor=poeExtrato($empresaEmissor, $contaEmissor, $pid, 0, $valor, $ok["saldo"], $quando);
            $extratoReceptor=poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando);
            try {
                $this->conexao->beginTransaction();
                $queryTransacao->execute();
                $extratoEmissor["atualizarConta"]->execute();
                $extratoEmissor["atualizarExtrato"]->execute();
                $extratoReceptor["atualizarConta"]->execute();
                $extratoReceptor["atualizarExtrato"]->execute();
                $this->conexao->commit();
            } catch (\PDOException $e) {
                $this->conexao->rollBack();
                return ["message"=>$e->getMessage(),"ok"=>false];
            }
            return ["message"=>"Transacao bem sucedida","ok"=>true];
        }

        if($tipo=="recorrente"){
            $queryTransacao=$this->transacao($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $queryRecorrente=$this->recorrente($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $extratoEmissor=poeExtrato($empresaEmissor, $contaEmissor, $pid, 0, $valor, $ok["saldo"], $quando);
            $extratoReceptor=poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando);
            try {
                $this->conexao->beginTransaction();
                $queryTransacao->execute();
                $queryRecorrente->execute();
                $extratoEmissor["atualizarConta"]->execute();
                $extratoEmissor["atualizarExtrato"]->execute();
                $extratoReceptor["atualizarConta"]->execute();
                $extratoReceptor["atualizarExtrato"]->execute();
                $this->conexao->commit();
            } catch (\PDOException $e) {
                $this->conexao->rollBack();
                return ["message"=>$e->getMessage(),"ok"=>false];
            }
            return ["message"=>"Transacao bem sucedida","ok"=>true];
        }

        if($tipo=="parcelado"){
            $queryTransacao=$this->transacao($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $queryParcelado=$this->parcelado($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $extratoEmissor=poeExtrato($empresaEmissor, $contaEmissor, $pid, 0, $valor, $ok["saldo"], $quando);
            $extratoReceptor=poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando);
            try {
                $this->conexao->beginTransaction();
                $queryTransacao->execute();
                $queryParcelado->execute();
                $extratoEmissor["atualizarConta"]->execute();
                $extratoEmissor["atualizarExtrato"]->execute();
                $extratoReceptor["atualizarConta"]->execute();
                $extratoReceptor["atualizarExtrato"]->execute();
                $this->conexao->commit();
            } catch (\PDOException $e) {
                $this->conexao->rollBack();
                return ["message"=>$e->getMessage(),"ok"=>false];
            }
            return ["message"=>"Transacao bem sucedida","ok"=>true];
        }

    }
     public function transacao($empresaEmissor, $contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado=false){
        
        $queryTransacao=$this->conexao->prepare("INSERT INTO transacao (identificador_conta, pid, tipo, de, para, onde, valor, descricao, quando, dia, mes, ano, executado) VALUES (identificador_conta = :conta, pid= :pid, tipo = :tipo, de = :de, para = :para, onde = :onde, valor = :valor, descricao = :descricao, quando = :quando, dia = :dia, mes = :mes, ano = :ano, executado = :executado)");
        $queryTransacao->bindValue(':conta', $conta);
        $queryTransacao->bindValue(':pid', $pid);
        $queryTransacao->bindValue(':tipo', $tipo);
        $queryTransacao->bindValue(':de', $de);
        $queryTransacao->bindValue(':para', $para);
        $queryTransacao->bindValue(':onde', $onde);
        $queryTransacao->bindValue(':valor', $valor);
        $queryTransacao->bindValue(':descricao', $this->funcoes::rm_special_chars($descricao));
        $queryTransacao->bindValue(':quando', $quando);
        $queryTransacao->bindValue(':dia', date('d'));
        $queryTransacao->bindValue(':mes', date('m'));
        $queryTransacao->bindValue(':ano', date('Y'));
        $queryTransacao->bindValue(':executado', $executado);

        return $queryTransacao;

    }
     public function recorrente($contaEmissor, $pid, $de, $para, $valor, $periodicidade, $quando, $ativo=true){
        
        $queryRecorrente=$this->conexao->prepare("INSERT INTO recorrente (identificador, transacao_pid, de, para, valor, periodicidade, quando, dia, mes, ano, ativo) 
        VALUES (identificador = :conta, transacao_pid= :pid, de = :de, para = :para, valor = :valor, periodicidade = :periodo, quando = :quando, dia = :dia, mes = :mes, ano = :ano, ativo = :ativo)");
        $queryRecorrente->bindValue(':conta', $contaEmissor);
        $queryRecorrente->bindValue(':pid', json_encode([$pid]));
        $queryRecorrente->bindValue(':de', $de);
        $queryRecorrente->bindValue(':para', $para);
        $queryRecorrente->bindValue(':valor', $valor);
        $queryRecorrente->bindValue(':periodo', $periodicidade);
        $queryRecorrente->bindValue(':quando', $quando);
        $queryRecorrente->bindValue(':dia', date('d'));
        $queryRecorrente->bindValue(':mes', date('m'));
        $queryRecorrente->bindValue(':ano', date('Y'));
        $queryRecorrente->bindValue(':ativo', $ativo);

        return $queryRecorrente;

    }
     public function parcelado($contaEmissor, $pid, $de, $para, $parcelas, $valor, $total, $periodicidade, $quando, $ativo=true){
        
        $queryParcelado=$this->conexao->prepare("INSERT INTO parcelado (identificador, transacao_pid, de, para, parcelas, valor_parcela, valor_total, periodicidade, quando, dia, mes, ano, ativo) 
        VALUES (identificador = :conta, transacao_pid= :pid, de = :de, para = :para, parcelas=:parcelas, valor_parcelas = :valor, valor_total = :total, periodicidade = :periodo, quando = :quando, dia = :dia, mes = :mes, ano = :ano, ativo = :ativo)");
        $queryParcelado->bindValue(':conta', $contaEmissor);
        $queryParcelado->bindValue(':pid', json_encode([$pid]));
        $queryParcelado->bindValue(':de', $de);
        $queryParcelado->bindValue(':para', $para);
        $queryParcelado->bindValue(':parcelas', $parcelas);
        $queryParcelado->bindValue(':valor', $valor);
        $queryParcelado->bindValue(':total', $total);
        $queryParcelado->bindValue(':periodo', $periodicidade);
        $queryParcelado->bindValue(':quando', $quando);
        $queryParcelado->bindValue(':dia', date('d'));
        $queryParcelado->bindValue(':mes', date('m'));
        $queryParcelado->bindValue(':ano', date('Y'));
        $queryParcelado->bindValue(':ativo', $ativo);

        return $queryParcelado;

    }
    public function poeExtrato($empresa, $conta, $pid, $entrada, $movimento, $balancoAtual, $quando){

        $balanco = $balancoAtual - $movimento;

        $queryExtrato=$this->conexao->prepare("INSERT INTO extrato (identificador_conta, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano) 
        VALUES (identificador_conta = :conta, transacao_pid= :pid, entrada = :entrada, movimento = :movimento, balanco = :balanco, quando = :quando, dia = :dia, mes = :mes, ano = :ano)");
        $queryExtrato->bindValue(':conta', $conta);
        $queryExtrato->bindValue(':pid', $pid);
        $queryExtrato->bindValue(':entrada', $entrada);
        $queryExtrato->bindValue(':movimento', $movimento);
        $queryExtrato->bindValue(':balanco', $balanco);
        $queryExtrato->bindValue(':quando', $quando);
        $queryExtrato->bindValue(':dia', date('d'));
        $queryExtrato->bindValue(':mes', date('m'));
        $queryExtrato->bindValue(':ano', date('Y'));

        $queryConta=null;

         if($empresa){
            $queryConta=$this->conexao->prepare("UPDATE empresa SET balanco = :balanco WHERE identificador = :conta");
            $queryConta->bindValue(':balanco', $balanco);
            $queryConta->bindValue(':conta', $conta);
        }
        
        if(!$empresa){
            $queryConta=$this->conexao->prepare("UPDATE particular SET  balanco = :balanco WHERE identificador = :conta");
            $queryConta->bindValue(':balanco', $balanco);
            $queryConta->bindValue(':conta', $conta);
        } 

        return ["atualizarConta"=>$queryConta,"atualizarExtrato"=>$queryExtrato];

    } 
}