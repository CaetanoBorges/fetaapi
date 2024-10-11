<?php

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\Cadastrar;
use Classes\dbWrapper;

require '../vendor/autoload.php';

$dados = [
    'nome'=>'Um nome',
    'telefone'=>'943124678',
    'palavra_passe'=>'yuytewtruweuqu'
];

$extra = ['extra'=>Funcoes::seisDigitos()];
$identificador = ['identificador'=>Funcoes::chaveDB()];
$insert = array_merge($identificador,$dados,$extra);


//DONE
$where = ['telefone'=>$dados['telefone']];
foreach($where as $key => $value){
    $where = $key.'='.AX::attr($value);
}
$count = 'telefone';

foreach($insert as $key => $value){
    $insert[$key] = AX::attr($value);
}



$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabela = AX::tb('administrador');
$arrayStructure['insert'] = $insert;
$arrayStructure['where'] = $where;
$arrayStructure['count'] = $count;

$cadastrar = new Cadastrar($db,$funcoes,$tabela,$arrayStructure);
$res = $cadastrar->cadastrar();

var_dump($res);

