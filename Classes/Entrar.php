<?php
namespace Classes;

use Ferramentas\AX;
use Ferramentas\Funcoes;

class Entrar
{
    protected $telefone;    
    protected $password; 

    protected $db;  
    protected $tabela;        
    protected $user;     

    public function __construct($db, $tabela, $telefone, $password) 
    {
       $this->db = $db;
       $this->telefone = $telefone;
       $this->tabela = $tabela;
       $this->password = Funcoes::fazHash($password);
    }

    public function login()
    {
        $user = $this->_checkCredentials();
        if ($user) {
            return $user;
        }
        return false;
    }

    protected function _checkCredentials()
    {
        $pass = AX::attr($this->password);
        $telefone = AX::attr($this->telefone);

        $user = $this->db->select()
        ->from($this->tabela)
        ->where(["telefone = $telefone", "palavra_passe = $pass"])
        ->pegaResultado();
        
        if($user){
            if (count($user) > 1) {
                $this->user = $user;
                return true;
            } 
        }
        return false;
    }

    public function getUser()
    {
        return $this->user['identificador'];
    }
    public function getTelefone()
    {
        return $this->user['telefone'];
    }
}