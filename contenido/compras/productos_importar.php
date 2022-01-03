<?php 

function csv2Array($csvFile, $numberCols, $csvDelimiter = ";") {
	if (($contents = $csvFile) === false) {
		return false;
	}
	$fixCol = 0;
	$result = array();
	$toArray = array();
	while ($contents){
		$word = "";
		$delimiter = (++$fixCol % $numberCols) ? $csvDelimiter : "\r";
		$position = -1;
		do {
			if(($position = strpos($contents, $delimiter, ++$position)) === false) $position = strlen($contents);
			$word = substr($contents, 0, $position);
			$x = substr_count($word, '"') % 2;
			$position;
		} while ($x) ;
		if (($fixCol % $numberCols) == 1) $toArray = array($word); else $toArray[] = $word;
		if ($fixCol % $numberCols == 0) $result[] = $toArray;
		$contents = substr($contents, $position+1);
	}
	if ($fixCol % $numberCols != 0) $result[] = $toArray;
	return $result;
}



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

if(isset($storagename)){

	$file 	= file_get_contents($ruta.$storagename);
	$key 	= csv2Array($file,8,";");
	
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
		<tbody>
			<?php

			for ($i=1; $i < count($key)-1; $i++) { 

				$data->codigo 		= $key[$i][0]; #codigo
				$data->nombre 		= substr($key[$i][1],0,30); #nombre
				$data->descripcion 	= substr($key[$i][2],0,150); #descripcion
				$data->detalle 		= substr($key[$i][3],0,1024); #detalle
				$data->familia 		= intval(str_replace('.','',$key[$i][4])); #familia
				$data->clase 		= intval(str_replace('.','',$key[$i][5])); #clase
				$data->costo 		= intval(str_replace('.','',$key[$i][6])); #costo
				$data->lista 		= intval(str_replace('.','',$key[$i][7])); #lista
				?>
				<tr>
					<td><?= $data->codigo;?></td>
					<td><?= $data->nombre;?></td>
					<td><?= $data->descripcion;?></td>
					<td><?= $data->detalle;?></td>
					<td><?= $data->familia;?></td>
					<td><?= $data->clase;?></td>
					<td><?= $data->costo;?></td>
					<td><?= $data->lista;?></td>
					</td>
				</tr>
				<?php	
			$data->procesar_importacion();
			}
			?>				
		</tbody>
	</table>
	<?php
	$data->finalizar_importacion();
}
?>