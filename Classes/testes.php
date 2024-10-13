<?php

use Ferramentas\Funcoes;
use Classes\Auth;

require '../vendor/autoload.php';


$funcoes = new Funcoes;
$conexao = $funcoes->conexao();
$cadastrar = new Auth($conexao,$funcoes);
$res = $cadastrar->enviaCodigo("921797626");

var_dump($res);

