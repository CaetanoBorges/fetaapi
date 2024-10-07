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
    $tabelaCarro = AX::tb("carro");
    $tabelaRentacar = AX::tb("rentacar");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $db = new dbWrapper($funcoes::conexao());

    $res=$db->select()
        ->from($tabelaCarro)
        ->pegaResultados();
        
    $resultado=[];
    foreach($res as $carro){
        
        $rentacar = AX::attr($carro['rentacar']);
        $r=$db->select()
        ->from($tabelaRentacar)
        ->where(["identificador=$rentacar"])
        ->pegaResultado();
        $carro['rentacar'] = $r;

        array_push($resultado, $carro);
    }

    $return['payload'] = $resultado;
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    