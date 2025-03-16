<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Enviar;

use Exception;

class EnviarControl extends CheckIn
{
    protected $enviar;

    public function __construct()
    {
        parent::__construct();
        $this->enviar = new Enviar($this->conexao, $this->funcoes);
    }

    public function novo(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        if (!$this->autorizado) {
            return $this->_($response, ['ok' => false, "nivel" => 0, 'payload' => 'Autorização errada']);
        } 
        

        $res = $this->enviar->nova($this->autorizacao->getId(), $this->body["para"], $this->body["tipo"], $this->body["onde"], $this->body["valor"], $this->body["descricao"], $this->body["opcoes"]);
        //var_dump($res);
        if(!$res["ok"]){
            return $this->_($response, $res);
        }

        $envia = $this->enviar->commit();
        return $this->_($response, $envia);
    }
    
    public function aceitarPendente(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        if (!$this->autorizado) {
            return $this->_($response, ['ok' => false, "nivel" => 0, 'payload' => 'Autorização errada']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->enviar->aceitarPendente($this->body["pid"]);
         if(!$res["ok"]){
            return $this->_($response, $res);
        }

        $envia = $this->enviar->commit();
        return $this->_($response, $envia);
    }
    
}
