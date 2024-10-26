<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;
use Ferramentas\Autorizacao;
use Classes\Transacao;
use Classes\Estatistica;
use Classes\Pendente;
use Classes\Recorrente;
use Classes\Configuracao;
use Classes\Perfil;
use Classes\Enviar;

require 'vendor/autoload.php';

$token = 'FcOXI3a2YG7mmPYAedvGojY+481gQ1jw3vi7tAt1jcUXcPZ1nEVZcwJ6bAT8oGRWvgLZ4tBWZWQOGzfIxWOSSwoE6yLPN0xIElR2OTujJc8Wgw+URjgoyvR+edTt5aFc+nbFoBi2bv1xCBKjtBrt8yz4WV5jSio9nlDZZkj+vsRqyQ==.NTE2NzU5NmE3NTM1NTU2ZTRiNzI2NDQ4NzY3NjM3NDY3MDRjNzc0NjQ4NGY2MTU5NDU2MTZlNGM3MTM1NjczMjYyNjk0YjU4NTAzNDUwNDI=';

$Auth;
try {
    $Auth = new Autorizacao($token);
} catch (Exception $e) {
    echo $e->getMessage();
    return;
}
//var_dump($Auth->getId());
//return;
/* 
Transacao avancada
$body = (array) json_decode('{
	"valor":208,
	"para": 921797626,
	"descricao": "uma descricao",
	"tipo": "parcelado",
	"onde": "app",
    "opcoes": "{\"periodicidade\": \"diario\",\"parcelas\": \"2\",\"valor_parcelas\": \"100\"}"
}');

$t = new Enviar(Funcoes::conexao(), new Funcoes());

try {
    //code...
    $t->nova($Auth->getId(), $body["para"], $body["tipo"], $body["onde"], $body["valor"], $body["descricao"], (array)json_decode($body["opcoes"]));
    $envia = $t->commit();
    echo ($envia);
} catch (\Exception $e) {
    
    echo $e->getMessage();
} */


/* 
#transacao normal
$body = (array) json_decode('{
	"valor":1000,
	"para": 921797626,
	"descricao": "uma descricao",
	"tipo": "normal",
	"onde": "app"
}');
try {
    //code...
    $t->nova($Auth->getId(), $body["para"], $body["tipo"], $body["onde"], $body["valor"], $body["descricao"]);
    $envia = $t->commit();
    echo ($envia);
} catch (\Exception $e) {
    
    echo $e->getMessage();
} */



return;

$t = new Transacao(Funcoes::conexao(), new Funcoes());
#$res = $t->verTodosInit("947436662");
#$res = $t->verTodos("921797626","10","2024");
#$res = $t->verDetalhes("1");
#echo json_encode($res);


$e = new Estatistica(Funcoes::conexao(), new Funcoes());
#$res = $e->verTodosInit("6710363e3da27");
#$res = $e->verTodos("6710363e3da27","10","2024");

$p = new Pendente(Funcoes::conexao(), new Funcoes());
//$res = $p->verTodos("921797626");
//$res = $p->verDetalhes("9");
//echo json_encode($res);

$r = new Recorrente(Funcoes::conexao(), new Funcoes());
#$res = $r->verTodos("921797626");
#$res = $r->verDetalhes("1099985634");
#echo json_encode($res);

$c = new Configuracao(Funcoes::conexao(), new Funcoes());
//$res = $c->verPin("671039056e390");
//echo json_encode($res);


$p = new Perfil(Funcoes::conexao(), new Funcoes());
//$res = $p->verDetalhes("6710363e3da0a");
#$res = $p->init("671039056e390");
#echo json_encode($res);
