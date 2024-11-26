<?php
header("Access-Control-Allow-Origin: *");

use Treinetic\ImageArtist\lib\Image;
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
    $response = $response->withHeader('content-type', "application/json");

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
    $query->bindValue(1, $args["id"]);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('content-type', "application/json");
});

$app->group('/auth', function (RouteCollectorProxy $group) {
    $group->post('/verificaexistencia', AuthControl::class . ":verificaExistencia");
    $group->post('/verificatelefone', AuthControl::class . ":verificaTelefone");
    $group->post('/cadastrar', AuthControl::class . ":cadastrar");
    $group->post('/entrar', AuthControl::class . ":entrar");
    $group->post('/recuperarconta', AuthControl::class . ":recuperarConta");
    $group->post('/confirmarcodigo', AuthControl::class . ":confirmarCodigo");
    $group->post('/novopin', AuthControl::class . ":novoPin");
});
$app->post('/pedecodigo', ConfiguracaoControl::class . ":pedecodigo");

$app->group('/config', function (RouteCollectorProxy $group) {
    $group->get('/timeout', ConfiguracaoControl::class . ":timeout");
    $group->post('/settimeout', ConfiguracaoControl::class . ":setTimeout");
    $group->post('/alterarpin', ConfiguracaoControl::class . ":setPin");
});


$app->group('/estatistica', function (RouteCollectorProxy $group) {
    $group->get('/init', EstatisticaControl::class . ":init");
    $group->post('/ver', EstatisticaControl::class . ":ver");
});


$app->group('/transacao', function (RouteCollectorProxy $group) {
    $group->get('/init', TransacaoControl::class . ":init");
    $group->post('/ver', TransacaoControl::class . ":ver");
    $group->post('/detalhes', TransacaoControl::class . ":detalhe");
    $group->post('/receber', ReceberControl::class . ":novo"); // Da class Receber
    $group->post('/enviar', EnviarControl::class . ":novo"); // Da class Enviar
    $group->post('/aceitarpendente', EnviarControl::class . ":aceitarPendente"); // Da class Enviar
});


$app->group('/recorrente', function (RouteCollectorProxy $group) {
    $group->get('/init', RecorrenteControl::class . ":init");
    $group->post('/detalhes', RecorrenteControl::class . ":detalhe");
    $group->post('/cancelar', RecorrenteControl::class . ":cancelar");
});

$app->group('/perfil', function (RouteCollectorProxy $group) {
    $group->get('/init', PerfilControl::class . ":init");
    $group->get('/detalhes', PerfilControl::class . ":detalhe");
});

$app->group('/pendente', function (RouteCollectorProxy $group) {
    $group->get('/init', PendenteControl::class . ":init");
    $group->post('/detalhes', PendenteControl::class . ":detalhe");
    $group->post('/cancelar', PendenteControl::class . ":cancelar");
});

