<?php

namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Controladores\CheckIn;
use Classes\Configuracao;

use Exception;

class ConfiguracaoControl extends CheckIn
{
    protected $configuracao;

    public function __construct()
    {
        parent::__construct();
        $this->configuracao = new Configuracao($this->conexao, $this->funcoes);
    }

    public function timeout(Request $request, Response $response, $args)
    {

        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        $res = $this->configuracao->verTempoBloqueio($this->autorizacao->getCliente());

        return $this->_($response, $res);
    }

    public function setTimeout(Request $request, Response $response, $args)
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

        $res = $this->configuracao->setTimeOut($this->autorizacao->getCliente(), $this->body['tempo_bloqueio']);
        return $this->_($response, $res);
    }

    public function setPin(Request $request, Response $response, $args)
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

        $res = $this->configuracao->alteraPin($this->autorizacao->getCliente(), $this->body['pin'], $this->autorizacao->getId());
        return $this->_($response, $res);
    }


    public function pedeCodigo(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->enviaCodigo($this->body['acao']);
        return $this->_($response, $res);
    }
    public function pedeCodigoLevantamento(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->enviaCodigoAoCliente($this->body['numero'], $this->body['acao']);
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

    public function convidarAmigo(Request $request, Response $response, $args)
    {
        //------INICIO--CHECK-IN-------//
        $this->fazCheckIn($request);
        if ($this->expirou) {
            return $this->_($response, ['ok' => false, "nivel" => 1, 'payload' => 'Sessão expirou, acesse com o pin']);
        }
        //------FIM--CHECK-IN-------//

        
        $res = $this->convidar($this->body['telefone'],$this->autorizacao->getId());
        return $this->_($response, $res);
    }
}
