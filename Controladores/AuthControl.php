<?php
namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
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
           $this->auth->enviaCodigo($body['telefone']);
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
            $response->getBody()->write(json_encode($res));
        }else{
            $res = $this->auth->cadastrarParticular($body);
            $response->getBody()->write(json_encode($res));
        }
        return $response;
    }


}