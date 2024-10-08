<?php

header("Access-Control-Allow-Origin: *");

use Ferramentas\Funcoes;

require 'vendor/autoload.php';

$dados = $_POST;


$funcoes = new Funcoes;
$conexao = $funcoes::conexao();

   $sql =<<<EOF
      INSERT INTO cliente (identificador,empresa)
      VALUES (1, false);

      INSERT INTO cliente (identificador,empresa)
      VALUES (2, true);     
      
      INSERT INTO cliente (identificador,empresa)
      VALUES (3, true);      
      
      INSERT INTO cliente (identificador,empresa)
      VALUES (4, false);
EOF;

pg_query($conexao, $sql);
//pg_query($conexao, 'INSERT INTO cliente (identificador, empresa) VALUES ("pidUiytwe", false)');	

$result = pg_query($conexao, "select * from cliente");
var_dump(pg_fetch_all($result));