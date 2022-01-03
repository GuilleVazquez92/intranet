<?php 



$arrayName = array('auto' => 1, 'moto' => 2);

$obj =  json_encode($arrayName);
$php = json_decode($obj);

print_r($php);

echo $php->{'auto'}; # imprime "este es el valor de algo"


 ?>