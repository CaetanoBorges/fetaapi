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
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $query = $this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $telefone = $query->fetch(\PDO::FETCH_COLUMN);

        $query = $this->conexao->prepare("SELECT empresa FROM cliente WHERE identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $empresa = $query->fetch(\PDO::FETCH_COLUMN);

        if ($empresa) {
            $query = $this->conexao->prepare("SELECT identificador, nome, balanco FROM empresa WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["telefone"] = ($telefone);
            $res["transacoes"] = $this->initTransacoes($res["identificador"],$telefone);
            return ["ok"=>true, "payload"=> $res];
        }

        if (!$empresa) {
            $query = $this->conexao->prepare("SELECT identificador, nome, balanco FROM particular WHERE cliente_identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["telefone"] = ($telefone);
            $res["transacoes"] = $this->initTransacoes($res["identificador"],$telefone);
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

    function initTransacoes($identificador_conta,$conta)
    {
        $query = $this->conexao->prepare("SELECT identificador, quando, movimento as valor, entrada FROM extrato WHERE identificador_conta = :id ORDER BY identificador DESC LIMIT 6");
        $query->bindValue(':id', $identificador_conta);
        $query->execute();
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        array_multisort(array_map(function($element) {
            return $element['identificador'];
        }, $res), SORT_DESC, $res);
        
        return ["ok"=>true, "payload"=> $res];
    }
}
