<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;
use Classes\Transacao;
use Classes\Estatistica;
use Classes\Pendente;
use Classes\Recorrente;

require 'vendor/autoload.php';


$t = new Transacao(Funcoes::conexao(), new Funcoes());
#$res = $t->verTodosInit("921797626");
#$res = $t->verTodos("921797626","10","2024");
#$res = $t->verDetalhes("1");


$e = new Estatistica(Funcoes::conexao(), new Funcoes());
#$res = $e->verTodosInit("6710363e3da27");
#$res = $e->verTodos("6710363e3da27","10","2024");

$p = new Pendente(Funcoes::conexao(), new Funcoes());
//$res = $p->verTodos("921797626");
//$res = $p->verDetalhes("9");
//echo json_encode($res);

$r = new Recorrente(Funcoes::conexao(), new Funcoes());
#$res = $r->verTodos("921797626");
$res = $r->verDetalhes("1099985634");
echo json_encode($res);