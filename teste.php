<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;
use Classes\Transacao;

require 'vendor/autoload.php';


$t = new Transacao(Funcoes::conexao(), new Funcoes());
$res = $t->verTodosInit("921797626");

echo json_encode($res);