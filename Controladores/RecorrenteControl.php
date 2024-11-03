<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Recorrente;

use Exception;

class RecorrenteControl extends CheckIn
{
    protected $recorrente;

    public function __construct()
    {
        parent::__construct();
        $this->recorrente = new Recorrente($this->conexao, $this->funcoes);
    }

    public function init(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->recorrente->verTodos($this->autorizacao->getId());

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

        $res = $this->recorrente->verDetalhes($this->body["pid"]);

        return $this->_($response, $res);
    }
    
    public function cancelar(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->recorrente->cancelarRecorrente($this->body["pid"]);

        return $this->_($response, $res);
    }

    
}
