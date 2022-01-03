<?php
error_reporting(E_ALL); 
require('../../header.php');
require( CONTROLADOR . 'ir.php');
$ir = new IR();

$cuenta = "";
if(isset($_POST['cuenta'])){
	$ir->cuenta = $cuenta = $_POST['cuenta'];
}

$entrega = 0;


?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Refinanciación</li>
		</ol>
	</nav>	

	<div class="container">
		<form method="POST" action="" class="form-inline">
			<div class="form-group input-group-sm">
				<label for="cuenta" class=" mb-3 mr-2">Cuenta del cliente</label>
				<input type="number" min="0" class="form-control  mb-3 mr-2" id="cuenta" name="cuenta" aria-describedby="CuentaHelp" placeholder="Cuenta" value="<?= $cuenta;?>" required="required">
			</div>
			<button type="submit" class="btn btn-primary btn-sm mb-3">Consultar</button>
		</form>

		<?php
		$datos = $ir->consultar_cliente();
		if(count($datos)>0){
			?>		

			<hr>
			<div class="table-responsive-sm">
				<table class="table table-sm table-borderless">
					<tr>
						<th>Cliente</th>
						<td><?= $datos['cliente'];?></td>
						<th>Documento</th>
						<td><?= $datos['documento'];?></td>
					</tr>
					<tr>
						<th>Dirección Particular</th>
						<td><?= $datos['direccion_part'];?></td>
						<th>Ciudad</th>
						<td><?= $datos['ciudad_part'];?></td>
					</tr>
					<tr>
						<th>Teléfono Particular</th>
						<td><?= $datos['telefono_part'];?></td>
						<th>Celular</th>
						<td><?= $datos['celular_part'];?></td>
					</tr>
					<tr>
						<th>Laboral</th>
						<td><?= $datos['laboral'];?></td>
						<th>Teléfono Laboral</th>
						<td><?= $datos['telefono_lab'];?></td>
					</tr>
					<tr>
						<th>Tramo</th>
						<td><?= $tramo =  $datos['tramo'];?></td>
						<th>Gestor</th>
						<td><?= $datos['gestor'];?></td>
					</tr>
					<tr>
						<th>Calificación</th>
						<td><?= $datos['calificacion'];?></td>
						<th>Estado Judicial</th>
						<td><?= $datos['adenun'];?></td>
					</tr>
				</table>
			</div>
			<hr>	

			<?php
		}  

		$bruto = 0;
		$datos = $ir->operaciones_consultar();

		if(count($datos)>0){
			$abogado 	= 0;
			?>

			<p>
				<a 	class="btn btn-primary btn-sm" 
				data-toggle="collapse" 
				href="#collapseOperaciones" 
				role="button" 
				aria-expanded="false" 
				aria-controls="collapseExample">
				Operaciones Vigentes
			</a>
		</p>

		<div class="collapse table-responsive-sm" id="collapseOperaciones">
			<table class="table table-sm">
				<thead>
					<tr class="bg-warning">
						<th class="text-center">Operación</th>
						<th class="text-center">Cantidad</th>
						<th class="text-center">Pendientes</th>
						<th class="text-right">Monto Operación</th>
						<th class="text-right">Valor Cuota</th>
						<th class="text-right">Saldo Capital</th>
						<th class="text-right">Saldo Moratorio</th>
					</tr>
				</thead>
				<tbody>
					<?php  
					for ($i=0; $i < count($datos) ; $i++) { 
						$bruto += $datos[$i]['monto_mora'];
						if($abogado != $datos[$i]['abogado'] and $datos[$i]['abogado']>0){
							$abogado = $datos[$i]['abogado'];
						}

						?>			
						<tr>
							<td class="text-center"><?= $datos[$i]['operacion'];?></td>
							<td class="text-center"><?= number_format($datos[$i]['cuotas_cant'],0,',','.');?></td>
							<td class="text-center"><?= number_format($datos[$i]['cuotas_pend'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['monto'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['monto_cuota'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['saldo'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['monto_mora'],0,',','.');?></td>		
						</tr>
						<?php
					}
					?>	
					<tr class="bg-warning">
						<th colspan="6">TOTAL</th>
						<th class="text-right"><?= number_format($bruto,0,',','.');?></th>
					</tr>
				</tbody>
			</table>
		</div>
		<br>

		<div class="card" id="contenedor_operaciones">

			<div class="card-body">

				<?php  
				$datos = $ir->operaciones_pendientes();
				if(count($datos)>0){
					?>
					<h5>Operación Pendiente</h5>
					<table class="table table-sm">
						<thead>
							<tr class="bg-warning">
								<th class="text-center">Operación</th>
								<th class="text-center">Cantidad</th>
								<th class="text-center">Pendientes</th>
								<th class="text-right">Monto Operación</th>
								<th class="text-right">Valor Cuota</th>
								<th class="text-right">Saldo Capital</th>
								<th class="text-center">Documento</th>
							</tr>
						</thead>
						<tbody>
							<?php  
							for ($i=0; $i < count($datos) ; $i++) { 
								?>			
								<tr>
									<td class="text-center"><?= $datos[$i]['operacion'];?></td>
									<td class="text-center"><?= number_format($datos[$i]['cuotas_cant'],0,',','.');?></td>
									<td class="text-center"><?= number_format($datos[$i]['cuotas_pend'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['monto'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['monto_cuota'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['saldo'],0,',','.');?></td>
									<td class="text-center"><a href="pagares_pdf.php?cuenta=<?= $_POST['cuenta'];?>&tipo=<?= $datos[$i]['tipo'];?>" target="_blank">
										<img src="<?= IMAGE.'pdf32x32.png';?>" alt="pdf"></a>
									</td>		
								</tr>
								<?php
							}
						}else{
							?>	

							<h5 class="card-title">Cuotas</h5>

							<div class="form-group form-inline">

								<label for="tipo" class=" mb-2 mr-2">Tipo de negociación</label>
								<input type="text"  id="abogado" value="<?= $abogado;?>" hidden="hidden">
								<input type="text"  id="bruto" value="<?= $bruto;?>" hidden="hidden">
								<input type="text"  id="tramo" value="<?= $tramo;?>" hidden="hidden">

								<select id="tipo" class="form-control form-control-sm mb-2 mr-2" required="required">
									<option></option>
									<option value="1">CONTRATO</option>
									<option value="2">PAGARE</option>
								</select>
							</div>

							<div class="form-group form-inline">		
								<div class="input-group input-group-sm mb-2">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon3">Entrega inicial</span>
									</div>
									<input type="number" class="form-control" id="entrega" aria-describedby="basic-addon3" value="<?= $entrega;?>" 
											min="0" step="10000" style="text-align: right !important;">
								</div>

							</div>
							<div class="form-group form-inline">
								<button type="button" class="btn btn-primary btn-sm" onclick="calcular_cuotero()">Calcular cuota</button>
							</div>


							<?php 
						}
						?>		  
					</div>

					<div id="cuotero">
					</div>
				</div>
				<?php
			}else{
				?>
				<div class="alert alert-warning" role="alert">
					No se encontraron operaciones Vigentes o tiene una REFINANCIACION
				</div>	
				<?php
			}
			?>		
		</div>

		<?php require('../../footer.php'); ?>

		<script>
			function calcular_cuotero(){

				var cuenta 	= document.getElementById('cuenta').value;
				var bruto 	= document.getElementById('bruto').value;
				var tramo 	= document.getElementById('tramo').value;
				var tipo 	= document.getElementById('tipo').value;
				var entrega	= document.getElementById('entrega').value;

				$.ajax({
					type:'POST',
					url:"cuotero.php",
					data:{
						cuenta: cuenta, 
						bruto: bruto,
						tramo: tramo,
						tipo: tipo ,
						entrega : entrega
					},
					success:function(resp){
						$("#cuotero").html(resp);
					}
				});
			}

			function nueva_operacion(){

				var abogado	= document.getElementById('abogado').value;
				var cuenta 	= document.getElementById('cuenta').value;
				var entrega = document.getElementById('entrega2').value;
				var radios  = document.getElementsByName("cuoteros");
				var valor;

				for (var i = 0, length = radios.length; i < length; i++){
					if (radios[i].checked) {
						valor = radios[i].value;
						break;

					}else{

						if( i >= radios.length-1){

							alert('Seleccione una opcion');
							/**crear un popup para enviar mensaje**/		
						}
					}
				}
				valor = valor.split(",");
				$.ajax({
					type:'POST',
					url:"crear_operaciones.php",
					data:{
						cuenta: 		cuenta,
						abogado: 		abogado,
						entrega : 		entrega, 
						valor_capital: 	valor[0],			
						valor_tasa: 	valor[1]*1000,
						cantidad_cuota: valor[2],
						valor_cuota : 	valor[3],
						valor_total: 	valor[4],
						cabezon: 		valor[5],
						cod_oper : 		valor[6] 
					},
					success:function(resp){
						$("#contenedor_operaciones").html(resp);
					}
				});
			}
		</script>
