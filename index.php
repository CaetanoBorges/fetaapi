<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php';


$app = AppFactory::create();


$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->setBasePath("/fetaapi");
$app->get('/', function (Request $request, Response $response, $args) {

    $response->getBody()->write("Hello World!");
    return $response;
});
$app->setBasePath("/fetaapi");
$app->post('/receber', function (Request $request, Response $response, $args) {

    $response->getBody()->write("Hello post!");
    return $response;
});


// Run app
$app->run();