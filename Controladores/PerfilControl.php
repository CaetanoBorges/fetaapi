<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Perfil;

use Exception;

class PerfilControl extends CheckIn
{
    protected $perfil;

    public function __construct()
    {
        parent::__construct();
        $this->perfil = new Perfil($this->conexao, $this->funcoes);
    }

    public function init(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->perfil->init($this->autorizacao->getCliente());

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
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $res = $this->perfil->verDetalhes($this->autorizacao->getCliente());

        return $this->_($response, $res);
    }

    
}
