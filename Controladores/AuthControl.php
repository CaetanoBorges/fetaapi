<?php
namespace Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ferramentas\Funcoes;
use Classes\Auth;

class AuthControl
{
    public function verificaExistencia(Request $request, Response $response, $args) 
    {
        $body = $request->getParsedBody();
        $response->getBody()->write(json_encode($body));

        return $response;
    }


}