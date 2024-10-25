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

require 'vendor/autoload.php';

$token = 'mU9rDW+N2P8WoP8E4fObtBID4ss+4CJnaB71BQ3KzDezaFVKII5lrcLH/Jdu2puRtgrTO2ec2EZKIYwF7ibe0cVSYmPfPQ8g55xGYCeKVPZOBbhz9XRRenUqEKMcQCQL8r+OfVqu4k8D+XG6f61rbitTGKobR4IukQziOOaa7yBRcQ==.NTA3ODVhNmE2NzQ0MzA1NTVhNGI2NDQ2NDczNzZhMzU1ODYzNTM3NTczNmIzOTY2NTA1NzMwNGIzNTRkNTU0ZDMwNDg3MDMwNTM0MjZiNTc=';

$Auth;
try{
    $autorizacao = new Autorizacao($token);
    $Auth = $autorizacao;
}catch(Exception $e){
    echo $e->getMessage();
    return;
}
#var_dump($Auth->eEmpresa());
#return;

$t = new Transacao(Funcoes::conexao(), new Funcoes());
#$res = $t->verTodosInit("947436662");
#$res = $t->verTodos("921797626","10","2024");
#$res = $t->verDetalhes("1");
$res = $t->verificaSaldo(1,"671039056e3cc","30");
echo json_encode($res);


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