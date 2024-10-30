<?php
header("Access-Control-Allow-Origin: *");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;


use Ferramentas\Funcoes;
use Controladores\AuthControl;
use Controladores\AutorizacaoControl;
use Controladores\ConfiguracaoControl;
use Controladores\EstatisticaControl;
use Controladores\TransacaoControl;
use Controladores\RecorrenteControl;
use Controladores\PerfilControl;
use Controladores\PendenteControl;
use Controladores\ReceberControl;
use Controladores\EnviarControl;

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

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/verificaexistencia', AuthControl::class.":verificaExistencia");
    $group->post('/verificatelefone', AuthControl::class.":verificaTelefone");
    $group->post('/cadastrar', AuthControl::class.":cadastrar");
    $group->post('/entrar', AuthControl::class.":entrar");
    $group->post('/recuperarconta', AuthControl::class.":recuperarConta");
    $group->post('/confirmarcodigo', AuthControl::class.":confirmarCodigo");
    $group->post('/novopin', AuthControl::class.":novoPin");
});
$app->post('/pedecodigo', ConfiguracaoControl::class.":pedecodigo");

$app->group('/config', function (RouteCollectorProxy $group) {
    $group->get('/timeout', ConfiguracaoControl::class.":timeout");
    $group->post('/settimeout', ConfiguracaoControl::class.":setTimeout");
    $group->post('/alterarpin', ConfiguracaoControl::class.":setPin");
});


$app->group('/estatistica', function (RouteCollectorProxy $group) {
    $group->get('/init', EstatisticaControl::class.":init");
    $group->get('/ver', EstatisticaControl::class.":ver");
});


$app->group('/transacao', function (RouteCollectorProxy $group) {
    $group->get('/init', TransacaoControl::class.":init");
    $group->get('/ver', TransacaoControl::class.":ver");
    $group->get('/detalhes', TransacaoControl::class.":detalhe");
    $group->post('/receber', ReceberControl::class.":novo"); // Da class Receber
    $group->post('/enviar', EnviarControl::class.":novo"); // Da class Enviar
    $group->post('/aceitarpendente', EnviarControl::class.":aceitarPendente"); // Da class Enviar
});


$app->group('/recorrente', function (RouteCollectorProxy $group) {
    $group->get('/init', RecorrenteControl::class.":init");
    $group->get('/detalhes', RecorrenteControl::class.":detalhe");
});

$app->group('/perfil', function (RouteCollectorProxy $group) {
    $group->get('/init', PerfilControl::class.":init");
    $group->get('/detalhes', PerfilControl::class.":detalhe");
});

$app->group('/pendente', function (RouteCollectorProxy $group) {
    $group->get('/init', PendenteControl::class.":init");
    $group->get('/detalhes', PendenteControl::class.":detalhe");
    $group->get('/cancelar', PendenteControl::class.":cancelar");
});



// Run app
$app->run();


//$app->post('/auth/verificaexistencia', AuthControl::class.":verificaExistencia");
//$app->post('/auth/verificatelefone', AuthControl::class.":verificaTelefone");
//$app->post('/auth/cadastrar', AuthControl::class.":cadastrar");
//$app->post('/auth/entrar', AuthControl::class.":entrar");
//$app->post('/auth/recuperarconta', AuthControl::class.":recuperarConta");
//$app->post('/auth/confirmarcodigo', AuthControl::class.":confirmarCodigo");
//$app->post('/auth/novopin', AuthControl::class.":novoPin");