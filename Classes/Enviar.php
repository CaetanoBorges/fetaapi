<?php

namespace Classes;

use Exception;

class Enviar
{
    protected $funcoes;
    protected $conexao;
    protected $commits = [];
    protected $pid = null;

    public function __construct($conexao, $funcoes)
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }
    public function verDetalhes($pid)
    {

        $r = [];

        $query = $this->conexao->prepare("SELECT * FROM transacao WHERE pid = :pid");
        $query->bindValue(':pid', $pid);
        $query->execute();
        $resPrincipal = $query->fetch(\PDO::FETCH_ASSOC);
        //var_dump($resPrincipal);

        if ($resPrincipal["tipo"] == "recorrente") {

            $query = $this->conexao->prepare("SELECT * FROM recorrente WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal, $res, ["pagamentos" => $transacoes]);
            return $r;
        }
        if ($resPrincipal["tipo"] == "parcelado") {

            $query = $this->conexao->prepare("SELECT * FROM parcelado WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $transacoes = count(json_decode($res["transacao_pid"]));
            $r = array_merge($resPrincipal, $res, ["pagamentos" => $transacoes]);
            return $r;
        }
        if ($resPrincipal["tipo"] == "deposito") {

            $query = $this->conexao->prepare("SELECT * FROM deposito WHERE transacao_pid LIKE '%$pid%'");
            //$query->bindValue(':pid', $pid);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $r = array_merge($resPrincipal, $res);
            return $r;
        }

        return $resPrincipal;
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

    public function nova($de, $para, $tipo, $onde, $valor, $descricao, $opcoes = [], $executado = true)
    {

        $pid = $this->funcoes::chaveDB();
        $this->pid = $pid;
        $quando = date("d-m-Y h:i:s A");

        $emissor = $this->contaBalancoTipo($de);

        if ($de == $para) {
            return (["payload" => "Não pode fazer operação para a mesma conta", "ok" => false]);
        }

        $receptor = $this->contaBalancoTipo($para);


        if ($tipo == "normal") {
            if ($emissor["saldo"] < $valor) {
                return (["payload" => "Saldo insuficiente", "ok" => false]);
            }
            $this->transacao($emissor["cliente_identificador"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            $this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["cliente_identificador"], $pid, 1, $valor, $receptor["saldo"], $quando, false);
            return (["ok" => true]);
        }

        if ($tipo == "recorrente") {
            if ($emissor["saldo"] < $valor) {
                return (["payload" => "Saldo insuficiente", "ok" => false]);
            }
            $this->transacao($emissor["cliente_identificador"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando);
            $this->recorrente($pid, $de, $para, $valor, $opcoes["periodicidade"], $quando);
            $this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["cliente_identificador"], $pid, 1, $valor, $receptor["saldo"], $quando, false);

            return (["ok" => true]);
        }

        if ($tipo == "parcelado") {
            if ($emissor["saldo"] < $opcoes["valor_parcelas"]) {
                return (["payload" => "Saldo insuficiente", "ok" => false]);
            }
            $this->transacao($emissor["cliente_identificador"], $pid, $de, $para, $tipo, $onde, $opcoes["valor_parcelas"], $descricao, $quando);
            $this->parcelado($pid, $de, $para, $opcoes["parcelas"], $opcoes["valor_parcelas"], $valor, $opcoes["periodicidade"], $quando);
            $this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->poeExtrato($receptor["cliente_identificador"], $pid, 1, $valor, $receptor["saldo"], $quando, false);

            return (["ok" => true]);
        }
        if ($tipo == "deposito") {
           /*  if ($emissor["saldo"] < $valor) {
                return (["payload" => "Saldo insuficiente", "ok" => false]);
            } */
            $this->transacao($emissor["cliente_identificador"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            #$this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->deposito($pid, $emissor["cliente_identificador"], $receptor["cliente_identificador"], $valor, $quando);
            $this->poeExtrato($receptor["cliente_identificador"], $pid, 1, $valor, $receptor["saldo"], $quando, false);
            return (["ok" => true]);
        }
        if ($tipo == "levantamento") {
            if ($receptor["saldo"] < $valor) {
                return (["payload" => "Saldo insuficiente", "ok" => false]);
            } 
            $this->transacao($emissor["cliente_identificador"], $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado);
            #$this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $valor, $emissor["saldo"], $quando);
            $this->levantamento($pid, $emissor["cliente_identificador"], $receptor["cliente_identificador"], $valor, $quando);
            $this->poeExtrato($receptor["cliente_identificador"], $pid, 0, $valor, $receptor["saldo"], $quando, true);
            return (["ok" => true]);
        }
    }
    public function transacao($contaEmissor, $pid, $de, $para, $tipo, $onde, $valor, $descricao, $quando, $executado = true)
    {

        $queryTransacao = $this->conexao->prepare("INSERT INTO transacao (cliente_identificador, pid, tipo, de, para, onde, valor, descricao, quando, dia, mes, ano, executado) VALUES (:conta, :pid, :tipo, :de, :para, :onde, :valor, :descricao, :quando, :dia, :mes, :ano, :executado)");
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
    public function deposito($pid, $de, $para, $total, $quando)
    {
        $id = $this->funcoes->chaveDB();
        $queryDeposito = $this->conexao->prepare("INSERT INTO deposito (identificador, cliente_identificador, transacao_pid, agente, total, quando, dia, mes, ano) 
        VALUES (:id, :para, :pid, :de, :total, :quando, :dia, :mes, :ano)");
        $queryDeposito->bindValue(':id', $id);
        $queryDeposito->bindValue(':para', $para);
        $queryDeposito->bindValue(':pid', $pid);
        $queryDeposito->bindValue(':de', $de);
        $queryDeposito->bindValue(':total', $total);
        $queryDeposito->bindValue(':quando', $quando);
        $queryDeposito->bindValue(':dia', date('d'));
        $queryDeposito->bindValue(':mes', date('m'));
        $queryDeposito->bindValue(':ano', date('Y'));

        array_push($this->commits, $queryDeposito);
    }
    public function levantamento($pid, $de, $para, $total, $quando)
    {
        $id = $this->funcoes->chaveDB();
        $queryLevantar = $this->conexao->prepare("INSERT INTO levantamento (identificador, cliente_identificador, transacao_pid, agente, total, quando, dia, mes, ano) 
        VALUES (:id, :para, :pid, :de, :total, :quando, :dia, :mes, :ano)");
        $queryLevantar->bindValue(':id', $id);
        $queryLevantar->bindValue(':para', $para);
        $queryLevantar->bindValue(':pid', $pid);
        $queryLevantar->bindValue(':de', $de);
        $queryLevantar->bindValue(':total', $total);
        $queryLevantar->bindValue(':quando', $quando);
        $queryLevantar->bindValue(':dia', date('d'));
        $queryLevantar->bindValue(':mes', date('m'));
        $queryLevantar->bindValue(':ano', date('Y'));

        array_push($this->commits, $queryLevantar);
    }
    public function poeExtrato($conta, $pid, $entrada, $movimento, $balancoAtual, $quando, $enviar = true)
    {
        if ($enviar) {
            $balanco = $balancoAtual - $movimento;
        }

        if (!$enviar) {
            $balanco = $balancoAtual + $movimento;
        }


        $queryExtrato = $this->conexao->prepare("INSERT INTO extrato (cliente_identificador, transacao_pid, entrada, movimento, balanco, quando, dia, mes, ano) 
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

        $queryConta = $this->conexao->prepare("UPDATE cliente SET balanco = :balanco WHERE identificador = :conta");
        $queryConta->bindValue(':balanco', $balanco);
        $queryConta->bindValue(':conta', $conta);

        array_push($this->commits, $queryConta, $queryExtrato);
    }


    function commit()
    {
        try {
            $pid = $this->pid;
            $this->pid = null;
            $this->conexao->beginTransaction();
            foreach ($this->commits as $query) {
                $query->execute();
            }
            $this->conexao->commit();
            $this->commits = [];
            return (["payload" => "Transação concluida com sucesso", "ok" => true, "pid" => $pid]);
        } catch (\PDOException $e) {

            $this->conexao->rollBack();
            return (["payload" => "Erro inexperado, verifique os dados da operação", "ok" => false]);
        }
    }

    public function aceitarPendente($pid)
    {

        $transacao = $this->verDetalhes($pid);
        $emissor = $this->contaBalancoTipo($transacao["de"]);
        $receptor = $this->contaBalancoTipo($transacao["para"]);

        if ($transacao["executado"]) {
            return ["payload" => "Esta transação nao está pendente", "ok" => false];
        }

        if ($transacao["tipo"] == "normal") {
            if ($emissor["saldo"] < $transacao["valor"]) {
                return ["payload" => "Saldo insuficiente", "ok" => false];
            }
            $queryNormal = $this->conexao->prepare("UPDATE transacao SET executado = 1 WHERE pid = :pid");
            $queryNormal->bindValue(':pid', $pid);

            array_push($this->commits, $queryNormal);
        }

        if ($transacao["tipo"] == "recorrente") {
            if ($emissor["saldo"] < $transacao["valor"]) {
                return ["payload" => "Saldo insuficiente", "ok" => false];
            }
            $queryNormal = $this->conexao->prepare("UPDATE transacao SET executado = 1 WHERE pid = :pid");
            $queryNormal->bindValue(':pid', $pid);

            $queryRecorrente = $this->conexao->prepare("UPDATE recorrente SET ativo = 1 WHERE identificador = :id");
            $queryRecorrente->bindValue(':id', $transacao["identificador"]);

            array_push($this->commits, $queryNormal, $queryRecorrente);
        }
        if ($transacao["tipo"] == "parcelado") {
            if ($emissor["saldo"] < $transacao["valor_parcelas"]) {
                return ["payload" => "Saldo insuficiente", "ok" => false];
            }
            $queryNormal = $this->conexao->prepare("UPDATE transacao SET executado = 1 WHERE pid = :pid");
            $queryNormal->bindValue(':pid', $pid);

            $queryParcelado = $this->conexao->prepare("UPDATE parcelado SET ativo = 1 WHERE identificador = :id");
            $queryParcelado->bindValue(':id', $transacao["identificador"]);
            array_push($this->commits, $queryNormal, $queryParcelado);
        }

        $this->poeExtrato($emissor["cliente_identificador"], $pid, 0, $transacao["valor"], $emissor["saldo"], $transacao["quando"]);
        $this->poeExtrato($receptor["cliente_identificador"], $pid, 1, $transacao["valor"], $receptor["saldo"], $transacao["quando"], false);

        return ["ok" => true];
    }

    public function autoPayParcelado()
    {

        $query = $this->conexao->prepare("SELECT * FROM parcelado WHERE ativo = :ativo");
        $query->bindValue(':ativo', '1');
        $query->execute();
        $todos = $query->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($todos as $k => $v) {
            $query = $this->conexao->prepare("SELECT * FROM parcelado WHERE identificador = :pid");
            $query->bindValue(':pid', $v["identificador"]);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $parcelas_pagas = (array) json_decode($res["transacao_pid"]);
            if (count($parcelas_pagas) >= $res["parcelas"]) {
                continue;
            }

            if (count($parcelas_pagas) < $res["parcelas"]) {
                $executar = $this->nova($res["de"], $res["para"], "normal", "system", $res["valor_parcela"], "Pagamento automatico parcelado");
                if ($executar["ok"]) {
                    $commit = $this->commit();
                    if ($commit["ok"]) {
                        array_push($parcelas_pagas, $commit["pid"]);
                        $query = $this->conexao->prepare("UPDATE parcelado SET transacao_pid = :transacao_pid WHERE identificador = :pid");
                        $query->bindValue(":transacao_pid", json_encode($parcelas_pagas));
                        $query->bindValue(':pid', $res["identificador"]);
                        $query->execute();
                    }
                }
            }

            if ((count($parcelas_pagas) + 1) >= $res["parcelas"]) {
                $query = $this->conexao->prepare("UPDATE parcelado SET ativo = :ativo WHERE identificador = :pid");
                $query->bindValue(':pid', $res["identificador"]);
                $query->bindValue(':ativo', '0');
                $query->execute();
            }
        }
    }

    public function autoPayRecorrente()
    {

        $query = $this->conexao->prepare("SELECT * FROM recorrente WHERE ativo = :ativo");
        $query->bindValue(':ativo', '1');
        $query->execute();
        $todos = $query->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($todos as $k => $v) {
            $query = $this->conexao->prepare("SELECT * FROM recorrente WHERE identificador = :pid");
            $query->bindValue(':pid', $v["identificador"]);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $parcelas_pagas = (array) json_decode($res["transacao_pid"]);
            //var_dump($k);
            $executar = $this->nova($res["de"], $res["para"], "normal", "system", $res["valor"], "Pagamento automatico recorrente");
            if ($executar["ok"]) {
                $commit = $this->commit();
                if ($commit["ok"]) {
                    array_push($parcelas_pagas, $commit["pid"]);
                    $query = $this->conexao->prepare("UPDATE recorrente SET transacao_pid = :transacao_pid WHERE identificador = :pid");
                    $query->bindValue(":transacao_pid", json_encode($parcelas_pagas));
                    $query->bindValue(':pid', $res["identificador"]);
                    $query->execute();
                }
            }
        }
    }
}
