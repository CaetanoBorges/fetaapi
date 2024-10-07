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
        ->pegaResultados();

    $tabelaCar = AX::tb("carro");  
    $res = [];
    foreach($resAlug as $aluguer){
        $identificadorCarro = AX::attr($aluguer['carro']);
        $resCar=$db->select()
            ->from($tabelaCar)
            ->where(["identificador=$identificadorCarro"])
            ->pegaResultado();

        $result['aluguer'] =$aluguer;
        $result['carro'] = $resCar;
        array_push($res,$result);
    }
    


    $return['payload'] = $res;
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    