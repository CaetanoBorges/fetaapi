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
        
        $receptor = $this->contaBalancoTipo($de);
       
        if ($de == $para) {
            throw new Exception(json_encode(["message" => "Nao pode cobrar para a mesma conta", "ok" => false]));

            return;
        }

        $emissor = $this->contaBalancoTipo($para);
        
        
        if ($tipo == "normal") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            
            return;
        }

        if ($tipo == "recorrente") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando);
            $this->recorrente($pid, $de, $para, $valor, $opcoes["periodicidade"], $quando);
            return;
        }

        if ($tipo == "parcelado") {
            $this->transacao($emissor["identificador_conta"], $pid, $de, $para, $tipo, $onde, $opcoes["valor_parcelas"], $descricao, $quando);
            $this->parcelado($pid, $de, $para, $opcoes["parcelas"], $opcoes["valor_parcelas"], $valor, $opcoes["periodicidade"], $quando);
            
            return;
        }
    }
    public function transacao($contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado = false, $pedido = true)
    {

        $queryTransacao = $this->conexao->prepare("INSERT INTO transacao (identificador_conta, pid, tipo, de, para, onde, valor, descricao, quando, dia, mes, ano, executado, pedido) VALUES (:conta, :pid, :tipo, :de, :para, :onde, :valor, :descricao, :quando, :dia, :mes, :ano, :executado, :pedido)");
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
        $queryTransacao->bindValue(':pedido', $pedido);

        array_push($this->commits, $queryTransacao);
    }
    public function recorrente($pid, $de, $para, $valor, $periodicidade, $quando, $ativo = false)
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
    public function parcelado($pid, $de, $para, $parcelas, $valor, $total, $periodicidade, $quando, $ativo = false)
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
