<?php
header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\dbWrapper;

require '../../vendor/autoload.php';

$dados = $_POST;
$funcoes = new Funcoes;
$TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
if ($funcoes::tokeniza($TOKEN)) {

    $tabela = AX::tb("aluguer");
    $db = new dbWrapper($funcoes::conexao());
    $acesso = $funcoes::valid($TOKEN);
    $user = $acesso['user'];

    $identificador = ['identificador' => Funcoes::chaveDB()];
    $cliente = ['cliente' => $user];
    $quando = ['quando' => date("Y-m-d H:i:s")];

    unset($dados['token']);

    $insert = array_merge($identificador, $cliente, $quando, $dados);

    $in = [];
    foreach ($insert as $key => $value) {
        $in[$key] = AX::attr($value);
    }

    $res = $db->insert($tabela, $in)
        ->executaQuery();

    $return['payload'] = $res;
    $return['ok'] = true;
    echo json_encode($return);

} else {

    $return['payload'] = "Erro";
    $return['ok'] = false;
    echo json_encode($return);

}