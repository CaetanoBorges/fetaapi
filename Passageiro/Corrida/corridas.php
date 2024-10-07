<?php
header("Access-Control-Allow-Origin: *");
error_reporting(1);

use Ferramentas\AX;
use Ferramentas\Funcoes;
use Classes\dbWrapper;


require '../../vendor/autoload.php';


$dados = $_GET;

$funcoes = new Funcoes;

$TOKEN = $funcoes::substituiEspacoPorMais($dados['token']);
if ($funcoes::tokeniza($TOKEN)) {
    $tabela = AX::tb("corrida");
    $acesso = $funcoes::valid($TOKEN);
    $user = AX::attr($acesso['user']);

    $db = new dbWrapper($funcoes::conexao());
    $resCorrida = $db->select()
        ->from($tabela)
        ->where(["passageiro=$user", "motorista!=''"])
        ->pegaResultados();

    $tabelaMotorista = AX::tb("motorista");
    $tabelaPassageiro = AX::tb("passageiro");
    $tabelaClassificacao = AX::tb("classificacao");
    $tabelaChat = AX::tb("chat");
    $tabelaVeiculo = AX::tb("veiculo");
    $res = [];
    foreach ($resCorrida as $corrida) {
        $identificadorMotorista = AX::attr($corrida['motorista']);
        $resMotorista = $db->select()
            ->from($tabelaMotorista)
            ->where(["identificador=$identificadorMotorista"])
            ->pegaResultado();

        $resPassageiro = $db->select()
            ->from($tabelaPassageiro)
            ->where(["identificador=$user"])
            ->pegaResultado();

        $resVeiculo = $db->select()
            ->from($tabelaVeiculo)
            ->where(["motorista=$identificadorMotorista"])
            ->pegaResultado();


        $resClassificacao = $db->select()
            ->from($tabelaClassificacao)
            ->where(["motorista=$identificadorMotorista"])
            ->pegaResultados();

        $classificacao = [];
        foreach ($resClassificacao as $classif) {
            if ($classif["classificador"] == $corrida['passageiro']) {
                $classif["classificador"] = 'eu';
            }
            array_push($classificacao, $classif);
        }
        $classificacaoFinal = Funcoes::classificacaoMotorista($classificacao);

        $identificadorCorrida = AX::attr($corrida['identificador']);
        $resChat = $db->select()
            ->from($tabelaChat)
            ->where(["corrida=$identificadorCorrida"])
            ->pegaResultados();

        $chatt = [];

        foreach ($resChat as $chat) {
            if ($chat["emissor"] == $corrida['motorista']) {
                $chat["emissor"] = '<b>' . $resMotorista["nome"] . "</b> (Motorista)";
                $chat["eu"] = false;
            }
            if ($chat["emissor"] == $corrida['passageiro']) {
                $chat["emissor"] = '<b>' . $resPassageiro["nome"] . "</b> (Eu)";
                $chat["eu"] = true;
            }
            array_push($chatt, $chat);
        }


        $result['corrida'] = $corrida;
        $result['motorista'] = $resMotorista;
        $result['chat'] = $chatt;
        $result['veiculo'] = $resVeiculo;
        $result['classificacao'] = $classificacaoFinal;
        array_push($res, $result);
    }



    $return['payload'] = $res;
    $return['ok'] = true;

    echo json_encode($return);
} else {
    $return['payload'] = "Erro";
    $return['ok'] = false;

    echo json_encode($return);
}
