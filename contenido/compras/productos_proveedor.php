<?php 
if(isset($_POST['cod_proveedor'])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'compras.php');
	$data = new Compras();
	$data->cod_proveedor = $_POST['cod_proveedor'];
}
?>
<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr class="bg-warning">
				<th class="align-middle">Cod.FL</th>
				<th class="align-middle">Cod.Prov</th>
				<th class="align-middle">Nombre</th>
				<th class="align-middle">Descripción</th>
				<th class="align-middle">Detalle</th>
				<th class="align-middle">Familia</th>
				<th class="align-middle">Clase</th>
				<th class="align-middle">Precio Costo</th>
				<th class="align-middle">Precio Lista</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$datos = $data->consultar_proveedor();
			foreach ($datos as $key) {
				?>
				<tr>
					<td><?= $key['codigo'];?></td>
					<td><?= $key["cod_producto"];?></td>
					<td><?= $key["nombre"];?></td>
					<td><?= $key["descripcion_larga"];?></td>
					<td><?= $key["detalle"];?></td>
					<td><?= $key["familia"];?></td>
					<td><?= $key["clase"];?></td>
					<td class="text-center"><?= number_format($key["precio_costo"],0,',','.');?></td>
					<td class="text-center"><?= number_format($key["precio_lista"],0,',','.');?></td>
				</tr>
				<?php 
			}
			?>
		</tbody>
	</table>
</div>