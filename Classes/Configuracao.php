<?php 
namespace Classes;

class Configuracao {
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    public function setTimeOut($id_cliente, $tempo){
        $query=$this->conexao->prepare("UPDATE configuracao SET tempo_bloqueio = :tempo_bloqueio WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':tempo_bloqueio', $tempo);
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        return true;
    }
    
    public function alteraPin($id_cliente, $pin){
        $query=$this->conexao->prepare("UPDATE configuracao SET pin = :pin WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':pin', $pin);
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        return true; 
    }
    public function verPin($id_cliente){
        $query=$this->conexao->prepare("SELECT pin FROM configuracao WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_COLUMN);
        return $res;
    }
    
    public function verTempoBloqueio($id_cliente){
        $query=$this->conexao->prepare("SELECT tempo_bloqueio FROM configuracao WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_COLUMN);
        return $res;
    }
}