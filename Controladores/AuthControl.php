<?php
namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\Auth;

class AuthControl
{
    protected $funcoes;
    protected $conexao;
    protected $auth;

    public function __construct(){
        $this->funcoes = new Funcoes();
        $this->conexao = Funcoes::conexao();
        $this->auth = new Auth($this->conexao, $this->funcoes);
    }
    public function verificaExistencia(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->verificaExistencia($body);
        if(!$res['ok']){
           $this->auth->enviaCodigo($body['id']);
        }
        $response->getBody()->write(json_encode($res));
        return $response;
    }
    public function verificaTelefone(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->verificaCodigo($body);
        if($res['ok']){
           $response->getBody()->write(json_encode($res));
        }else{
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }
    public function cadastrar(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        if($body['comercial']){
            $res = $this->auth->cadastrarEmpresa($body);
            if($res['ok']){

                $res = $this->auth->entrar($body);
                $credencial = json_encode($res['dados']);
                $cript = new Criptografia();
                $chave_sms_real = $cript->fazChave();
                $chave_sms = $cript->criptChave($chave_sms_real);

                $sms = $cript->encrypt($credencial,$chave_sms_real);

                $return['token'] = $sms.'.'.$chave_sms;
                $return['sms'] = "Cadastro de conta comercial concluido";
                $return['ok'] = true;
                $response->getBody()->write(json_encode($return));
            }else{
                $response->getBody()->write(json_encode($res));
            }
        }else{
            $res = $this->auth->cadastrarParticular($body);
            
            if($res['ok']){
                $res = $this->auth->entrar($body);
                $credencial = json_encode($res['dados']);
                $cript = new Criptografia();
                $chave_sms_real = $cript->fazChave();
                $chave_sms = $cript->criptChave($chave_sms_real);

                $sms = $cript->encrypt($credencial,$chave_sms_real);

                $return['token'] = $sms.'.'.$chave_sms;
                $return['sms'] = "Cadastro de conta particular concluido";
                $return['ok'] = true;
                $response->getBody()->write(json_encode($return));
            }else{
                $response->getBody()->write(json_encode($res));
            }
            
        }
        return $response;
    }

    public function entrar(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->entrar($body);
        if($res['ok']){

            $credencial = json_encode($res['dados']);
            $cript = new Criptografia();
            $chave_sms_real = $cript->fazChave();
            $chave_sms = $cript->criptChave($chave_sms_real);

            $sms = $cript->encrypt($credencial,$chave_sms_real);

            $return['token'] = $sms.'.'.$chave_sms;
            $return['sms'] = $res['sms'];
            $return['ok'] = true;
            $response->getBody()->write(json_encode($return));
        }else{
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }
    
    public function recuperarConta(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->recuperar($body);
        if($res['ok']){
            $response->getBody()->write(json_encode($res));
        }else{
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }
    public function confirmarCodigo(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->confirmarCodigo($body);
        if($res['ok']){
            $response->getBody()->write(json_encode($res));
        }else{
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }
    public function novoPin(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->auth->novoPin($body);
        if($res['ok']){
            $res = $this->auth->entrar($body);
            if($res['ok']){

                $credencial = json_encode($res['dados']);
                $cript = new Criptografia();
                $chave_sms_real = $cript->fazChave();
                $chave_sms = $cript->criptChave($chave_sms_real);

                $sms = $cript->encrypt($credencial,$chave_sms_real);

                $return['token'] = $sms.'.'.$chave_sms;
                $return['sms'] = "Pin atualizado";
                $return['ok'] = true;
                $response->getBody()->write(json_encode($return));
            }
        }else{
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }
}