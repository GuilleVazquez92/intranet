<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$data  = new Vendedores;
$data->cuenta = $_POST['cuenta'];
?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="cartera.php">Cartera</a></li>
			<li class="breadcrumb-item"><a href="gestion.php">Gestión</a></li>
			<li class="breadcrumb-item active" aria-current="page">Operaciones</li>
		</ol>
	</nav>	
	<h4><?= $_POST['cuenta']." ".$_POST['cliente'] ;?></h4>
	<div class="table-responsive-sm">
		<table class="table table-sm table-striped">
			<?php 
			$var = 99;
			for ($x=0; $x < 4 ; $x++) { 
				switch ($x) {
					case 0:
					$titulo = 'EN PROCESO';
					$variable = 'proceso';
					break;
					case 1:
					$titulo = 'FACTURADAS';
					$variable = 'facturadas';							
					break;
					case 2:
					$titulo = 'CANCELADAS';
					$variable = 'canceladas';							
					break;
					case 3:
					$titulo = 'OTRAS';
					$variable = 'otras';							
					break;																					
				}

				$proceso = $data->operaciones($variable);
				if(count($proceso)>0){
					?>
					<tr class="bg-warning">
						<td colspan="13">
							<h4 class="card-title"><?= $titulo;?></h4>
						</td>
					</tr>
					<tr class="table-warning">
						<th class="text-center">Operación</th>
						<th class="text-left">Cliente</th>
						<th class="text-center">Tipo</th>
						<th class="text-center">Estado</th>
						<th class="text-center">Atraso</th>
						<th class="text-center">Total</th>
						<th class="text-center">Cuota</th>
						<th class="text-center">Cant</th>
						<th class="text-center">Pend</th>
						<th class="text-center">Saldo</th>
						<th class="text-center">Calif</th>
						<th class="text-center">Vend</th>
						<th class="text-center">FechaVal</th>
					</tr>
					<tbody>
						<?php 
						foreach ($proceso as $operacion) {
						?>	
							<tr>
								<?php for ($i=0; $i< 13; $i++) { 

									$estilo = "text-center";	
									$datos = $operacion[$i];

									if($i==0) $datos = "<a onclick='detalle_operacion($datos)'>".$datos."<a>";
									if($i==1) $estilo = " text-left ";

									if(trim($operacion[$i])=='VIG' && $i==3) $estilo .= " table-success ";
									if($operacion[$i]>=5 && $i==4) $estilo .= " table-danger ";
									
									if ($i == 5 || $i == 6 || $i == 9) {
										$estilo .= " text-right ";
										$datos = number_format($operacion[$i],0,',','.');
									}
									
									if($i == 12){
										$date  = date_create($operacion[$i]);
										$datos = date_format($date,"d-m-Y");
									}
														
									?>
									<td class="<?= $estilo;?>" nowrap>
										<?= $datos;?>
									</td>
								
									<?php	
								} ?>
							</tr>
							<?php
						}
						?>
					</tbody>
					<?php 
				}	
				$var = $x;
			}
			?>

		</table>
	</div>
</div>
<!--MODAL-->
<div class="modal fade" id="ModalDetalles" tabindex="-1" role="dialog" aria-labelledby="ModalDetalles" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalDetallesTitle">Detalle de la Operación</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="detalle_operacion">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<?php
require('../../footer.php'); 
?>
<script>
	function detalle_operacion(operacion){

		$('#ModalDetalles').modal('show');
		$.ajax({
			type:'POST',
			url:"operacion_detalles.php",
			data:{
				accion : 'consultar',
				operacion : operacion
			},
			success:function(resp){
				$("#detalle_operacion").html(resp);
			}
		});
	}
</script>