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
$where = ['telefone' => $dados['telefone']];
foreach ($where as $key => $value) {
    $where = $key . '=' . AX::attr($value);
}

$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabelaPassageiro = AX::tb('passageiro');
$tabelaNumTel = AX::tb('confirmarcadastro');


$res = $db->select()
    ->from($tabelaPassageiro)
    ->where([$where])
    ->pegaResultados();

if (count($res) < 1) {

    $return['payload'] = "Erro, não existe um usuário com esse telefone";
    $return['ok'] = false;

    echo json_encode($return);
    return;
}


$digitos = Funcoes::seisDigitos();
$telefone = $dados['telefone'];

Funcoes::setRemetente('SIMTAXI');
Funcoes::enviaSMS($dados['telefone'], "$digitos, use esse número para recuperar a sua conta...");


$res = $db->insert($tabelaNumTel, ['numero' => $digitos, 'telefone' => $telefone])
    ->executaQuery();

$return['payload'] = "Número de recuperação reenviado";
$return['ok'] = true;
echo json_encode($return);
