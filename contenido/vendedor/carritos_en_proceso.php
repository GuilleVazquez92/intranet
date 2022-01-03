<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');


require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Carritos en proceso Tienda</li>
		</ol>
	</nav>

	<table class="table table-sm">
		<thead>
			<tr class="table-warning">
				<th>Cuenta</th>
				<th>Cliente</th>
				<th>Teléfono</th>
				<th>Fecha</th>
				<th>Estado</th>
				<th>Producto</th>
			</tr>
		</thead>
		<?php

		$url = 'https://www.facilandia.com.py/api/v1/carritos?cod_vend=';
		$cod_vend = $_COOKIE['rol'];

		$json  = file_get_contents($url.$cod_vend);
		$data = json_decode($json, true);
		for ($i=0; $i < count($data); $i++) {

			$producto = $vendedor->consultar_producto($data[$i]['producto_cod']);
			$detalle = (isset($producto[0]['epdescl'])) ? $producto[0]['epdescl'] : "";
			?>
			<tr>
				<td><?= $data[$i]['cuenta'];?></td>
				<td><?= $data[$i]['nombre'].' '.$data[$i]['apellido'];?></td>
				<td><?= $data[$i]['telefono'];?></td>
				<td>
					<?php 
					$fecha = str_replace('T', ' ', explode('.', $data[$i]['fecha_modificacion']));
					echo $fecha[0];
					?>
				</td>
				<td><?= $data[$i]['estado'];?></td>
				<td><?= $data[$i]['producto_cod'].' - '.$detalle;?></td>
			</tr>
			<?php
		}
		?>
	</table>
</div>
<?php 
require('../../footer.php');
?>
<script>
	function formato_fecha_hora(dato){

		var fecha = dato.split('T');
		var hora = fecha[1].split('.');
		return fecha[0]+' '+hora[0];
	};
</script>
