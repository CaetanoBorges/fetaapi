<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;

require 'vendor/autoload.php';

$dados = $_POST;


$funcoes = new Funcoes;
$conexao = $funcoes::conexao();

function queryDB($conexao, $sql, $retorna = true)
{
   $query = <<<EOF
     $sql
   EOF;

   $result = pg_query($conexao, $query);
   if($retorna){
      return pg_fetch_all($result);
   }
   return true;
}
   $id7 = 110;
   $id8 = 0;
   $id9 = 9;
   $id10 = 1010;

   $sqlInsert = "INSERT INTO cliente (identificador,empresa) VALUES ($id7, '$id8')";
   $sqlSelect = "select * from cliente";

//pg_query($conexao, 'INSERT INTO cliente (identificador, empresa) VALUES ("pidUiytwe", false)');	

//$result = pg_query($conexao, "select * from cliente");
//var_dump(pg_fetch_all($result));

//$result = Funcoes::queryDB($sqlInsert, false);
$result = Funcoes::queryDB($sqlSelect, true);
var_dump(json_encode($result)); //var_dump($result);