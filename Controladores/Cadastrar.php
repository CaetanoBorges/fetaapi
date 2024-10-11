<?php

namespace Classes;

use Ferramentas\AX;

class Cadastrar
{
    protected $db;       
    protected $array;
    protected $funcoes;
    protected $tabela;

    public function __construct($db, $funcoes, $tabela, $array) 
    {
        /*
        $arrayStructure = [
            'insert'=>[],
            'where'=>["campo" => $campo],
            'count'=>''
        ]
        */

        $this->db = $db;
        $this->array = $array;
        $this->funcoes = $funcoes;
        $this->tabela = $tabela;
    }

    protected function _check()
    {
        $count = $this->array['count'];
        $where = $this->array['where'];
        
        $res = $this->db->count($count)
        ->from($this->tabela)
        ->where([$where])
        ->pegaResultado();
        if($res[$count] > 0){
            return true;
        }else{
            return false;
        }
        
    }

    public function verifica(){
        $user = $this->_check();
        if ($user) {
            return true;
        }else{
            return false;
        }
    }

    public function cadastrar()
    {
        $insert = $this->array['insert'];
        
        $user = $this->_check();
        if ($user) {
            return false;
        }else{
            $res = $this->novoUser($insert);
            return $res;
        }
        
    }

    public function novoUser($insert)
    {
        $res = $this->db->insert($this->tabela,$insert)
        ->executaQuery();

        if ($res) {
            return true;
        }
        return false;
    }

}