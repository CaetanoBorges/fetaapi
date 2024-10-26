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
        $query->bindValue(':quando', time());
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
        $queryContacto->bindValue(':telefone', $dados['id']);
        $queryContacto->bindValue(':atual', "1");
        
        $queryConfiguracao=$this->conexao->prepare("INSERT INTO configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin) VALUES (:cliente_identificador, :tempo_bloqueio, :auto_pagamento_recebimento, :pin)");
        $queryConfiguracao->bindValue(':cliente_identificador', $identificador);
        $queryConfiguracao->bindValue(':tempo_bloqueio', "30");
        $queryConfiguracao->bindValue(':auto_pagamento_recebimento', "0");
        $queryConfiguracao->bindValue(':pin', $pin);

        $identificador_conta = $this->funcoes::chaveDB();
        $queryParticular=$this->conexao->prepare("INSERT INTO particular (identificador, cliente_identificador, bi, nome, genero, nascimento, balanco, img) VALUES (:identificador, :identificador_cliente, :bi, :nome, :genero, :nascimento, :balanco, :img)");
        $queryParticular->bindValue(':identificador', $identificador_conta);
        $queryParticular->bindValue(':identificador_cliente', $identificador);
        $queryParticular->bindValue(':bi', $dados['bi']);
        $queryParticular->bindValue(':nome', $dados['nome']);
        $queryParticular->bindValue(':genero', $dados['genero']);
        $queryParticular->bindValue(':nascimento', $dados['nascimento']);
        $queryParticular->bindValue(':balanco', "0.00");
        $queryParticular->bindValue(':img', "default.png");

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
            return ["message"=>$e->getMessage(),"ok"=>false];
        }
        $this->funcoes::setRemetente('FETA-FACIL');
        $mensagem = "BEM-VINDO A FETA FACIL, agora pode: Receber pagamentos, fazer combranças, desfrutar de pagamentos online e muito mais. O MELHOR SISTEMA DE PAGAMENTOS DE ANGOLA! \n \n \n \n É FETA, É FACIL.";
        $this->funcoes::enviaSMS($dados["id"], $mensagem);
        return ["message"=>"Conta criada com sucesso","ok"=>true];
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
        $queryContacto->bindValue(':telefone', $dados['id']);
        $queryContacto->bindValue(':atual', "1");
        
        $queryConfiguracao=$this->conexao->prepare("INSERT INTO configuracao (cliente_identificador, tempo_bloqueio, auto_pagamento_recebimento, pin) VALUES (:cliente_identificador, :tempo_bloqueio, :auto_pagamento_recebimento, :pin)");
        $queryConfiguracao->bindValue(':cliente_identificador', $identificador);
        $queryConfiguracao->bindValue(':tempo_bloqueio', "30");
        $queryConfiguracao->bindValue(':auto_pagamento_recebimento', "0");
        $queryConfiguracao->bindValue(':pin', $pin);

        $identificador_conta = $this->funcoes::chaveDB();
        $query=$this->conexao->prepare("INSERT INTO empresa (identificador, cliente_identificador, nif, nome, area_atuacao, balanco, img) VALUES (:identificador, :identificador_cliente, :nif, :nome, :area, :balanco, :img)");
        $query->bindValue(':identificador', $identificador_conta);
        $query->bindValue(':identificador_cliente', $identificador);
        $query->bindValue(':nif', $dados['nif']);
        $query->bindValue(':nome', $dados['nome']);
        $query->bindValue(':area', $dados['area']);
        $query->bindValue(':balanco', "0.00");
        $query->bindValue(':img', "default.png");

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
            return ["message"=>$e->getMessage(),"ok"=>false];

        }
        $mensagem = "BEM-VINDO A FETA FACIL, agora pode: Receber pagamentos, fazer combranças, desfrutar de pagamentos online e muito mais. O MELHOR SISTEMA DE PAGAMENTOS DE ANGOLA! \n \n \n \n É FETA, É FACIL.";
        $this->funcoes::enviaSMS($dados["id"], $mensagem);
        return ["message"=>"Conta criada com sucesso","ok"=>true];
        
    }

    public function verificaExistencia($dados)
    {
        if($this->existeTelefone($dados['id'])){
            return ["message"=>"Este telefone ja existe numa conta","ok"=>true];
        }

        if($dados['comercial']){
            if($this->existeNif($dados['nif'])){
                return ["message"=>"Este nif ja existe numa conta","ok"=>true];
            }
        }
        if(!$dados['comercial']){
            if($this->existeBi($dados['bi'])){
                return ["message"=>"Este BI ja existe numa conta","ok"=>true];
            }
        }
        return ["message"=>"Nao existe nenhuma conta com esses dados","ok"=>false];
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

        if($query->rowCount() > 0){

            $query=$this->conexao->prepare("UPDATE confirmar SET confirmou = :confirmou WHERE cliente_identificador = :cliente AND codigo_enviado = :codigo");
            $query->bindValue(':cliente', $dados['id']);
            $query->bindValue(':codigo', $dados['codigo']);
            $query->bindValue(':confirmou', 1);
            $query->execute();
            return ["message"=>"Verificacao completa","ok"=>true];

        }else{
            return ["message"=>"Nao verificado","ok"=>false];
        }
    }

    

    public function Entrar($dados){
        $res = [];
        $query=$this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :telefone AND atual = :atual");
        $query->bindValue(':telefone', $dados['id']);
        $query->bindValue(':atual', "1");
        $query->execute();

        if($query->rowCount() > 0){

            $identificador_cliente = $query->fetch(\PDO::FETCH_ASSOC);

            $query=$this->conexao->prepare("SELECT * FROM cliente WHERE identificador = :identificador");
            $query->bindValue(':identificador', $identificador_cliente["cliente_identificador"]);
            $query->execute();
            $tipo = $query->fetch(\PDO::FETCH_ASSOC);

            if($tipo["empresa"]){
                $query=$this->conexao->prepare("SELECT identificador FROM empresa WHERE cliente_identificador = :identificador");
                $query->bindValue(':identificador', $identificador_cliente["cliente_identificador"]);
                $query->execute();
                $r = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($res,["conta"=>$r["identificador"]]);
            }else{  
                $query=$this->conexao->prepare("SELECT identificador FROM particular WHERE cliente_identificador = :identificador");
                $query->bindValue(':identificador', $identificador_cliente["cliente_identificador"]);
                $query->execute();
                $r = $query->fetch(\PDO::FETCH_ASSOC);
                array_push($res,["conta"=>$r["identificador"]]);
            }

            $query=$this->conexao->prepare("SELECT * FROM configuracao WHERE pin = :pin AND cliente_identificador = :identificador");
            $query->bindValue(':pin', $this->funcoes::fazHash($dados['pin']));
            $query->bindValue(':identificador', $identificador_cliente["cliente_identificador"]);
            $query->execute();

            $res = array_merge($res[0], $tipo, ['telefone' => $dados['id'], "quando"=>time()]);
            //var_dump($res);
            if($query->rowCount() > 0){
                return ["message"=>"Logado com sucesso","ok"=>true, "dados"=>$res];
            }else{
                return ["message"=>"Credenciais erradas","ok"=>false];
            }

        }else{
            return ["message"=>"Nao verificado","ok"=>false];
        }
    }

    public function recuperar($dados){
        $res = [];
        $query=$this->conexao->prepare("SELECT * FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $dados['id']);
        $query->execute();

        if($query->rowCount() > 0){

            $cliente = $query->fetch(\PDO::FETCH_ASSOC);
            $this->funcoes::setRemetente('FETA-FACIL');
            $codigo = $this->funcoes::seisDigitos();
            $mensagem = "Estimado cidadão, o seu código para recuperacao de conta é: $codigo";
            $this->funcoes::enviaSMS($dados['id'], $mensagem);

            $query=$this->conexao->prepare("INSERT INTO confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou) VALUES (:cliente, :acao, :codigo, :quando, :confirmou)");
            $query->bindValue(':cliente', $dados["id"]);
            $query->bindValue(':acao', "Recuperacao de pin");
            $query->bindValue(':codigo', $codigo);
            $query->bindValue(':quando', time());
            $query->bindValue(':confirmou', "0");
            $query->execute();
            return ["message"=>"Numero de verificacao enviado","ok"=>true];
        }else{
            return ["message"=>"Este numero nao se encontra na nossa base de dados","ok"=>false];
        }
    }

    public function confirmarCodigo($dados){
        $confirmar = $this->verificaCodigo($dados);
        if($confirmar["ok"]){
            $confirmar["codigo"] = $dados["codigo"];
            $confirmar["id"] = $dados["id"];
            return $confirmar;
        }
        return $confirmar;
    }

    public function novoPin($dados){
        $query=$this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $dados['id']);
        $query->execute();
        $identificador_cliente = $query->fetch(\PDO::FETCH_ASSOC);

        
        $query=$this->conexao->prepare("SELECT * FROM confirmar WHERE cliente_identificador = :telefone AND codigo_enviado = :codigo AND confirmou = :confirmou");
        $query->bindValue(':telefone', $dados['id']);
        $query->bindValue(':codigo', $dados['codigo']);
        $query->bindValue(':confirmou', "1");
        $query->execute();
        if($query->rowCount() > 0){
            $query=$this->conexao->prepare("UPDATE configuracao SET pin = :pin WHERE cliente_identificador = :cliente");
            $query->bindValue(':pin', $this->funcoes::fazHash($dados['pin']));
            $query->bindValue(':cliente', $identificador_cliente["cliente_identificador"]);
            $query->execute();

            
            $query=$this->conexao->prepare("UPDATE confirmar SET codigo_enviado = :novo WHERE cliente_identificador = :telefone AND codigo_enviado = :codigo AND confirmou = :confirmou");
            $query->bindValue(':novo', "------");
            $query->bindValue(':telefone', $dados['id']);
            $query->bindValue(':codigo', $dados['codigo']);
            $query->bindValue(':confirmou', "1");
            $query->execute();

            $mensagem = "ALERTA: O seu pin foi atualizado! \n  \n  \n  \n É FETA, É FACIL.";
            $this->funcoes::enviaSMS($dados["id"], $mensagem);
            return ["message"=>"Pin atualizado","ok"=>true];
        }

        return ["message"=>"Erro inexperado, tente mais tarde","ok"=>false];

    }
}