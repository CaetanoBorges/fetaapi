<?php
header("Access-Control-Allow-Origin: *");
use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\dbWrapper;


require '../../vendor/autoload.php';

$dados = $_GET;

$funcoes = new Funcoes;

$TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
if($funcoes::tokeniza($TOKEN)){
    $tabela = AX::tb("carro");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $db = new dbWrapper($funcoes::conexao());
    $identificador = AX::attr($dados['identificador']);
    $resCar=$db->select()
        ->from($tabela)
        ->where(["identificador=$identificador"])
        ->pegaResultado();

    $tabelaRent = AX::tb("rentacar");  
    $identificadorRent = AX::attr($resCar['rentacar']);
    $resRent=$db->select(['nome','sobre'])
        ->from($tabelaRent)
        ->where(["identificador=$identificadorRent"])
        ->pegaResultado();

    $res = array_merge($resCar,$resRent);


    $return['payload'] = $res;
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    