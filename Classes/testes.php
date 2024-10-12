<?php

use Ferramentas\Funcoes;
use Classes\Cadastrar;

require '../vendor/autoload.php';


$funcoes = new Funcoes;
$conexao = $funcoes->conexao();
$cadastrar = new Cadastrar($conexao,$funcoes);
$res = $cadastrar->enviaCodigo("921797626");

var_dump($res);

