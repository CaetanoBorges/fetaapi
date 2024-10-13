<?php

namespace Classes;


class Auth
{
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao,$funcoes) 
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    protected function _check()
    {
        
        
    }

    public function existeBi($bi){
        $query=$this->conexao->prepare("SELECT * FROM particular WHERE bi = :bi");
        $query->bindValue(':bi', $bi);
        $query->execute();
        if($query->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }
    public function existeNif($nif){
        $query=$this->conexao->prepare("SELECT * FROM empresa WHERE nif = :nif");
        $query->bindValue(':nif', $nif);
        $query->execute();
        if($query->rowCount() > 0){
            return true;
        }else{
            return false;        
        }
    }
    public function existeTelefone($telefone){
        $query=$this->conexao->prepare("SELECT * FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $telefone);
        $query->execute();
        if($query->rowCount() > 0){
            return true;
        }else{
            return false;
        }
    }
    public function enviaCodigo($telefone){
        $this->funcoes::setRemetente('FETA-FACIL');
        $codigo = $this->funcoes::seisDigitos();
        $mensagem = "Caro cidadão, o seu código de confirmação para o cadastro é: $codigo";
        $this->funcoes::enviaSMS($telefone, $mensagem);

        $query=$this->conexao->prepare("INSERT INTO confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou) VALUES (:cliente, :acao, :codigo, :quando, :corfirmou)");
        $query->bindValue(':cliente', $telefone);
        $query->bindValue(':acao', "cadastro");
        $query->bindValue(':codigo', $codigo);
        $query->bindValue(':quando', $this->funcoes::quando(time()));
        $query->bindValue(':corfirmou', 0);
        $query->execute();

        return true;
    }
    public function verificaCodigo($telefone, $codigo){
        
    }

    public function cadastrarParticular()
    {
       
        
    }
    public function cadastrarEmpresa()
    {
       
        
    }
}