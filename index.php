<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;


use Ferramentas\Funcoes;
use Controladores\AuthControl;

require 'vendor/autoload.php';

$funcoes = new Funcoes;
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType("application/json");
$afterMiddleware = function (Request $request, RequestHandler $handler) {
    // Proceed with the next middleware
    $response = $handler->handle($request);
    
    // Modify the response after the application has processed the request
    $response = $response->withHeader('content-type',"application/json");
    
    return $response;
};
$app->add($afterMiddleware);
$app->setBasePath("/fetaapi");
$app->get('/', function (Request $request, Response $response, $args) {

    $response->getBody()->write("Hello World!");
    return $response;
});
$app->get('/{id}', function (Request $request, Response $response, $args) {

    $conexao = Funcoes::conexao();
    $query = $conexao->prepare("select * from cliente where identificador=?");
    $query->bindValue(1,$args["id"]);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('content-type',"application/json");
});
$app->get('/usuario/{id}', function (Request $request, Response $response, $args) {
    
    $body = $request->getParsedBody();

    $response->getBody()->write(json_encode($body));

    return $response->withHeader('content-type',"application/json")->withStatus(201);
});

$app->post('/auth/verificaexistencia', AuthControl::class.":verificaExistencia");
$app->post('/auth/verificatelefone', AuthControl::class.":verificaTelefone");
$app->post('/auth/cadastrar', AuthControl::class.":cadastrar");
$app->post('/auth/entrar', AuthControl::class.":entrar");

// Run app
$app->run();