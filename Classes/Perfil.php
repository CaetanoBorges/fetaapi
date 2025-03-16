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
        
        $query = $this->conexao->prepare("SELECT tempo_bloqueio FROM configuracao WHERE cliente_identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $tempo_bloqueio = $query->fetch(\PDO::FETCH_COLUMN);

        $query = $this->conexao->prepare("SELECT identificador, tipo, nome, balanco FROM cliente WHERE identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_ASSOC);
        $res["telefone"] = ($telefone);
        $res["bloqueio"] = ($tempo_bloqueio);
        $res["transacoes"] = $this->initTransacoes($res["identificador"],$telefone);
        return ["ok"=>true, "payload"=> $res];
    }


    public function verDetalhes($id_cliente)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        $query = $this->conexao->prepare("SELECT telefone FROM contacto WHERE cliente_identificador = :cliente");
        $query->bindValue(':cliente', $id_cliente);
        $query->execute();
        $telefone = $query->fetch(\PDO::FETCH_COLUMN);


        $query = $this->conexao->prepare("SELECT *, bi AS id_doc FROM cliente WHERE identificador = :cliente");
            $query->bindValue(':cliente', $id_cliente);
            $query->execute();
            $res = $query->fetch(\PDO::FETCH_ASSOC);
            $res["empresa"] = 0;
            $res["telefone"] = $telefone;
            return ["ok"=>true, "payload"=> $res];
    }

    function initTransacoes($identificador_conta,$conta)
    {
        $query = $this->conexao->prepare("SELECT identificador, quando, movimento as valor, entrada FROM extrato WHERE cliente_identificador = :id ORDER BY identificador DESC LIMIT 6");
        $query->bindValue(':id', $identificador_conta);
        $query->execute();
        $res = $query->fetchAll(\PDO::FETCH_ASSOC);

        array_multisort(array_map(function($element) {
            return $element['identificador'];
        }, $res), SORT_DESC, $res);
        
        return ["ok"=>true, "payload"=> $res];
    }
}
