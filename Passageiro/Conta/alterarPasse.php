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

    $tabela = AX::tb("passageiro");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $db = new dbWrapper($funcoes::conexao());

    $res = $db->select(["palavra_passe"])
        ->from($tabela)
        ->where(["identificador=$user"])
        ->pegaResultado();

    $nova = $funcoes::fazHash($dados['nova']);
    $atual = $funcoes::fazHash($dados['atual']);
    if ($res['palavra_passe'] != $atual) {
        $return['payload'] = "Palavra passe antiga nao condiz";
        $return['ok'] = false;

        echo json_encode($return);
        return;
    }

    $nov = AX::attr($nova);
    $db->update($tabela)
        ->set(["palavra_passe=$nov"])
        ->where(["identificador=$user"])
        ->executaQuery();


    $return['payload'] = "Atualizou";
    $return['ok'] = true;

    echo json_encode($return);

} else {
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
