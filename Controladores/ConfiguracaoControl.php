<?php
namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Classes\Configuracao;

class ConfiguracaoControl
{
    protected $funcoes;
    protected $conexao;
    protected $autorizacao;
    protected $configuracao;

    public function __construct($token){

        $this->funcoes = new Funcoes();
        $this->conexao = Funcoes::conexao();

        try {

            $this->autorizacao = new Autorizacao($token,Funcoes::conexao());
            $this->configuracao = new Configuracao($this->conexao, $this->funcoes);

        } catch (Exception $e) {

            echo $e->getMessage();
            return;

        }

    }
    public function timeout(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $res = $this->configuracao->verTempoBloqueio($this->autorizacao->getCliente());
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


}