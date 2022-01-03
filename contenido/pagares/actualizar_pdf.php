<?php
require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

$operaciones = $data->operaciones_sin_pdf();
$i = 0;
echo count($operaciones);
echo "<br>";
foreach ($operaciones as $datos) {
	$data->actualizar_pdf($datos['lote'],$datos['operacion'],$datos['tipo']);
	$i++;
}
echo "listo";
?>