<?php
header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\Passageiro;
use Classes\dbWrapper;


require '../../vendor/autoload.php';

$dados = $_POST;

//
//$dados["token"] = 'TI1e39NQ9xrYnNRt16SnmwGNj5GKYZUYVkJwEBIc7pSlXsxAMrXypR3uts6lBlbHlWPXn+Hea4luwAW3ECvy.NmI0NTMzNzU0MTQ1NmY0MzY5NmY1MTcxMzUzMjM1NzY2ZTZhNTc2ODZmNmQ2MzZkNzk1YTZiNGM3YTY1MzMzMTQyNGM2Zjc5MzE2NDZlNGI=';
//$dados["nome"] = 'Nome';
//$dados["telefone"] = 'telefone';
//$dados["email"] = 'email';
//$dados["genero"] = 'genero';
//$dados["provincia"] = 'provincia';
//$dados["municipio"] = 'municipio';

$funcoes = new Funcoes;

$TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
if($funcoes::tokeniza($TOKEN)){
    $tabela = AX::tb("passageiro");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);
    unset($dados['token']);

    $set = [];
    foreach($dados as $key => $value){
        array_push($set, $key.'='.AX::attr($value));
    }

    $db = new dbWrapper($funcoes::conexao());
    $db ->update($tabela)
        ->set($set)
        ->where(["identificador=$user"])
        ->executaQuery();

    

    $return['payload'] = "Alterou os detalhes de conta";
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);

    
}
    