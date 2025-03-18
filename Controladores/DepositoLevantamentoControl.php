<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\DepositoLevantamento;

use Exception;

class DepositoLevantamentoControl extends CheckIn
{
    protected $DepositoLevantamento;

    public function __construct()
    {
        parent::__construct();
        $this->DepositoLevantamento = new DepositoLevantamento($this->conexao, $this->funcoes);
    }

    public function init(Request $request, Response $response, $args)
    {
        
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->DepositoLevantamento->verTodosInit($this->autorizacao->getCliente());

        return $this->_($response, $res);
    }
    public function ver(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//
        
        $res = $this->DepositoLevantamento->verTodos($this->autorizacao->getCliente(),$this->body["mes"],$this->body["ano"]);

        return $this->_($response, $res);
    }
    public function detalhe(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->DepositoLevantamento->verDetalhes($this->body["pid"]);

        return $this->_($response, $res);
    }

    
}
