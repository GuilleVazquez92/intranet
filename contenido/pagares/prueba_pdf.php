<?php

require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

$file = $data->ver_pdf(495,2405474,1);
$file = base64_decode($file[0][0],true);
header("Content-type:application/pdf");
print $file;

?>
