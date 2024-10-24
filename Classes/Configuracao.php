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

    public function setTimeOut(){

    }
    
    public function alteraPin($id){

    }
    public function verPin(){

    }
    
}