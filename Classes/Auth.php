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

    /**
     * Verifica se existe um particular com um determinado numero de BI
     * @param string $bi
     * @return boolean
     */
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
    /**
     * Verifica se existe uma empresa com um determinado NIF
     * @param string $nif
     * @return boolean
     */
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
    /**
     * Verifica se existe um contacto com um determinado telefone
     * @param string $telefone
     * @return boolean
     */
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

    /**
     * Envia um código de confirmação para um determinado telefone
     * @param string $telefone
     * @return boolean
     */
    public function enviaCodigo($telefone){
        $this->funcoes::setRemetente('FETA-FACIL');
        $codigo = $this->funcoes::seisDigitos();
        $mensagem = "Caro cidadão, o seu código de confirmação para o cadastro é: $codigo";
        $this->funcoes::enviaSMS($telefone, $mensagem);

        $query=$this->conexao->prepare("INSERT INTO confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou) VALUES (:cliente, :acao, :codigo, :quando, :confirmou)");
        $query->bindValue(':cliente', $telefone);
        $query->bindValue(':acao', "cadastro");
        $query->bindValue(':codigo', $codigo);
        $query->bindValue(':quando', $this->funcoes::quando(time()));
        $query->bindValue(':confirmou', 0);
        $query->execute();

        return true;
    }

    public function cadastrarParticular()
    {
       
        
    }
    public function cadastrarEmpresa()
    {
       
        
    }

    public function verificaExistencia($dados)
    {
        if($this->existeTelefone($dados['telefone'])){
            return ["sms"=>"Este telefone ja existe numa conta","ok"=>true];
        }

        if($dados['comercial']){
            if($this->existeNif($dados['nif'])){
                return ["sms"=>"Este nif ja existe numa conta","ok"=>true];
            }
        }
        if(!$dados['comercial']){
            if($this->existeBi($dados['bi'])){
                return ["sms"=>"Este BI ja existe numa conta","ok"=>true];
            }
        }
        return ["sms"=>"Nao existe nenhuma conta com esses dados","ok"=>false];
    }

        /**
     * Verifica se o código de confirmação recebido é o mesmo que
     * foi enviado para um determinado telefone
     * @param string $telefone
     * @param string $codigo
     * @return array
     */
    public function verificaCodigo($dados){

        $query=$this->conexao->prepare("SELECT * FROM confirmar WHERE cliente_identificador = :cliente AND codigo_enviado = :codigo AND confirmou = :confirmou");
        $query->bindValue(':cliente', $dados['id']);
        $query->bindValue(':codigo', $dados['codigo']);
        $query->bindValue(':confirmou', 0);
        $query->execute();
        $res = $query->rowCount();

        if($query->rowCount() > 0){

            $query=$this->conexao->prepare("UPDATE confirmar SET confirmou = :confirmou WHERE cliente_identificador = :cliente AND codigo_enviado = :codigo");
            $query->bindValue(':cliente', $dados['id']);
            $query->bindValue(':codigo', $dados['codigo']);
            $query->bindValue(':confirmou', 1);
            $query->execute();
            return ["sms"=>"Verificacao completa","ok"=>true];

        }else{
            return ["sms"=>"Nao verificado","ok"=>false];
        }
    }
}