<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;

require 'vendor/autoload.php';

$dados = $_POST;

$funcoes = new Funcoes;
$conexao = $funcoes::conexao();

$query = $conexao->prepare("select * from cliente");
$query->execute();
$result = $query->fetchAll(PDO::FETCH_ASSOC);

var_dump(($result)); //var_dump($result);

foreach($result as $res){
   echo $res["identificador"]."\n";
   if($res["empresa"]){
      echo "Empresa \n";
   }

}