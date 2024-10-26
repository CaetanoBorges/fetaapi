<?php

namespace Classes;
use Exception;
class Receber
{
    protected $funcoes;
    protected $conexao;
    protected $commits = [];

    public function __construct($conexao, $funcoes)
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function contaBalancoTipo($telefone)
    {

        $query = $this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :tel");
        $query->bindValue(':tel', $telefone);
        $query->execute();
        $cliente_identificador = $query->fetch(\PDO::FETCH_COLUMN);

        $query = $this->conexao->prepare("SELECT empresa FROM cliente WHERE identificador = :id");
        $query->bindValue(':id', $cliente_identificador);
        $query->execute();
        $empresa = $query->fetch(\PDO::FETCH_COLUMN);

        if ($empresa) {
            $query = $this->conexao->prepare("SELECT balanco, identificador FROM empresa WHERE cliente_identificador = :id");
            $query->bindValue(':id', $cliente_identificador);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            return ["empresa" => $empresa, "identificador_conta" => $res["identificador"], "saldo" => $res["balanco"]];
        }
        if (!$empresa) {
            $query = $this->conexao->prepare("SELECT balanco, identificador FROM particular WHERE cliente_identificador = :id");
            $query->bindValue(':id', $cliente_identificador);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            return ["empresa" => $empresa, "identificador_conta" => $res["identificador"], "saldo" => $res["balanco"]];
        }
    }

    public function nova($de, $para, $tipo, $onde, $valor, $descricao, $opcoes = [], $executado = false)
    {

        $pid = $this->funcoes::chaveDB();
        $quando = date("d-m-Y h:i:s");
        
        $emissor = $this->contaBalancoTipo($de);
        if ($emissor["saldo"] < $valor) {
            throw new Exception(json_encode(["message" => "Saldo insuficiente", "ok" => false]));

            return;
        }
        if ($de == $para) {
            throw new Exception(json_encode(["message" => "Nao pode transferir para a mesma conta", "ok" => false]));

            return;
        }

        $receptor = $this->contaBalancoTipo($para);
        
        
        if ($tipo == "normal") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $this->poeExtrato($emissor["empresa"], $emissor["identificador_conta"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando, false);
            
            return;
        }

        if ($tipo == "recorrente") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando);
            $this->recorrente($pid, $de, $para, $valor, $opcoes["periodicidade"], $quando);
            $this->poeExtrato($emissor["empresa"], $emissor["identificador_conta"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando, false);
            
            return;
        }

        if ($tipo == "parcelado") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $opcoes["valor_parcelas"], $descricao, $quando);
            $this->parcelado($pid, $de, $para, $opcoes["parcelas"], $opcoes["valor_parcelas"], $valor, $opcoes["periodicidade"], $quando);
            $this->poeExtrato($emissor["empresa"], $emissor["identificador_conta"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["empresa"], $receptor["identificador_conta"], $pid, 1, $valor, $receptor["saldo"], $quando, false);
            
            return;
        }
    }
    public function transacao($contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado = true)
    {

        $queryTransacao = $this->conexao->prepare("INSERT INTO transacao (identificador_conta, pid, tipo, de, para, onde, valor, descricao, quando, dia, mes, ano, executado) VALUES (:conta, :pid, :tipo, :de, :para, :onde, :valor, :descricao, :quando, :dia, :mes, :ano, :executado)");
        $queryTransacao->bindValue(':conta', $contaEmissor);
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

        array_push($this->commits, $queryTransacao);
    }
    public function recorrente($pid, $de, $para, $valor, $periodicidade, $quando, $ativo = true)
    {

        $id = $this->funcoes->chaveDB();
        $queryRecorrente = $this->conexao->prepare("INSERT INTO recorrente (identificador, transacao_pid, de, para, valor, periodicidade, quando, dia, mes, ano, ativo) 
        VALUES (:id, :pid, :de, :para, :valor, :periodo, :quando,:dia, :mes, :ano, :ativo)");
        $queryRecorrente->bindValue(':id', $id);
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


        array_push($this->commits, $queryRecorrente);
    }
    public function parcelado($pid, $de, $para, $parcelas, $valor, $total, $periodicidade, $quando, $ativo = true)
    {
        $id = $this->funcoes->chaveDB();
        $queryParcelado = $this->conexao->prepare("INSERT INTO parcelado (identificador, transacao_pid, de, para, parcelas, valor_parcela, valor_total, periodicidade, quando, dia, mes, ano, ativo) 
        VALUES (:id, :pid,:de, :para, :parcelas, :valor, :total, :periodo, :quando,:dia, :mes, :ano, :ativo)");
        $queryParcelado->bindValue(':id', $id);
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


        array_push($this->commits, $queryParcelado);
    }
    public function poeExtrato($empresa, $conta, $pid, $entrada, $movimento, $balancoAtual, $quando, $enviar = true)
    {
        if($enviar){
            $balanco = $balancoAtual - $movimento;
        }
        
        if(!$enviar){
            $balanco = $balancoAtual + $movimento;
        }
        

        $queryExtrato = $this->conexao->prepare("INSERT INTO extrato (identificador_conta, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano) 
        VALUES (:conta, :pid, :entrada, :movimento, :balanco, :quando, :dia, :mes, :ano)");
        $queryExtrato->bindValue(':conta', $conta);
        $queryExtrato->bindValue(':pid', $pid);
        $queryExtrato->bindValue(':entrada', $entrada);
        $queryExtrato->bindValue(':movimento', $movimento);
        $queryExtrato->bindValue(':balanco', $balanco);
        $queryExtrato->bindValue(':quando', $quando);
        $queryExtrato->bindValue(':dia', date('d'));
        $queryExtrato->bindValue(':mes', date('m'));
        $queryExtrato->bindValue(':ano', date('Y'));

        $queryConta = null;

        if ($empresa) {
            $queryConta = $this->conexao->prepare("UPDATE empresa SET balanco = :balanco WHERE identificador = :conta");
            $queryConta->bindValue(':balanco', $balanco);
            $queryConta->bindValue(':conta', $conta);
        }

        if (!$empresa) {
            $queryConta = $this->conexao->prepare("UPDATE particular SET  balanco = :balanco WHERE identificador = :conta");
            $queryConta->bindValue(':balanco', $balanco);
            $queryConta->bindValue(':conta', $conta);
        }

        array_push($this->commits, $queryConta, $queryExtrato);
    }


    function commit()
    {
        try {

            $this->conexao->beginTransaction();
            foreach ($this->commits as $query) {
                $query->execute();
            }
            $this->conexao->commit();
            $this->commits = [];
            return json_encode(["message" => "Transacao concluida", "ok" => true]);
        } catch (\PDOException $e) {

            $this->conexao->rollBack();
            return json_encode(["message" => $e->getMessage(), "ok" => false]);

        }

        
    }
}
