<?php
namespace Ferramentas;

use Exception;
use Ferramentas\Funcoes;
use Ferramentas\Criptografia;
use FG\ASN1\Universal\Boolean;

class Autorizacao extends Funcoes{
    private $acesso;
    function __construct($token){
        $tk=self::substituiEspacoPorMais($token);
        $eToken = self::Tokeniza($tk);
        if($eToken){
            $this->acesso = self::valid($tk);
            $agora = time();
            $quando = $this->acesso["quando"];

            $diff = $agora-$quando;
            $tempo = floor($diff/60);
            if($tempo >= 5){
                //throw new Exception(json_encode(["ok"=>false, "erro"=>["sms"=>"Tempo expirou","nivel"=>0], "sms"=>"Token invalido"]));
            }
            $this->acesso = self::valid($token);
        }
    }
    public function getConta(){
        return $this->acesso["conta"];
    }
    public function getCliente(){
        return $this->acesso["identificador"];
    }
    public function getId(){
        return $this->acesso["telefone"];
    }
    public function eEmpresa(){
        return (bool) $this->acesso["empresa"];
    }
}
