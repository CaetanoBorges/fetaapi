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
$where = ['telefone'=>$dados['telefone'],'numero'=>$dados['numero']];
foreach($where as $key => $value){
    $where = $key.'='.AX::attr($value);
}

$funcoes = new Funcoes;
$db = new dbWrapper($funcoes::conexao());
$tabelaNumTel = AX::tb('confirmarcadastro');
$tabelaPassageiro = AX::tb('passageiro');

$res = $db->select()
    ->from($tabelaNumTel)
    ->where([$where])
    ->pegaResultados();

if(count($res) > 0){
    $digitos = Funcoes::seisDigitos();

    Funcoes::setRemetente('SIMTAXI');
    Funcoes::enviaSMS($dados['telefone'],"$digitos, use esse número para confirmar o cadastro...");

    $update['palavra_passe'] = $funcoes::fazHash($dados['palavra_passe']);
    foreach($update as $key => $value){
        $update = $key.'='.AX::attr($value);
    }
    $where = ['telefone'=>$dados['telefone']];
    foreach($where as $key => $value){
        $where = $key.'='.AX::attr($value);
    }
    
    $db->update($tabelaPassageiro)
    ->set([$update])
    ->where([$where])
    ->executaQuery();

    $db->delete($tabelaNumTel)
    ->where([$where])
    ->executaQuery();


    $init = new Entrar($db, $tabelaPassageiro, $dados['telefone'], $dados['palavra_passe']);
        if($init->login()){
           

            $credencial['user']=$init->getUser();
            $credencial['telefone']=$init->getTelefone();

            $credencial = json_encode($credencial);
            
            $cript = new Criptografia();
            $chave_sms_real = $cript->fazChave();
            $chave_sms = $cript->criptChave($chave_sms_real);

            $sms = $cript->encrypt($credencial,$chave_sms_real);

            $return['payload'] = $sms.'.'.$chave_sms;
            $return['ok'] = true;



            echo json_encode($return);

      
            //echo $sms.'.'.$chave_sms;
        }else{
            $return['payload'] = "Erro, credenciais errados";
            $return['ok'] = false;

            echo json_encode($return);
        }

}else{
    $return['payload'] = "Erro, dados errados";
    $return['ok'] = false;

    echo json_encode($return);
}
    