<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'cobranza.php');
$data = new COBRANZAS();

$cuenta = "";
if(isset($_POST['cuenta'])){
	$data->cuenta = $cuenta = $_POST['cuenta'];
}
$usuario = strtoupper(substr($_COOKIE['usuario'],0,10));

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
		if(isset($_POST['cuenta'])){
			$datos = $data->consultar_cliente();
			if(count($datos[0])>0){
				?>		

				<hr>
				<div class="table-responsive-sm">
					<table class="table table-sm table-borderless">
						<tr>
							<th>Cliente</th>
							<td><?= $datos[0]['cliente'];?></td>
							<th>Documento</th>
							<td><?= $datos[0]['documento'];?></td>
						</tr>
						<tr>
							<th>Dirección Particular</th>
							<td><?= $datos[0]['direccion_part'];?></td>
							<th>Ciudad</th>
							<td><?= $datos[0]['ciudad_part'];?></td>
						</tr>
						<tr>
							<th>Teléfono Particular</th>
							<td><?= $datos[0]['telefono_part'];?></td>
							<th>Celular</th>
							<td><?= $datos[0]['celular_part'];?></td>
						</tr>
						<tr>
							<th>Laboral</th>
							<td><?= $datos[0]['laboral'];?></td>
							<th>Teléfono Laboral</th>
							<td><?= $datos[0]['telefono_lab'];?></td>
						</tr>
						<tr>
							<th>Tramo</th>
							<td><?= $tramo =  $datos[0]['tramo'];?></td>
							<th>Gestor</th>
							<td><?= $datos[0]['gestor'];?></td>
						</tr>
						<tr>
							<th>Calificación</th>
							<td><?= $datos[0]['calificacion'];?></td>
							<th>Estado Judicial</th>
							<td><?= $datos[0]['adenun'];?></td>
						</tr>
					</table>
				</div>
				<hr>	

				<?php
			}  

			$entrega = 0;
			$saldo_capital = 0;
			$saldo_mora = 0;
			$saldo_iva = 0;
			$datos = $data->operaciones_consultar();

			if(count($datos)>0){
				?>
				<div class="table-responsive-sm">
					<table class="table table-sm">
						<thead>
							<tr class="bg-warning">
								<th class="text-center">Operación</th>
								<th class="text-center">Refinanciar</th>
								<th class="text-center">Cantidad</th>
								<th class="text-center">Pendientes</th>
								<th class="text-right">Monto Operación</th>
								<th class="text-right">Valor Cuota</th>
								<th class="text-right">Total Capital</th>
								<th class="text-right">Total Moratorio</th>
								<th class="text-right">Total IVA</th>
								<th class="text-right">TOTAL</th>
							</tr>
						</thead>
						<tbody>
							<?php  
							for ($i=0; $i < count($datos) ; $i++) { 

								$saldo_capital += $datos[$i]['saldo_capital'];
								$saldo_mora    += $datos[$i]['saldo_mora'];
								$saldo_iva 	   += $datos[$i]['saldo_iva'];
								$info = $datos[$i]["saldo_capital"].','.$datos[$i]["saldo_mora"].','.$datos[$i]['saldo_iva'];
								
								?>			
								<tr>
									<td class="text-center"><?= $datos[$i]['operacion'];?></td>
									<td class="text-center">
										<input type="checkbox" name="operacion" id="<?= $datos[$i]['operacion'];?>" value="<?= $info;?>" onclick="generar_saldo()" />
									</td>
									<td class="text-center"><?= number_format($datos[$i]['cuotas_cant'],0,',','.');?></td>
									<td class="text-center"><?= number_format($datos[$i]['cuotas_pend'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['monto'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['monto_cuota'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['saldo_capital'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['saldo_mora'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['saldo_iva'],0,',','.');?></td>
									<td class="text-right"><?= number_format($datos[$i]['saldo_capital']+$datos[$i]['saldo_mora']+$datos[$i]['saldo_iva'],0,',','.');?></td>		
								</tr>
								<?php
							}
							?>	
							<tr class="bg-warning">
								<th colspan="9">TOTAL</th>
								<th class="text-right"><?= number_format($saldo_capital+$saldo_mora+$saldo_iva,0,',','.');?></th>
							</tr>
						</tbody>
					</table>
				</div>
				<br>

				<div class="card" id="contenedor_operaciones">
					<div class="card-body">
						<?php

						$datos = $data->operaciones_pendientes();
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
										</tr>
										<?php
									}
								}else{
									?>	

									<h5 class="card-title">Cuotero para la refinanciación</h5>
									<input type="text"  id="usuario" value="<?= $usuario;?>" hidden="hidden">
									<input type="text"  id="tramo" value="<?= $tramo;?>" hidden="hidden">
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
				}
				?>		
			</div>
			<?php require('../../footer.php'); ?>

			<script>

				function generar_saldo(){

					var x;
					var datos 	= document.getElementsByName("operacion");
					var tramo 	= document.getElementById("tramo").value;
					var capital = 0;
					var interes = 0;

					for (var i = 0; i < datos.length; i++) {
						if (datos[i].checked == true) {

							x = datos[i].value.split(',')	
							capital += parseInt(x[0]); 
							interes += parseInt(x[1])+parseInt(x[2]);
						}
					}	

					$.ajax({
						type:'POST',
						url:"cuotero.php",
						data:{
							tramo : tramo,
							capital: capital,
							interes: interes
						},
						success:function(resp){
							$("#cuotero").html(resp);
						}
					});

				}

				function nueva_operacion(){

					var cuenta 	= document.getElementById('cuenta').value;
					var usuario = document.getElementById('usuario').value;
					var radios  = document.getElementsByName("cuoteros");
					var datos 	= document.getElementsByName("operacion");				
					var operaciones = "";
					var valor;

					for (var i = 0; i < datos.length; i++) {
						if (datos[i].checked == true) {
							x = datos[i].id;
							operaciones += x +',';
						}
					}
					operaciones = operaciones.substring(0,operaciones.length-1);	

					for (var i = 0, length = radios.length; i < length; i++){
						if (radios[i].checked) {
							valor = radios[i].value;
							break;
						}else{
							if( i >= radios.length-1){
								alert('Seleccione una opcion');
							}
						}
					}
					valor = valor.split(",");
					$.ajax({
						type:'POST',
						url:"crear_operaciones.php",
						data:{
							cuenta 			: cuenta,
							usuario 		: usuario,
							operaciones 	: operaciones,
							valor_cuota 	: valor[0],
							valor_total		: valor[1],							
							valor_capital 	: valor[2],
							valor_interes 	: valor[3],
							valor_tasa 		: valor[4],
							cantidad_cuota 	: valor[5]
						},
						success:function(resp){
							$("#contenedor_operaciones").html(resp);
						}
					});
				}
			</script>
