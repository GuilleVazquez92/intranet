<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');

$vendedor = new Vendedores();
$vendedor->cuenta = $_POST['cuenta'];
$data = $vendedor->verificaciones();

if(count($data)>0){
	$i = 0;
	foreach ($data as $datos) {
		$id = "verificacion".$i;
		$fecha = $datos['fecha'];	
		?>
		<p>
			<button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#<?= $id;?>" aria-expanded="false" aria-controls="<?= $id;?>" onclick="mostrar(<?= $id;?>);">
				<?= $fecha;?>
			</button>
			<br>
			<h6><?= $datos['gestor']; ?></h6>
			<?= $datos['verificacion']; ?>

		</p>
		<div class="collapse mb-3 multi-collapse" id="<?= $id;?>">
			<div class="table-responsive">
				<table class="table table-sm table-bordered table-striped">
					<tr class="table-warning">
						<th>Tipo Ref.</th>
						<th>Nombre</th>
						<th>Teléfono</th>
						<th>Verificado</th>
					</tr>
					<?php
					$detalle = $vendedor->verificaciones_detalle($fecha);
					for ($x=0; $x < count($detalle); $x++) { 
						?>
						<tr>
							<td><?= $detalle[$x]['tipo'];?></td>
							<td nowrap><?= $detalle[$x]['nombre'];?></td>
							<td><?= $detalle[$x]['telefono'];?></td>
							<td class="text-center"><?= $detalle[$x]['verificado'];?></td>
						</tr>
						<tr>
							<td colspan="4"><?= $detalle[$x]['detalle'];?></td>
						</tr>		
						<?php		
					}
					?>			
				</table>					
			</div>
		</div>
		<?php
		$i++;
	}	
}
?>
<script>
	function mostrar(id){

		$('.multi-collapse').collapse('hide');
	}
</script>
