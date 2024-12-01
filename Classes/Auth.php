<?php

namespace Classes;


class Auth
{
    protected $funcoes;
    protected $conexao;

    public function __construct($conexao, $funcoes)
    {
        $this->conexao = $conexao;
        $this->funcoes = $funcoes;
    }

    /**
     * Verifica se existe um particular com um determinado numero de BI
     * @param string $bi
     * @return boolean
     */
    public function existeBi($bi)
    {
        $query = $this->conexao->prepare("SELECT * FROM cliente WHERE bi = :bi");
        $query->bindValue(':bi', $bi);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se existe um contacto com um determinado telefone
     * @param string $telefone
     * @return boolean
     */
    public function existeTelefone($telefone)
    {
        $query = $this->conexao->prepare("SELECT * FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $telefone);
        $query->execute();
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Envia um código de confirmação para um determinado telefone
     * @param string $telefone
     * @return boolean
     */
    public function enviaCodigo($telefone)
    {
        $this->funcoes::setRemetente('FETA-FACIL');
        $codigo = $this->funcoes::seisDigitos();
        $mensagem = "Caro cidadão, o seu código de confirmação para o cadastro é: $codigo";
        $this->funcoes::enviaSMS($telefone, $mensagem);

        $query = $this->conexao->prepare("INSERT INTO confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou) VALUES (:cliente, :acao, :codigo, :quando, :confirmou)");
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

        /* $queryCliente=$this->conexao->prepare("INSERT INTO cliente (identificador, empresa) VALUES (:identificador, :empresa)");
        $queryCliente->bindValue(':identificador', $identificador);
        $queryCliente->bindValue(':empresa', "0"); */

        $queryEndereco = $this->conexao->prepare("INSERT INTO endereco (cliente_identificador, atual) VALUES (:cliente_identificador, :atual)");
        $queryEndereco->bindValue(':cliente_identificador', $identificador);
        $queryEndereco->bindValue(':atual', "1");

        $queryContacto = $this->conexao->prepare("INSERT INTO contacto (cliente_identificador, telefone, atual) VALUES (:cliente_identificador, :telefone, :atual)");
        $queryContacto->bindValue(':cliente_identificador', $identificador);
        $queryContacto->bindValue(':telefone', $dados['id']);
        $queryContacto->bindValue(':atual', "1");

        $queryConfiguracao = $this->conexao->prepare("INSERT INTO configuracao (cliente_identificador, tempo_bloqueio, pin) VALUES (:cliente_identificador, :tempo_bloqueio, :pin)");
        $queryConfiguracao->bindValue(':cliente_identificador', $identificador);
        $queryConfiguracao->bindValue(':tempo_bloqueio', "mins1");
        $queryConfiguracao->bindValue(':pin', $pin);

        $queryParticular = $this->conexao->prepare("INSERT INTO cliente (identificador, bi, nome, genero, nascimento, altura, estado_civil, morada, provincia, natural_de, filiacao, foto_bi, balanco, img) VALUES (:identificador, :bi, :nome, :genero, :nascimento, :altura, :estado_civil, :morada, :provincia, :natural_de, :filiacao, :foto_bi, :balanco, :img)");
        $queryParticular->bindValue(':identificador', $identificador);
        $queryParticular->bindValue(':bi', $dados['bi']);
        $queryParticular->bindValue(':nome', $dados['nome']);
        $queryParticular->bindValue(':genero', $dados['genero']);
        $queryParticular->bindValue(':nascimento', $dados['nascimento']);
        $queryParticular->bindValue(':altura', $dados['altura']);
        $queryParticular->bindValue(':estado_civil', $dados['estado_civil']);
        $queryParticular->bindValue(':morada', $dados['morada']);
        $queryParticular->bindValue(':provincia', $dados['provincia']);
        $queryParticular->bindValue(':natural_de', $dados['natural_de']);
        $queryParticular->bindValue(':filiacao', $dados['filiacao']);
        $queryParticular->bindValue(':foto_bi', $dados['foto_bi']);
        $queryParticular->bindValue(':balanco', "0.00");
        $queryParticular->bindValue(':img', "user.svg");

        try {
            $this->conexao->beginTransaction();
            //$queryCliente->execute();
            $queryEndereco->execute();
            $queryContacto->execute();
            $queryConfiguracao->execute();
            $queryParticular->execute();
            $this->conexao->commit();
        } catch (\PDOException $e) {

            $this->conexao->rollBack();
            return ["payload" => $e->getMessage(), "ok" => false];
        }
        $this->funcoes::setRemetente('FETA-FACIL');
        $mensagem = "BEM-VINDO A FETA FACIL, agora pode: Receber pagamentos, fazer combranças, desfrutar de pagamentos online e muito mais. O MELHOR SISTEMA DE PAGAMENTOS DE ANGOLA! \n \n \n \n É FETA, É FACIL.";
        $this->funcoes::enviaSMS($dados["id"], $mensagem);
        return ["payload" => "Conta criada com sucesso", "ok" => true];
    }

    public function verificaExistencia($dados)
    {
        if ($this->existeTelefone($dados['id'])) {
            return ["payload" => "Este telefone já está associado a uma conta", "ok" => true];
        }
        if ($this->existeBi($dados['bi'])) {
            return ["payload" => "Este BI já está associado a uma conta", "ok" => true];
        }

        return ["payload" => "Não existe nenhuma conta com esses dados", "ok" => false];
    }

    /**
     * Verifica se o código de confirmação recebido é o mesmo que
     * foi enviado para um determinado telefone
     * @param string $telefone
     * @param string $codigo
     * @return array
     */
    public function verificaCodigo($dados)
    {

        $query = $this->conexao->prepare("SELECT * FROM confirmar WHERE cliente_identificador = :cliente AND codigo_enviado = :codigo AND confirmou = :confirmou");
        $query->bindValue(':cliente', $dados['id']);
        $query->bindValue(':codigo', $dados['codigo']);
        $query->bindValue(':confirmou', 0);
        $query->execute();

        if ($query->rowCount() > 0) {

            $query = $this->conexao->prepare("UPDATE confirmar SET confirmou = :confirmou WHERE cliente_identificador = :cliente AND codigo_enviado = :codigo");
            $query->bindValue(':cliente', $dados['id']);
            $query->bindValue(':codigo', $dados['codigo']);
            $query->bindValue(':confirmou', 1);
            $query->execute();
            return ["payload" => "Verificação completa", "ok" => true];
        } else {
            return ["payload" => "Não foi verificado", "ok" => false];
        }
    }



    public function Entrar($dados)
    {
        $res = [];
        $query = $this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :telefone AND atual = :atual");
        $query->bindValue(':telefone', $dados['id']);
        $query->bindValue(':atual', "1");
        $query->execute();

        if ($query->rowCount() > 0) {

            $identificador_cliente = $query->fetch(\PDO::FETCH_ASSOC);
            array_push($res, ["conta" => $identificador_cliente["cliente_identificador"]]);

            $query = $this->conexao->prepare("SELECT * FROM configuracao WHERE pin = :pin AND cliente_identificador = :identificador");
            $query->bindValue(':pin', $this->funcoes::fazHash($dados['pin']));
            $query->bindValue(':identificador', $identificador_cliente["cliente_identificador"]);
            $query->execute();

            $res = array_merge($res[0], ['telefone' => $dados['id'], "identificador"=>$identificador_cliente["cliente_identificador"], "quando" => time()]);
            //var_dump($res);
            if ($query->rowCount() > 0) {
                return ["payload" => "Logado com sucesso", "ok" => true, "dados" => $res];
            } else {
                return ["payload" => "Credenciais erradas", "ok" => false];
            }
        } else {
            return ["payload" => "Não verificado", "ok" => false];
        }
    }

    public function recuperar($dados)
    {
        $res = [];
        $query = $this->conexao->prepare("SELECT * FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $dados['id']);
        $query->execute();

        if ($query->rowCount() > 0) {

            $cliente = $query->fetch(\PDO::FETCH_ASSOC);
            $this->funcoes::setRemetente('FETA-FACIL');
            $codigo = $this->funcoes::seisDigitos();
            $mensagem = "Estimado cidadão, o seu código para recuperação de conta é: $codigo";
            $this->funcoes::enviaSMS($dados['id'], $mensagem);

            $query = $this->conexao->prepare("INSERT INTO confirmar (cliente_identificador, acao, codigo_enviado, quando, confirmou) VALUES (:cliente, :acao, :codigo, :quando, :confirmou)");
            $query->bindValue(':cliente', $dados["id"]);
            $query->bindValue(':acao', "Recuperacao de pin");
            $query->bindValue(':codigo', $codigo);
            $query->bindValue(':quando', time());
            $query->bindValue(':confirmou', "0");
            $query->execute();
            return ["payload" => "Número de verificação enviado", "ok" => true];
        } else {
            return ["payload" => "Este número não se encontra na nossa base de dados", "ok" => false];
        }
    }

    public function confirmarCodigo($dados)
    {
        $confirmar = $this->verificaCodigo($dados);
        if ($confirmar["ok"]) {
            $confirmar["codigo"] = $dados["codigo"];
            $confirmar["id"] = $dados["id"];
            return $confirmar;
        }
        return $confirmar;
    }

    public function novoPin($dados)
    {
        $query = $this->conexao->prepare("SELECT cliente_identificador FROM contacto WHERE telefone = :telefone");
        $query->bindValue(':telefone', $dados['id']);
        $query->execute();
        $identificador_cliente = $query->fetch(\PDO::FETCH_ASSOC);


        $query = $this->conexao->prepare("SELECT * FROM confirmar WHERE cliente_identificador = :telefone AND codigo_enviado = :codigo AND confirmou = :confirmou");
        $query->bindValue(':telefone', $dados['id']);
        $query->bindValue(':codigo', $dados['codigo']);
        $query->bindValue(':confirmou', "1");
        $query->execute();
        if ($query->rowCount() > 0) {
            $query = $this->conexao->prepare("UPDATE configuracao SET pin = :pin WHERE cliente_identificador = :cliente");
            $query->bindValue(':pin', $this->funcoes::fazHash($dados['pin']));
            $query->bindValue(':cliente', $identificador_cliente["cliente_identificador"]);
            $query->execute();


            $query = $this->conexao->prepare("UPDATE confirmar SET codigo_enviado = :novo WHERE cliente_identificador = :telefone AND codigo_enviado = :codigo AND confirmou = :confirmou");
            $query->bindValue(':novo', "------");
            $query->bindValue(':telefone', $dados['id']);
            $query->bindValue(':codigo', $dados['codigo']);
            $query->bindValue(':confirmou', "1");
            $query->execute();

            $mensagem = "ALERTA: O seu pin foi atualizado! \n  \n  \n  \n É FETA, É FACIL.";
            $this->funcoes::enviaSMS($dados["id"], $mensagem);
            return ["payload" => "Pin atualizado", "ok" => true];
        }

        return ["payload" => "Erro inexperado, tente mais tarde", "ok" => false];
    }
}
