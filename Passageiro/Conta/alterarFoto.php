<?php
header("Access-Control-Allow-Origin: *");
use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\dbWrapper;


require '../../vendor/autoload.php';

$dados = $_POST;

$funcoes = new Funcoes;

$TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
if($funcoes::tokeniza($TOKEN)){
    $tabela = AX::tb("passageiro");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $arquivo = time()."-".$_FILES['foto']['name'];
    
    $local = "foto/".$arquivo;

    $db = new dbWrapper($funcoes::conexao());

    $res=$db->select(["foto"])
        ->from($tabela)
        ->where(["identificador=$user"])
        ->pegaResultado();

    if($res['foto'] != "default.png"){
        unlink("foto/".$res['foto']);
    }

    $arq = AX::attr($arquivo);
    $db->update($tabela)
        ->set(["foto=$arq"])
        ->where(["identificador=$user"])
        ->executaQuery();

    move_uploaded_file($_FILES['foto']['tmp_name'],$local);

    $return['payload'] = $arquivo;
    $return['ok'] = true;

    echo json_encode($return);

}else{
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
    