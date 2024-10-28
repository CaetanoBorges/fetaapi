<?php

namespace Controladores;

use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Exception;

class CheckIn
{
    protected $funcoes;
    protected $conexao;
    protected $autorizacao;
    protected $body;
    protected $autorizado = null;
    protected $expirou = null;

    public function __construct()
    {
        $this->funcoes = new Funcoes();
        $this->conexao = Funcoes::conexao();
    }
    protected function fazCheckIn($request)
    {
        try {

            $token = $request->getHeader('token')[0];
            $this->body = $request->getParsedBody();
            $this->autorizacao = new Autorizacao($token, $this->conexao);
             if ($this->autorizacao->expirou) {
                    $this->expirou = true;
                } else {
                    $this->expirou = false;
                }
            if (isset($request->getHeader('pin')[0])) {
                $res = $this->autorizacao->verificaPin($request->getHeader('pin')[0], $this->autorizacao->getCliente());
                if ($res['ok']) {
                    $this->autorizado = true;
                } else {
                    $this->autorizado = false;
                }
            }
            if (isset($request->getHeader('codigo')[0])) {
                $res = $this->autorizacao->verificaCodigo($this->autorizacao->getId(), $request->getHeader('codigo')[0]);
                if ($res['ok']) {
                    $this->autorizado = true;
                } else {
                    $this->autorizado = false;
                }
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
            return;
        }
    }
    protected function enviaCodigo($acao)
    {
        return $this->autorizacao->enviaCodigo($this->autorizacao->getId(), $acao);
    }
    protected function _($response, $res)
    {
        $response->getBody()->write(json_encode($res));
        return $response;
    }
}
