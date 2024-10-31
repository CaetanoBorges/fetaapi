<?php

namespace Classes;

class Perfil
{
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao, $funcoes)
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function init($id_cliente)
    {
        $query = $this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $telefone = $query->fetch(\PDO::FETCH_COLUMN);

        $query = $this->conexao->prepare("SELECT empresa FROM cliente WHERE identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $empresa = $query->fetch(\PDO::FETCH_COLUMN);

        if ($empresa) {
            $query = $this->conexao->prepare("SELECT nome, balanco FROM empresa WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["telefone"] = ($telefone);
            $res["transacoes"] = $this->initTransacoes($telefone);
            return ["ok"=>true, "payload"=> $res];
        }

        if (!$empresa) {
            $query = $this->conexao->prepare("SELECT nome, balanco FROM particular WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["telefone"] = ($telefone);
            $res["transacoes"] = $this->initTransacoes($telefone);

            return ["ok"=>true, "payload"=> $res];
        }
    }


    public function verDetalhes($id_cliente)
    {

        $query = $this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $telefone = $query->fetch(\PDO::FETCH_COLUMN);


        $query = $this->conexao->prepare("SELECT empresa FROM cliente WHERE identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $empresa = $query->fetch(\PDO::FETCH_COLUMN);

        if ($empresa) {
            $query = $this->conexao->prepare("SELECT *, nif AS id_doc, area_atuacao AS area FROM empresa WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["empresa"] = $empresa;
            $res["telefone"] = $telefone;
            return ["ok"=>true, "payload"=> $res];
        }

        if (!$empresa) {
            $query = $this->conexao->prepare("SELECT *, bi AS id_doc FROM particular WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["empresa"] = $empresa;
            $res["telefone"] = $telefone;
            return ["ok"=>true, "payload"=> $res];
        }
    }

    function initTransacoes($conta)
    {
        $query = $this->conexao->prepare("SELECT quando, descricao, valor, de FROM transacao WHERE executado = :executado AND para = :para LIMIT 3");
        $query->bindValue(':executado', '1');
        $query->bindValue(':para', $conta);
        $query->execute();
        $resUm = $query->fetchAll(\PDO::FETCH_ASSOC);

        $query = $this->conexao->prepare("SELECT quando, descricao, valor, de FROM transacao WHERE executado = :executado AND de = :de LIMIT 3");
        $query->bindValue(':executado', '1');
        $query->bindValue(':de', $conta);
        $query->execute();
        $resDois = $query->fetchAll(\PDO::FETCH_ASSOC);

        $res = array_merge($resUm, $resDois);
        foreach ($res as $k => $v) {
            if ($v["de"] == $conta) {
                $res[$k]["enviar"] = 1;
            } else {
                $res[$k]["enviar"] = 0;
            }
        }
        array_multisort(array_map(function($element) {
            return $element['quando'];
        }, $res), SORT_DESC, $res);
        return ["ok"=>true, "payload"=> $res];
    }
}
