<?php 
require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();

$selected0 = "selected";
$selected1 = $selected2 = $selected3 = $selected4 = $selected5 = "";

if(isset($_POST['filtros_select'])){

	switch ($_POST['filtros_select']) {
		case '1':
		$selected1 = "selected";
		$selected0 = $selected2 = $selected3 = $selected4 = $selected5 = "";			
		$convenio->filtro_estado = 1;
		break;

		case '2':
		$selected2 = "selected";
		$selected1 = $selected0 = $selected3 = $selected4 =  $selected5 ="";
		$convenio->filtro_estado = 2;
		break;

		case '3':
		$selected3 = "selected";
		$selected1 = $selected2 = $selected0 = $selected4 =  $selected5 ="";
		$convenio->filtro_estado = 3;
		break;

		case '4':
		$selected4 = "selected";
		$selected1 = $selected2 = $selected3 = $selected0 = $selected5 = "";
		$convenio->filtro_estado = 4;
		break;

		case '5':
		$selected5 = "selected";
		$selected1 = $selected2 = $selected3 = $selected0 =  $selected4 ="";
		$convenio->filtro_estado = 5;
		break;			


		default:
		$selected0 = "selected";
		$selected1 = $selected2 = $selected3 = $selected4 =  $selected5 ="";
		$convenio->filtro_estado = 0;
		break;
	}
}

if(isset($_POST['accion']) && $_POST['accion']=='pagares_ubicacion'){

	$estado = (!isset($_POST['estado'])) ? 'ENVIO AL ALIADO' : $_POST['estado'];

	$convenio->id  		= $_POST['id'];
	$convenio->usuario 	= $_POST['usuario'];
	$convenio->operacion= $_POST['operacion'];
	$convenio->estado 	= $estado;
	$convenio->lote 	= $_POST['lote']; 
	$convenio->pagares_ubicacion();
}
$filtro_fecha = "";

?>	
<style>
	.btn-circle {
		width: 20px;
		height: 20px;
		text-align: center;
		padding: 1px 0;
		font-size: 12px;
		line-height: 1.428571;
		border-radius: 15px;
	}
