<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\Cadastrar;
use Classes\Entrar;
use Classes\dbWrapper;

use PHPMailer\PHPMailer\PHPMailer;

require '../vendor/autoload.php';

$dados = $_POST;


$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabela = AX::tb('passageiro');


$init = new Entrar($db, $tabela, $dados['telefone'], $dados['palavra_passe']);
if($init->login()){
   

    $credencial['user']=$init->getUser();
    $credencial['telefone']=$init->getTelefone();

    $credencial = json_encode($credencial);
    
    $cript = new Criptografia();
    $chave_sms_real = $cript->fazChave();
    $chave_sms = $cript->criptChave($chave_sms_real);

    $sms = $cript->encrypt($credencial,$chave_sms_real);

    $return['payload'] = $sms.'.'.$chave_sms;
    $return['ok'] = true;



    echo json_encode($return);

      
    //echo $sms.'.'.$chave_sms;
}else{

    $return['payload'] = "Erro, credenciais errados";
    $return['ok'] = false;

    echo json_encode($return);
}
