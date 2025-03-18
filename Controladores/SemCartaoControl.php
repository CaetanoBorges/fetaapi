<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\SemCartao;

use Exception;

class SemCartaoControl extends CheckIn
{
    protected $semcartao;

    public function __construct()
    {
        parent::__construct();
        $this->semcartao = new SemCartao($this->conexao, $this->funcoes);
    }

    public function timeout(Request $request, Response $response, $args)
    {

        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        #$res = $this->semcartao->verTempoBloqueio($this->autorizacao->getCliente());

        #return $this->_($response, $res);
    }

    public function cancelar(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->semcartao->cancelarLevantamentoSemCartao($this->body['identificador'],$this->autorizacao->getId());
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

        $res = $this->semcartao->verDetalhes($this->body['identificador']);
        return $this->_($response, $res);
    }


    public function verTodos(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->semcartao->verTodos($this->autorizacao->getCliente());
        return $this->_($response, $res);
    }
    public function novoLevantamentoSemCartao(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->semcartao->novoLevantamento($this->autorizacao->getCliente(),$this->body['valor'],$this->body['codigo'],$this->autorizacao->getId());
        return $this->_($response, $res);
    }
    
  /*   public function verLimites(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->limites();
        return $this->_($response, $res);
    } */

}