</style>
<br>
<div class="container-fluid" id="resolver">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Control de Pagares</li>
		</ol>
	</nav>
	<?php
			//require('filtro_fecha.php');
	$convenio->fecha_inicial = '2019-09-01';	
	$convenio->fecha_final 	 = date('Y-m-d');

	?>
	<form action="" method="POST" class="form-inline">
		<div class="form-group mx-sm-4 mb-3">
			<label for="filtros_select" class="sr-only">Ubicación de Pagares:</label>
			<select name="filtros_select" id="filtros_select" class="form-control">
				<option value="0" <?= $selected0;?>>Seleccione una ubicación</option>
				<option value="1" <?= $selected1;?>>EN FACILANDIA</option>
				<option value="2" <?= $selected2;?>>ENVIO AL ALIADO</option>
				<option value="3" <?= $selected3;?>>EN EL ALIADO</option>
				<option value="4" <?= $selected4;?>>ENVIO A FACILANDIA</option>
				<option value="5" <?= $selected5;?>>PAGARE CANJEADO</option>
			</select>
		</div>
		<button type="submit" class="btn btn-primary mb-3">Confirmar</button>
	</form>


	<table class="table table-sm">
		<thead>				
			<tr class="table-warning">
				<th scope="col">DOCUMENTO</th>
				<th scope="col">CUENTA</th>
				<th scope="col" class="text-center">OPERACION</th>
				<th scope="col" class="text-center">FACTURADO</th>
				<th scope="col" class="text-center">1er VENCIMIENTO</th>
				<th scope="col" class="text-center">CUOTAS</th>
				<th scope="col" class="text-center">ATRASO</th>
				<th scope="col" class="text-center">LOTE</th>
				<th scope="col" class="text-center">UBICACION</th>
				<!--<th scope="col" class="text-right">CUOTA</th>
					<th scope="col" class="text-right">TOTAL</th>-->
				</tr>
			</thead>
			<tbody>	
				<?php

				$lotes =  $convenio->lotes();
				$lote_habilitados = "<option>0</option>";

				for ($i=0; $i < count($lotes) ; $i++) { 
					$lote_habilitados .=  "<option>".$lotes[$i]['lote']."</option>";
				}

				$cuota = 0;
				$total = 0;
				$datos = $convenio->facturadas();
				
				for ($i=0; $i < count($datos); $i++) {

					$cuota += $datos[$i]['cuota'];
					$total += $datos[$i]['total'];
					$date=date_create($datos[$i]['vigencia']);
					$venc=date_create($datos[$i]['vencimiento']);
					$datos_var = $_COOKIE['id'].",'".$_COOKIE['usuario']."',".$datos[$i]['operacion'].",".$datos[$i]['lote'];
					$datos_var1 = $_COOKIE['id'].",'".$_COOKIE['usuario']."',".$datos[$i]['operacion'];
					
					?>
					<tr>
						<td><?= $datos[$i]['documento'];?></td>	
						<td><?= trim($datos[$i]['cuenta'].' - '.$datos[$i]['cliente']);?></td>
						<td class="text-center"><?= ucwords(strtolower($datos[$i]['operacion']))?></td>
						<td class="text-center"><?= date_format($date,"d-m-Y");?></td>
						<td class="text-center">
							<button type="button" 
							class="btn btn-info btn-circle" 
							data-toggle="modal" 
							data-target="#ModalCenter" 
							onclick="consultar_vencimiento(<?= $datos[$i]['operacion'];?>,<?= $datos[$i]['cuenta'];?>)">
						</button>
						<?= date_format($venc,"d-m-Y");?>
					</td>
					<td class="text-center"><?= $datos[$i]['pendiente']."/".$datos[$i]['cantidad']; ?></td>
					<td class="text-center"><?= number_format($datos[$i]['atraso'],0,',','.') ?></td>
					<td class="text-center">
						<?php 
						if((is_null($datos[$i]['lote']) || $datos[$i]['lote']==0) && $_COOKIE['cod_perfil'] < 9000){
							?>								
							<select onchange="lote_asignar(<?= $datos_var1;?>,this.value)">
								<?= $lote_habilitados; ?>
							</select>		 
							<?php
						}else {
							echo $datos[$i]['lote'];
						}
						?>
					</td>

					<td class="text-center">
						<button type="button" 
						class="btn btn-info btn-circle" 
						data-toggle="modal" 
						data-target="#ModalHistorico" 
						onclick="pagares_historial(<?= $datos[$i]['operacion'];?>)">
					</button>
					<?php 
						//var_dump($_COOKIE['cod_perfil']);	
					if($_COOKIE['cod_perfil'] > 9000){


						switch (trim($datos[$i]['ubicacion'])) {
							case 'ENVIO AL ALIADO':
							?>	
							<select onchange="pagares_ubicacion(<?= $datos_var;?>,this.value)">
								<option selected>ENVIO AL ALIADO</option>
								<option>EN EL ALIADO</option>
							</select>
							<?php		
							break;

							case 'EN EL ALIADO':
							?>	
							<select onchange="pagares_ubicacion(<?= $datos_var;?>,this.value)">
								<option selected>EN EL ALIADO</option>
								<option>ENVIO A FACILANDIA</option>

							</select>
							<?php		
							break;	

							default:
							echo $datos[$i]['ubicacion'];
							break;
						}

					} else{

						switch (trim($datos[$i]['ubicacion'])) {
							case 'ENVIO A FACILANDIA':
							?>	
							<select onchange="pagares_ubicacion(<?= $datos_var;?>,this.value)">
								<option selected>ENVIO A FACILANDIA</option>
								<option>EN FACILANDIA</option>
								<option>PAGARE CANJEADO</option>
							</select>
							<?php		
							break;

							default:
							echo $datos[$i]['ubicacion'];
							break;
						}
					}					 
					?>

				</td>
			</tr>							
			<?php
		}	
		?>
		<tr class="table-warning">
			<th colspan="10"><b></b></td>
				<!--<th class="text-right"><?= number_format($cuota,0,',','.') ?></th>
					<th class="text-right"><?= number_format($total,0,',','.') ?></th>-->
				</tr>
			</tbody>
		</table>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Cuotero de Operación</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="ModalHistorico" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Historial del Pagares</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

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
		function consultar_vencimiento(operacion,cuenta){
			$.ajax({
				type:'POST',
				url:"vencimiento_consultar.php",
				data:{
					operacion: operacion, 
					cuenta: cuenta
				},
				success:function(resp){
					$(".modal-body").html(resp);	
				}
			});
		}
		
		function pagares_ubicacion(id,usuario,operacion,lote,estado){

			usuario = usuario.toUpperCase();
			$.ajax({
				type:'POST',
				url:"pagares.php",
				data:{
					accion: 'pagares_ubicacion',			
					id: id,
					usuario: usuario, 
					operacion: operacion,
					estado: estado,
					lote: lote
				},
				success:function(resp){

				}
			});
		}

		function pagares_historial(operacion){
			$.ajax({
				type:'POST',
				url:"pagares_historial.php",
				data:{
					operacion: operacion
				},
				success:function(resp){
					$(".modal-body").html(resp);	
				}
			});
		}

		function lote_asignar(id,usuario,operacion,lote){

			usuario = usuario.toUpperCase();
			$.ajax({
				type:'POST',
				url:"pagares.php",
				data:{
					accion: 'pagares_ubicacion',			
					id: id,
					usuario: usuario, 
					operacion: operacion,
					lote: lote
				},
				success:function(resp){

				}
			});
		}


	</script>
