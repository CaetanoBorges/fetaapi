<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Receber;

use Exception;

class ReceberControl extends CheckIn
{
    protected $receber;

    public function __construct()
    {
        parent::__construct();
        $this->receber = new Receber($this->conexao, $this->funcoes);
    }

    public function novo(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Token invalido']);
        }
        if (!$this->autorizado) {
            return $this->_($response, ['ok' => false, "nivel" => 0, 'payload' => 'Autorizacao errada']);
        }
        //------FIM--CHECK-IN-------//
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $res = $this->receber->nova($this->body["de"], $this->autorizacao->getId(), $this->body["tipo"], $this->body["onde"], $this->body["valor"], $this->body["descricao"], $this->body["opcoes"]);
        //var_dump($res);
        if(!$res["ok"]){
            return $this->_($response, $res);
        }

        $envia = $this->receber->commit();
        return $this->_($response, $envia);
    }

    
}
