<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'compras.php');
$data = new Compras();
$data->cod_proveedor = $_POST['cod_proveedor'];

$ruta = "../uploads/";

if ( isset($_FILES["file"])) {
	if ($_FILES["file"]["error"] > 0) {
		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

	}
	else {
		if (file_exists($ruta . $_FILES["file"]["name"])) {
			echo $_FILES["file"]["name"] . " el archivo ya existe. ";
		} else {
			$storagename = $_FILES["file"]["name"];
			move_uploaded_file($_FILES["file"]["tmp_name"], $ruta . $storagename);
		}
	}
}



if ( isset($storagename) && $file = fopen( $ruta . $storagename , r ) ) {

	$firstline = fgets ($file, 40096 );
    //Gets the number of fields, in CSV-files the names of the fields are mostly given in the first line
	$num = strlen($firstline) - strlen(str_replace(";", "", $firstline));

    //save the different fields of the firstline in an array called fields
	$fields = array();
	$firstline = str_replace(PHP_EOL, '<p>', $firstline);
	$fields = explode( ";", $firstline, ($num+1) );

	$line = array();
	$i = 0;

        //CSV: one line is one record and the cells/fields are seperated by ";"
        //so $dsatz is an two dimensional array saving the records like this: $dsatz[number of record][number of cell]
	while ( $line[$i] = fgets ($file, 40096) ) {

		$dsatz[$i] = array();
		$dsatz[$i] = explode( ";", $line[$i], ($num+1) );
		$i++;
	}
	?>
	<table class="table table-striped">
		<thead>
			<tr class="bg-warning">
				<th class="align-middle">Codigo Proveedor</th>
				<th class="align-middle">Nombre</th>
				<th class="align-middle">Descripción Larga</th>
				<th class="align-middle">Detalle</th>
				<th class="align-middle">Familia</th>
				<th class="align-middle">Clase</th>
				<th class="align-middle">Precio Costo</th>
				<th class="align-middle">Precio Lista</th>
			</tr>
		</thead>
	<?php
		var_dump($dsatz);
		foreach ($dsatz as $key => $number) {
            //new table row for every record
    			echo "<tr>";
			$data->codigo 		= $number[0]; #codigo
			$data->nombre 		= substr($number[1],0,30); #nombre
			$data->descripcion 	= substr($number[2],0,150); #descripcion
			$data->detalle 		= substr($number[3],0,1024); #detalle
			$data->familia 		= intval(str_replace('.','',$number[4])); #familia
			$data->clase 		= intval(str_replace('.','',$number[5])); #clase
			$data->costo 		= intval(str_replace('.','',$number[6])); #costo
			$data->lista 		= intval(str_replace('.','',$number[7])); #lista
//			$data->procesar_importacion();
			print "<br>";
			foreach ($number as $k => $content) {
                //new table cell for every field of the record
				echo "<td>" . $content . "</td>";
			}
		}
		?>	
	</table>
	<?php

//	$data->finalizar_importacion();

}
?>