$app->post('/scan', function (ServerRequestInterface $request, ResponseInterface $response) {
    $directory = $this->get('upload_directory');
    $uploadedFiles = $request->getUploadedFiles();
    $tras = "";
    $frente = "";
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['bifrente'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $frente = moveUploadedFile($directory, $uploadedFile);
    }
    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['bitras'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $tras = moveUploadedFile($directory, $uploadedFile);
    }

    $bg = new Image("bg.png");
    $bg->scaleToHeight(4096); //scales the bg keeping height 4096
    $bg->scaleToWidth(4096); //scales the bg keeping width 4096
    $bg->resize(2048,4096); //resizes the bg to a given size 

    $img1 = new Image($frente);

    //var_dump($img1);
    
    $img1->scaleToHeight($img1->getHeight()); //scales the img1 keeping height 2048
    $img1->scaleToWidth($img1->getWidth()); //scales the img1 keeping width 2048
    $img1->resize($img1->getWidth(), $img1->getHeight()); //resizes the img1 to a given size 

    $img2 = new Image($tras);

    $img2->scaleToHeight($img2->getHeight()); //scales the img2 keeping height 2048
    $img2->scaleToWidth($img2->getWidth()); //scales the img2 keeping width 2048
    $img2->resize($img2->getWidth(),$img2->getHeight()); //resizes the img2 to a given size 

    $bg->merge($img1, 0, 0);
    $bg->merge($img2, 0, $img2->getHeight());
    $bg->rotate(180);
    $merged = time() . ".jpg";
    $bg->save($merged);

    unlink($tras);
    unlink($frente);

    
    $fileData = fopen($merged, 'r');
    $client = new \GuzzleHttp\Client();
    try {
        $r = $client->request('POST', 'https://api.ocr.space/parse/image', [
            'headers' => ['apiKey' => 'K83766600088957'],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $fileData
                ]
            ]
        ], ['file' => $fileData]);
        $res =  json_decode($r->getBody(), true);
        $dadosBI = arrayDados($res["ParsedResults"][0]["ParsedText"]);
        $response->getBody()->write(json_encode($dadosBI));
    } catch (GuzzleHttp\Exception\ClientException $e) {
        echo $e->getResponse()->getBody();
    } 

    //unlink($merged);

    
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
function arrayDados($response)
{
    $response = str_replace("\r", "", $response);
    $response = str_replace("\n", " ", $response);
    $result = [];
    $response = (string) $response;
    $NOMEPOS = strpos($response, "Nome Completo: ");
    $result["nome"] = substr($response, ($NOMEPOS + 15), 25);

    $NASCIMENTOPOS = strpos($response, " Altura(m)");
    $result["nascimento"] = substr($response, ($NASCIMENTOPOS - 10), 10);

    $ESTADOCIVILPOS = strpos($response, "Estado Civil: ");
    $result["esta_civil"] = substr($response, ($ESTADOCIVILPOS + 14), 7);

    $SEXOPOS = strpos($response, "Sexo: ");
    $result["sexo"] = substr($response, ($SEXOPOS + 6), 9);

    $ALTURAPOS = strpos($response, "): ");
    $result["altura"] = substr($response, ($ALTURAPOS + 3), 4);

    $PROVINCIAPOS = strpos($response, "Provincia de: ");
    $result["provincia"] = substr($response, ($PROVINCIAPOS + 14), 15);

    $RESIDENCIAPOS = strpos($response, "Residéncia: ");
    $result["morada"] = substr($response, ($RESIDENCIAPOS + 12), 33);

    $BIPOS = strpos($response, "NO: ");
    $result["bi"] = substr($response, ($BIPOS + 4), 13);

    $NATURALPOS = strpos($response, "Natural de: ");
    $result["natural"] = substr($response, ($NATURALPOS + 12), 13);

    $FILIACAOPOSPOS = strpos($response, "Filiaqäo: ");
    if(!$FILIACAOPOSPOS){ $FILIACAOPOSPOS = strpos($response, "FiliaGäo: "); }
    if(!$FILIACAOPOSPOS){ $FILIACAOPOSPOS = strpos($response, "FiliaGäo:"); }
    $result["filiacao"] = substr($response, ($FILIACAOPOSPOS + 11), 35);
    return $result;
}
function rm_special_chars($string) {

        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;

            $chars = array(
                chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
                chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
                chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
                chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
                chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
                chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
                chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
                chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
                chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
                chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
                chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
                chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
                chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
                chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
                chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
                chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
                chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
                chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
                chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
                chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
                chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
                chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
                chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
                chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
                chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
                chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
                chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
                chr(195).chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
                chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
                chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
                chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
                chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
                chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
                chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
                chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
                chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
                chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
                chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
                chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
                chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
                chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
                chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
                chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
                chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
                chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
                chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
                chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
                chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
                chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
                chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
                chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
                chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
                chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
                chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
                chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
                chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
                chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
                chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
                chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
                chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
                chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
                chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
                chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
                chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
                chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
                chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
                chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
                chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
                chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
                chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
                chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
                chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
                chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
                chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
                chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
                chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
                chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
                chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
                chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
                chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
                chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
                chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
                chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
                chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
                chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
                chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
                chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
                chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
                chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
                chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
                chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
            );

        $string = strtr($string, $chars);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        return str_replace('-', ' ', $string);
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