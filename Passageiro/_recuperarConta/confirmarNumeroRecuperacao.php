<?php
header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\Cadastrar;
use Classes\Entrar;
use Classes\dbWrapper;
use PHPMailer\PHPMailer\PHPMailer;


require '../../vendor/autoload.php';

$dados = $_POST;

$where = [];
foreach($dados as $key => $value){
    array_push($where, $key.'='.AX::attr($value));
}

$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabela = AX::tb('confirmarcadastro');

$res = $db->select()
    ->from($tabela)
    ->where($where)
    ->pegaResultados();

if(gettype($res) != "array"){
    $return['payload'] = "Erro, número de confirmação errado";
    $return['ok'] = false;

    echo json_encode($return);
    return;
}

if(count($res) > 0){

  
    $return['payload'] = "Confirmado... Conclua criando a palavra passe";
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro, número de confirmação errado";
    $return['ok'] = false;

    echo json_encode($return);
}
    