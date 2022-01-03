<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();
$vendedor->cuenta = $_POST['cuenta'];

if(isset($_POST['accion']) && $_POST['accion']=='quitar'){
	$vendedor->id = $_POST['id'];
	$vendedor->referencias_quitar();
}

if(isset($_POST['accion']) && $_POST['accion']=='agregar'){
	$vendedor->nombre 	= $_POST['nombre'];
	$vendedor->relacion = $_POST['relacion'];
	$vendedor->telefono = $_POST['telefono'];
	$vendedor->referencias_agregar();
}

$data =  $vendedor->referencias();
?>
<div class="table-responsive-sm">
	<table class="table">
		<thead>
			<tr class="table-warning">
				<th>REFENCIA</th>
				<th>RELACION</th>
				<th class="text-center" colspan="2">TELEFONO</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(count($data)>0){
				foreach ($data as $datos) {
					$id = $datos['id']."-".$_POST['cuenta'];	
					?>
					<tr>					
						<td><?= $datos['referencia'];?></td>
						<td><?= $datos['relacion'];?></td>
						<td class="text-center"><?= $datos['telefono'];?></td>
						<td><img src="<?= IMAGE.'fail.png';?>" title="Quitar referencia" onclick="referencias_quitar('<?= $id;?>')"></td>
					</tr>
					<?php  
				}
			}
			?>
		</tbody>
	</table>
</div>

<div>
	<h5>Agregar nueva Referencia</h5>

	<div class="input-group input-group-sm mb-2">
		<div class="input-group-prepend">
			<span class="input-group-text" id="inputGroup-sizing-sm">Nombre</span>
		</div>
		<input type="text" class="form-control" id="refencia_nombre" name="refencia_nombre" placeholder="Nombre Referencia">
	</div>

	<div class="input-group input-group-sm mb-2">
		<div class="input-group-prepend">
			<span class="input-group-text" id="">Relación</span>
		</div>
		<input type="text" class="form-control" id="refencia_relacion" name="refencia_relacion" placeholder="Relación">
	</div>

	<div class="input-group input-group-sm mb-3">
		<div class="input-group-prepend">
			<span class="input-group-text" id="">Teléfono</span>
		</div>
		<input type="text" class="form-control" id="refencia_telefono" name="refencia_telefono" placeholder="Teléfono">
	</div>
</div>




