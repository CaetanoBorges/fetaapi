<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;
use Classes\Transacao;
use Classes\Estatistica;

require 'vendor/autoload.php';


$t = new Transacao(Funcoes::conexao(), new Funcoes());
#$res = $t->verTodosInit("921797626");
#$res = $t->verTodos("921797626","10","2024");
#$res = $t->verDetalhes("7");


$e = new Estatistica(Funcoes::conexao(), new Funcoes());
#$res = $e->verTodosInit("6710363e3da27");
$res = $e->verTodos("6710363e3da27","10","2024");
#$res = $e->verDetalhes("7");
echo json_encode($res);