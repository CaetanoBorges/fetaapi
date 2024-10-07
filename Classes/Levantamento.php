<?php 
namespace Classes;

class Cupom{
    private $db;
    private $tabela;
    function __construct($database,$tabela)
    {
        $this->db = $database;
        $this->tabela = $tabela;

    }

    public function adicionar($array){

    }
    
    public function ver($where){

        $res = $this->db->select()
        ->from($this->tabela)
        ->where($where)
        ->pegaResultado();
        
        return $res;
    }
    public function verTodos(){

    }
    public function verExpirados($array){

    }
    public function verNaoUsados($array){

    }
    public function apagar($array){

    }
    public function usar($array){

    }
    
}