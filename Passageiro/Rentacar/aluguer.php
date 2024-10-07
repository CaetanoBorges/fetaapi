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
    $tabela = AX::tb("aluguer");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $db = new dbWrapper($funcoes::conexao());
    $resAlug=$db->select()
        ->from($tabela)
        ->where(["cliente=$user"])
        ->pegaResultado();

    $tabelaCar = AX::tb("carro");  
    $identificadorCarro = AX::attr($dados['carro']);
    $resCar=$db->select()
        ->from($tabelaCar)
        ->where(["identificador=$identificadorCarro"])
        ->pegaResultado();

    $res = array_merge($resCar,$resAlug);


    $return['payload'] = $res;
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    