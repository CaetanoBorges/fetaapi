<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use Classes\dbWrapper;
use Classes\Cupom;


require '../../vendor/autoload.php';

$dados = $_POST;

       
if (isset($dados['token'])) {


    $funcoes = new Funcoes;
    $db = new dbWrapper($funcoes::conexao());
    $tabela = AX::tb('classificacao');
    $TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);

    if ($funcoes::tokeniza($TOKEN)) {
        $acesso = $funcoes::valid($TOKEN);    
        $user = AX::attr($acesso['user']);
        $motorista = AX::attr($dados['motorista']);
        $voto = AX::attr(0);
        $res = $db->select()
                ->from($tabela)
                ->where(["classificador=$user","motorista=$motorista"])
                ->pegaResultados();

            
        if(count($res) > 0){
            if($dados["voto"] == "1"){
                $voto = AX::attr(1);
            }
            //var_dump($voto);
            $res = $db->update($tabela)
                ->set(["voto=$voto"])
                ->where(["classificador=$user","motorista=$motorista"])
                ->executaQuery();
        }else{
            if($dados["voto"] == "1"){
                $voto = AX::attr(1);
            }
            $res = $db->insert($tabela,[
                "classificador"=>$user,
                "motorista"=>$motorista,
                "voto"=>$voto])
                ->executaQuery();
        }

        $return['payload'] = "";
        $return['ok'] = true;
        echo json_encode($return);

    } else {
        $return['payload'] = "Erro";
        $return['ok'] = false;

        echo json_encode($return);
    }
}
