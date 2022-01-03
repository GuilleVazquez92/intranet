<?php 
require('../../controlador/main.php');
require( CONTROLADOR.'carro.php');
require( CONTROLADOR.'clientes.php');

if(isset($_POST['carro']) && isset($_POST['cuenta'])){ 

	$operacion 			= new Operaciones;
	$cliente 			= new Clientes;
	$cliente->cuenta 	= $_POST['cuenta'];
	$operacion->cuenta 	= $_POST['cuenta'];
	$operacion->carro 	= $_POST['carro'];
	$datos_carro 		= $operacion-> buscar_carro();
}
?>
<div class="container-fluid">
	<?Php 
	$datos = $cliente->datos_personales();	
	if(count($datos)>0){
		?>
		<div class="row">
			<h4><?= $datos['cliente'];?></h4>
			<table class="table table-sm">
				<tr>
					<td colspan="3">
						<h4>Datos del Cliente</h4>
					</td>
				</tr>
				<tr class="table-warning">
					<td>Documento</td>
					<td colspan="2"><?= $datos['documento'];?></td>
				</tr>
				<tr>
					<td>Dirección</td>
					<td colspan="2"><?= $datos['direccion'];?></td>
				</tr>
				<tr>
					<td>Telefono</td>
					<td colspan="2"><?= $datos['telefono'];?></td>
				</tr>
				<tr>
					<td>Celular</td>
					<td colspan="2"><?= $datos['celular'];?></td>
				</tr>
				<tr>
					<td>Ciudad</td>
					<td colspan="2"><?= $datos['ciudad'];?></td>
				</tr>
				<tr>
					<td>Barrio</td>
					<td colspan="2"><?= $datos['barrio'];?></td>
				</tr>
				<?php  
			}
			$datos_cabecera = $operacion-> carro_cabecera();
			?>	
			<tr>
				<td colspan="3">
					<h4>Datos Compra</h4>		
				</td>
			</tr>
			<tr class="table-warning">
				<td>Cuenta</td>
				<td colspan="2"><?= $datos['cuenta'];?></td>
			</tr>
			<tr>
				<td>Carro</td>
				<td colspan="2"><?= $operacion->carro ;?></td>
			</tr>
			<tr>
				<td>Factura</td>
				<td colspan="2"><?= $datos_cabecera['factura']; ?></td>
			</tr>
			<tr>
				<td>Fecha Factura</td>
				<td colspan="2"><?= $datos_cabecera['fecha_factura'];?></td>
			</tr>

			<tr class="table-info">
				<td>Cod.</td>
				<td>Descripción</td>
				<td>Cant.</td>
			</tr>
			<?php  
			$datos_detalle  = $operacion-> carro_detalle();
			for ($i=0; $i < count($datos_detalle); $i++) { 
				?>		
				<tr>
					<td><?= $datos_detalle[$i]['codigo']; ?></td>
					<td><?= $datos_detalle[$i]['descripcion']; ?></td>
					<td class="text-center"><?= $datos_detalle[$i]['cantidad']; ?></td>
				</tr>
				<?php 
			} 
			?>
		</table>
	</div>
</div>

