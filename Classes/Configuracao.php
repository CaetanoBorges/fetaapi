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
        return ["ok"=>true, "payload"=>'Tempo de bloqueio alterado'];
    }
    
    public function alteraPin($id_cliente, $pin,$telefone){
        $query=$this->conexao->prepare("UPDATE configuracao SET pin = :pin WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':pin', $this->funcoes::fazHash($pin));
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        $mensagem = "ALERTA: O seu pin foi atualizado! \n  \n  \n  \n É FETA, É FACIL.";
        $this->funcoes::enviaSMS($telefone, $mensagem);
        return ["ok"=>true, "payload"=>''];
    }
    public function verPin($id_cliente){
        $query=$this->conexao->prepare("SELECT pin FROM configuracao WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_COLUMN);
        return ["ok"=>true, "payload"=>$res];
    }
    public function verTempoBloqueio($id_cliente){
        $query=$this->conexao->prepare("SELECT tempo_bloqueio FROM configuracao WHERE cliente_identificador = :cliente_identificador");
        $query->bindValue(':cliente_identificador', $id_cliente);
        $query->execute();
        $res = $query->fetch(\PDO::FETCH_COLUMN);
        return ["ok"=>true, "payload"=>$res];
    }

    
}