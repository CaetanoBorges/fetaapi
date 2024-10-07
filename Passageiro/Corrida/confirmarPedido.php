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

$where = [];
foreach($dados as $key => $value){
    array_push($where, $key.'='.AX::attr($value));
}

$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabela = AX::tb('confirmarCorrida');

$res = $db->select()
    ->from($tabela)
    ->where($where)
    ->pegaResultados();

if(gettype($res) != "array"){
    $return['payload'] = "Erro, número de confirmação errado";
    $return['ok'] = false;

    echo json_encode($return);
    return;
}

if(count($res) > 0){

    $telefone['telefone'] = $dados['telefone'];
    $where = [];
    foreach($dados as $key => $value){
        array_push($where, $key.'='.AX::attr($value));
    }

    $res = $db->delete($tabela)
    ->where($where)
    ->executaQuery();

    $TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
    
    if($funcoes::tokeniza($TOKEN)){

        $acesso = $funcoes::valid($TOKEN);


        $res = $Cupom->ver([$where]);
        $resTipo = gettype($res);

        if($resTipo == "array"){
            if(count($res)>0){
                $return['payload'] = $res['valor'];
                $return['ok'] = true;
                echo json_encode($return);
            }else{
                $return['payload'] = "Cupom invalido";
                $return['ok'] = false;
                echo json_encode($return);
            }
        }
        if($resTipo == "boolean"){
            if($res){
                $return['payload'] = $res['valor'];
                $return['ok'] = true;
                echo json_encode($return);
            }else{
                $return['payload'] = "Cupom invalido";
                $return['ok'] = false;
                echo json_encode($return);
            }
        }
        

    }





}else{
    $return['payload'] = "Erro, número de confirmação errado";
    $return['ok'] = false;

    echo json_encode($return);
}
    