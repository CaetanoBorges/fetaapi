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
$where = ['telefone'=>$dados['telefone']];
foreach($where as $key => $value){
    $where = $key.'='.AX::attr($value);
}

$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabelaNumTel = AX::tb('confirmarcadastro');

$res = $db->select()
    ->from($tabelaNumTel)
    ->where([$where])
    ->pegaResultados();

if(count($res) > 0){
    $digitos = Funcoes::seisDigitos();

    Funcoes::setRemetente('SIMTAXI');
    Funcoes::enviaSMS($dados['telefone'],"$digitos, use esse número para confirmar o cadastro...");

    $update['numero'] = $digitos;
    foreach($update as $key => $value){
        $update = $key.'='.AX::attr($value);
    }

    $res = $db->update($tabelaNumTel)
    ->set([$update])
    ->where([$where])
    ->executaQuery();

    $return['payload'] = "Número de confirmação reenviado";
    $return['ok'] = true;
    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    