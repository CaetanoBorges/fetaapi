<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Transacao;

use Exception;

class TransacaoControl extends CheckIn
{
    protected $transacao;

    public function __construct()
    {
        parent::__construct();
        $this->transacao = new Transacao($this->conexao, $this->funcoes);
    }

    public function init(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Token invalido']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->transacao->verTodosInit($this->autorizacao->getId());

        return $this->_($response, $res);
    }
    public function ver(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Token invalido']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->transacao->verTodos($this->autorizacao->getId(),$this->body["mes"],$this->body["ano"]);

        return $this->_($response, $res);
    }
    public function detalhe(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Token invalido']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->transacao->verDetalhes($this->body["pid"]);

        return $this->_($response, $res);
    }

    
}
