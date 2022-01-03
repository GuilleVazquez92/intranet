<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$data  = new Vendedores;
$data->cod_vendedor = $_COOKIE['rol'];
?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="cartera.php">Cartera</a></li>
			<li class="breadcrumb-item active" aria-current="page">Operaciones</li>
		</ol>
	</nav>	

	<h4><?= $_COOKIE['rol']." ".$_COOKIE['nombre'] ;?></h4>
	<div class="table-responsive-sm">
		<table class="table table-sm">
			<?php 
			$var = 99;
			for ($x=0; $x < 3 ; $x++) { 
				switch ($x) {
					case 0:
					$titulo = 'EN PROCESO';
					$variable = 'proceso';
					break;
					case 1:
					$titulo = 'FACTURADAS';
					$variable = 'facturadas';							
					break;
				//	case 2:
				//	$titulo = 'CANCELADAS';
				//	$variable = 'canceladas';							
				//	break;
					case 2:
					$titulo = 'OTRAS';
					$variable = 'otras';							
					break;																					
				}

				$proceso = $data->operaciones($variable);
				if(count($proceso)>0){
					?>
					<tr class="bg-warning">
						<td colspan="14">
							<h4 class="card-title"><?= $titulo;?></h4>
						</td>
					</tr>
					<tr class="table-warning">
						<th class="text-center">Operación</th>
						<th class="text-left">Cliente</th>
						<th class="text-center">Tipo</th>
						<th class="text-center" colspan="2">Estado</th>
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
						foreach ($proceso as $key) {
							?>	
							<tr>
								<?php for ($i=0; $i< 13; $i++) { 

									$estilo 	= "text-center";	
									$datos 		= $key[$i];

									if($i==0){
										$operacion = $key[$i];
										$datos = "<a onclick='detalle_operacion($operacion)'>".$operacion."<a>";	
									} 
									if($i==1){
										$datos = "<a href='gestion.php?cuenta=$key[13]'>".$datos."<a>";	
										$estilo = " text-left ";	
									} 

									if($i==3){
										echo "<td nowrap>";

										if($key[14]=='WEB'){
											?>
											<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px">
											<?php
										}else{
											echo "&nbsp;";
										}
										echo "</td>";
										
										if(trim($key[$i])=='VIG'){
											$estilo .= " table-success ";	
										} 

										if(trim($key[$i])=='COND'){
											$estilo .= " table-warning ";
											$datos = "<a onclick='condicionados_responder($operacion)'>COND<a>";		
										}
									} 

									if($key[$i]>=5 && $i==4) $estilo .= " table-danger ";

									if ($i == 5 || $i == 6 || $i == 9) {
										$estilo .= " text-right ";
										$datos = number_format($key[$i],0,',','.');
									}
									
									if($i == 12){
										$date  = date_create($key[$i]);
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
<br>
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

	function condicionados_responder(operacion){

		$("#detalle_operacion").html("");
		$('#ModalDetalles').modal('show');

		$.ajax({
			type:'POST',
			url:"condicionados_responder.php",
			data:{
				operacion : operacion
			},
			success:function(resp){

				$("#detalle_operacion").html(resp);
			}
		});
	}


</script>