<?php
namespace Classes;

use Ferramentas\AX;
use Ferramentas\Funcoes;

class Recuperar
{
    protected $db;       

    public function __construct($pdo) 
    {
       $this->db = $pdo;
    }

    public function verificaEmail($email)
    {
        $mail = AX::attr($email);

        $res = $this->db->count("email")
        ->from(AX::tb("conta"))
        ->where(["email = $mail"])
        ->pegaResultado();

        if($res['email'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function selecionaNumeroDeRecuperacao($email, $codigo)
    {
        $cod = AX::attr($codigo);
        $mail = AX::attr($email);

        $res = $this->db->update("conta")
        ->set(["codigo_renova = $cod"])
        ->where(["email = $mail"])
        ->executaQuery();

        if ($res) {
            return true;
        }
        return false;
    }

    public function verificaNumeroEEmail($email, $codigo)
    {
        $cod = AX::attr($codigo);
        $mail = AX::attr($email);

        $res = $this->db->count("email")
        ->from(AX::tb("conta"))
        ->where(["email = $mail","codigo_renova = $cod"])
        ->pegaResultado();

        if($res['email'] > 0){
            return true;
        }else{
            return false;
        }
    } 

    public function resetCodigoRenovacao($email, $codigo){

        $cod = AX::attr($codigo);
        $mail = AX::attr($email);

        $res = $this->db->update(AX::tb("conta"))
        ->set(["codigo_renova = 0"])
        ->where(["email = $mail","codigo_renova = $cod"])
        ->executaQuery();
    
    }
    public function pegaPalavraPasseEsquecida($email){

        $mail = AX::attr($email);

        $res = $this->db->select()
        ->from("conta")
        ->where(["email = $mail"])
        ->pegaResultado();

        return $res['palavra_passe'];
    }
    public function atualizaHistoricoPalavraPasse($user,$passAtual){
        $us = AX::attr($user);
        $pass = AX::attr($passAtual);
        $time = AX::attr(time());

        $res = $this->db->insert("historicopalavrapasse",["chave_user" => $us, "palavra_passe" => $pass, "quando" => $time])
        ->executaQuery();

    }
    public function novaPasse($email, $codigo, $palavra_passe)
    {   
        $mail = AX::attr($email);
        $cod = AX::attr($codigo);
        $pass = AX::attr(Funcoes::fazHash($palavra_passe));

        $res = $this->db->update("conta")
        ->set(["palavra_passe = $pass"])
        ->where(["email = $mail","codigo_renova = $cod"])
        ->executaQuery();

        if($res){
            $this->resetCodigoRenovacao($email, $codigo);
            return true;
        }else{
            return false;
        }
    }
    
    public function alteraPasse($user, $palavra_passe)
    {
        $us = AX::attr($user);
        $pass = AX::attr(Funcoes::fazHash($palavra_passe));

        $res = $this->db->update("conta")
        ->set(["palavra_passe = $pass"])
        ->where(["chave = $us"])
        ->executaQuery();
        if($res){
            return true;
        }else{
            return false;
        }
    }

}