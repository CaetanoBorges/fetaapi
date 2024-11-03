<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Estatistica;

use Exception;

class EstatisticaControl extends CheckIn
{
    protected $estatistica;

    public function __construct()
    {
        parent::__construct();
        $this->estatistica = new Estatistica($this->conexao, $this->funcoes);
    }

    public function init(Request $request, Response $response, $args)
    {

        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->estatistica->verTodosInit($this->autorizacao->getConta());

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

        $res = $this->estatistica->verTodos($this->autorizacao->getConta(),$this->body["mes"],$this->body["ano"]);

        return $this->_($response, $res);
    }

    
}
