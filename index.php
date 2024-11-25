<?php
header("Access-Control-Allow-Origin: *");

use DI\ContainerBuilder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;
use Slim\Http\UploadedFile;


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

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$container->set('upload_directory', __DIR__ . '/');

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->setBasePath("/fetaapi");

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

$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($app): ResponseInterface {
    if ($request->getMethod() === 'OPTIONS') {
        $response = $app->getResponseFactory()->createResponse();
    } else {
        $response = $handler->handle($request);
    }

    $response = $response
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Pragma', 'no-cache');

    if (ob_get_contents()) {
        ob_clean();
    }

    return $response;
});
$app->add($afterMiddleware);

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
    $group->post('/ver', EstatisticaControl::class.":ver");
});


$app->group('/transacao', function (RouteCollectorProxy $group) {
    $group->get('/init', TransacaoControl::class.":init");
    $group->post('/ver', TransacaoControl::class.":ver");
    $group->post('/detalhes', TransacaoControl::class.":detalhe");
    $group->post('/receber', ReceberControl::class.":novo"); // Da class Receber
    $group->post('/enviar', EnviarControl::class.":novo"); // Da class Enviar
    $group->post('/aceitarpendente', EnviarControl::class.":aceitarPendente"); // Da class Enviar
});


$app->group('/recorrente', function (RouteCollectorProxy $group) {
    $group->get('/init', RecorrenteControl::class.":init");
    $group->post('/detalhes', RecorrenteControl::class.":detalhe");
    $group->post('/cancelar', RecorrenteControl::class.":cancelar");
});

$app->group('/perfil', function (RouteCollectorProxy $group) {
    $group->get('/init', PerfilControl::class.":init");
    $group->get('/detalhes', PerfilControl::class.":detalhe");
});

$app->group('/pendente', function (RouteCollectorProxy $group) {
    $group->get('/init', PendenteControl::class.":init");
    $group->post('/detalhes', PendenteControl::class.":detalhe");
    $group->post('/cancelar', PendenteControl::class.":cancelar");
});

$app->post('/scan', function (ServerRequestInterface $request, ResponseInterface $response) {
    $directory = $this->get('upload_directory');
    $uploadedFiles = $request->getUploadedFiles();

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['bifrente'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);
        $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
    }
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['bitras'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);
        $response->getBody()->write('Uploaded: ' . $filename . '<br/>');
    }

    return $response;
});
/**
 * Moves the uploaded file to the upload directory and assigns it a unique name
 * to avoid overwriting an existing uploaded file.
 *
 * @param string $directory The directory to which the file is moved
 * @param UploadedFileInterface $uploadedFile The file uploaded file to move
 *
 * @return string The filename of moved file
 */
function moveUploadedFile(string $directory, $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

    // see http://php.net/manual/en/function.random-bytes.php
    $basename = bin2hex(random_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);

    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}

// Run app
$app->run();


//$app->post('/auth/verificaexistencia', AuthControl::class.":verificaExistencia");
//$app->post('/auth/verificatelefone', AuthControl::class.":verificaTelefone");
//$app->post('/auth/cadastrar', AuthControl::class.":cadastrar");
//$app->post('/auth/entrar', AuthControl::class.":entrar");
//$app->post('/auth/recuperarconta', AuthControl::class.":recuperarConta");
//$app->post('/auth/confirmarcodigo', AuthControl::class.":confirmarCodigo");
//$app->post('/auth/novopin', AuthControl::class.":novoPin");