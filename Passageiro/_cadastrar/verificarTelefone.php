<?php
header("Access-Control-Allow-Origin: *");

error_reporting(0);

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\Cadastrar;
use Classes\Entrar;
use Classes\dbWrapper;

use PHPMailer\PHPMailer\PHPMailer;

require '../../vendor/autoload.php';

$dados = $_POST;


$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabela = AX::tb('passageiro');
$tabelaNumTel = AX::tb('confirmarcadastro');

/**
 * Verifica se email existe na tabela
 */
$where = ['email'=>$dados['email']];
foreach($where as $key => $value){
    $where = $key.'='.AX::attr($value);
}


$res = $db->select()
    ->from($tabela)
    ->where([$where])
    ->pegaResultados();
if(count($res) >= 1){ 
    $return['payload'] = "Erro, ja existe um usuário com esse email";
    $return['ok'] = false;

    echo json_encode($return);
    return;
}


/**
 * Verifica se telefone existe na tabela
 */
$where = ['telefone'=>$dados['telefone']];
foreach($where as $key => $value){
    $where = $key.'='.AX::attr($value);
}



$res = $db->select()
    ->from($tabela)
    ->where([$where])
    ->pegaResultados();

if(count($res) < 1){

    


    $digitos = Funcoes::seisDigitos();

    Funcoes::setRemetente('SIMTAXI');
    Funcoes::enviaSMS($dados['telefone'],"$digitos, use esse número para confirmar o cadastro...");

    $inserir['telefone'] = $dados['telefone'];
    $inserir['numero'] = $digitos;
    $res = $db->insert($tabelaNumTel,$inserir)->executaQuery();

    $return['payload'] = "Prossiga";
    $return['ok'] = true;
    echo json_encode($return);

}else{
    $return['payload'] = "Erro, ja existe um usuário com esse telefone";
    $return['ok'] = false;

    echo json_encode($return);
}
    