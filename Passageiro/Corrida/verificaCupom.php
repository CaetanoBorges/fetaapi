<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\dbWrapper;
use Classes\Cupom;


require '../../vendor/autoload.php';

$dados = $_POST;

if(isset($dados['token'])){
    

    $funcoes = new Funcoes;
    $db = new dbWrapper($funcoes::conexao());
    $tabela = AX::tb('cupom');
    $Cupom = new Cupom($db,$tabela);
    $TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
    
    if($funcoes::tokeniza($TOKEN)){

        $conexao = $funcoes::conexao();
        $acesso = $funcoes::valid($TOKEN);

        //DONE
        $where = ['identificador'=>$dados['identificador']];
        foreach($where as $key => $value){
            $where = $key.'='.AX::attr($value);
        }

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
        

    }else{
        $return['payload'] = "Erro";
        $return['ok'] = false;

        echo json_encode($return);

    }

}