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

    public function cadastrarParticular($dados)
    {
        $identificador = $this->funcoes::chaveDB();
        $pin = $this->funcoes::fazHash($dados['pin']);
        
        $queryCliente=$this->conexao->prepare("INSERT INTO cliente (identificador, empresa) VALUES (:identificador, :empresa)");
        $queryCliente->bindValue(':identificador', $identificador);
        $queryCliente->bindValue(':empresa', "0");

        $queryEndereco=$this->conexao->prepare("INSERT INTO endereco (cliente_identificador, atual) VALUES (:cliente_identificador, :atual)");
        $queryEndereco->bindValue(':cliente_identificador', $identificador);
        $queryEndereco->bindValue(':atual', "1");
        
        $queryContacto=$this->conexao->prepare("INSERT INTO contacto (cliente_identificador, telefone, atual) VALUES (:cliente_identificador, :telefone, :atual)");
        $queryContacto->bindValue(':cliente_identificador', $identificador);
        $queryContacto->bindValue(':telefone', $dados['telefone']);
        $queryContacto->bindValue(':atual', "1");
        
        $queryConfiguracao=$this->conexao->prepare("INSERT INTO configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin) VALUES (:cliente_identificador, :tempo_bloqueio, :auto_pagamento_recebimento, :pin)");
        $queryConfiguracao->bindValue(':cliente_identificador', $identificador);
        $queryConfiguracao->bindValue(':tempo_bloqueio', "30");
        $queryConfiguracao->bindValue(':auto_pagamento_recebimento', "0");
        $queryConfiguracao->bindValue(':pin', $pin);

        $identificador_conta = $this->funcoes::chaveDB();
        $queryParticular=$this->conexao->prepare("INSERT INTO particular (identificador, cliente_identificador, bi, nome, genero, nascimento, balanco) VALUES (:identificador, :identificador_cliente, :bi, :nome, :genero, :nascimento, :balanco)");
        $queryParticular->bindValue(':identificador', $identificador_conta);
        $queryParticular->bindValue(':identificador_cliente', $identificador);
        $queryParticular->bindValue(':bi', $dados['bi']);
        $queryParticular->bindValue(':nome', $dados['nome']);
        $queryParticular->bindValue(':genero', $dados['genero']);
        $queryParticular->bindValue(':nascimento', $dados['nascimento']);
        $queryParticular->bindValue(':balanco', "0.00");

        try {
            $this->conexao->beginTransaction();
            $queryCliente->execute();
            $queryEndereco->execute();
            $queryContacto->execute();
            $queryConfiguracao->execute();
            $queryParticular->execute();
            $this->conexao->commit();
        } catch (\PDOException $e) {
            
            $this->conexao->rollBack();
            return ["sms"=>$e->getMessage(),"ok"=>false];
        }
        return ["sms"=>"Conta criada com sucesso","ok"=>true];
    }
    public function cadastrarEmpresa($dados)
    {

        $identificador = $this->funcoes::chaveDB();
        $pin = $this->funcoes::fazHash($dados['pin']);
        
        $queryCliente=$this->conexao->prepare("INSERT INTO cliente (identificador, empresa) VALUES (:identificador, :empresa)");
        $queryCliente->bindValue(':identificador', $identificador);
        $queryCliente->bindValue(':empresa', "1");

        $queryEndereco=$this->conexao->prepare("INSERT INTO endereco (cliente_identificador, atual) VALUES (:cliente_identificador, :atual)");
        $queryEndereco->bindValue(':cliente_identificador', $identificador);
        $queryEndereco->bindValue(':atual', "1");
        
        $queryContacto=$this->conexao->prepare("INSERT INTO contacto (cliente_identificador, telefone, atual) VALUES (:cliente_identificador, :telefone, :atual)");
        $queryContacto->bindValue(':cliente_identificador', $identificador);
        $queryContacto->bindValue(':telefone', $dados['telefone']);
        $queryContacto->bindValue(':atual', "1");
        
        $queryConfiguracao=$this->conexao->prepare("INSERT INTO configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin) VALUES (:cliente_identificador, :tempo_bloqueio, :auto_pagamento_recebimento, :pin)");
        $queryConfiguracao->bindValue(':cliente_identificador', $identificador);
        $queryConfiguracao->bindValue(':tempo_bloqueio', "30");
        $queryConfiguracao->bindValue(':auto_pagamento_recebimento', "0");
        $queryConfiguracao->bindValue(':pin', $pin);

        $identificador_conta = $this->funcoes::chaveDB();
        $query=$this->conexao->prepare("INSERT INTO empresa (identificador, cliente_identificador, nif, nome, area_atuacao, balanco) VALUES (:identificador, :identificador_cliente, :nif, :nome, :area, :balanco)");
        $query->bindValue(':identificador', $identificador_conta);
        $query->bindValue(':identificador_cliente', $identificador);
        $query->bindValue(':nif', $dados['nif']);
        $query->bindValue(':nome', $dados['nome']);
        $query->bindValue(':area', $dados['area']);
        $query->bindValue(':balanco', "0.00");



        try {

            $this->conexao->beginTransaction();
            $queryCliente->execute();
            $queryEndereco->execute();
            $queryContacto->execute();
            $queryConfiguracao->execute();
            $query->execute();
            $this->conexao->commit();

        } catch (\PDOException $e) {

            $this->conexao->rollBack();
            return ["sms"=>$e->getMessage(),"ok"=>false];

        }
        return ["sms"=>"Conta criada com sucesso","ok"=>true];
        
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