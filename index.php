<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use Ferramentas\Funcoes;

require 'vendor/autoload.php';

$funcoes = new Funcoes;
$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType("application/json");

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
$app->post('/user/add', function (Request $request, Response $response, $args) {
    
    $body = $request->getParsedBody();

    $response->getBody()->write(json_encode($body));

    return $response->withHeader('content-type',"application/json")->withStatus(201);
});


// Run app
$app->run();