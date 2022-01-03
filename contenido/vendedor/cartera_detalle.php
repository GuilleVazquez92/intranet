<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require(CONTROLADOR.'vendedores.php');

$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];

## Filtro de Clientes
$_SESSION['filtro_cliente'] = (!isset($_SESSION['filtro_cliente'])) ? 0 : $_SESSION['filtro_cliente'];
$_SESSION['filtro_cliente'] = (isset($_POST['filtro_cliente'])) ? $_POST['filtro_cliente'] : $_SESSION['filtro_cliente'];

## Filtro de Gestion 
$_SESSION['filtro_gestion'] = (!isset($_SESSION['filtro_gestion'])) ? 0 : $_SESSION['filtro_gestion'];
$_SESSION['filtro_gestion'] = (isset($_POST['filtro_gestion'])) ? $_POST['filtro_gestion'] : $_SESSION['filtro_gestion'];

## Pagina actual
$pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

## Filtro de documento
$filtro_documento = (isset($_POST['filtro_documento']) && strlen($_POST['filtro_documento'])>1) ? $_POST['filtro_documento'] : "";

if(isset($_GET['filtro_documento']) && strlen($_GET['filtro_documento'])>1){
	$filtro_documento = (!isset($_POST['filtro_documento'])) ? $_GET['filtro_documento'] : $_POST['filtro_documento']; 
}

## Filtro de documento
$filtro_cuenta=(isset($_POST['filtro_cuenta']) && strlen($_POST['filtro_cuenta'])>1)?$_POST['filtro_cuenta']:"";

if(isset($_GET['filtro_cuenta']) && strlen($_GET['filtro_cuenta'])>1){
	$filtro_cuenta = (!isset($_POST['filtro_cuenta'])) ? $_GET['filtro_cuenta'] : $_POST['filtro_cuenta']; 
}

## Cantidad de clientes
$cantidad = $vendedor->cant_cartera();
?>

<div>
	<input type="text" class="form-control form-control-sm mb-2" id="buscar_documento" placeholder="Documento">
	<input type="text" class="form-control form-control-sm mb-2" id="buscar_cuenta" placeholder="Cuenta"> 
	<button class="btn btn-outline-primary btn-sm mb-2" type="button" onclick="filtro_buscar_cliente();">Buscar</button>
</div>
<hr>

<div class="text-center mb-2">
	<button type="button" class="btn btn-secondary btn-sm" id="FC_0" onclick="filtro_cliente(0)">
		<small class="form-text text-light">TODOS</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FC_1" onclick="filtro_cliente(1)">
		<small class="form-text text-light">PROSPECTOS</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FC_2" onclick="filtro_cliente(2)">
		<small class="form-text text-light">ACTIVOS</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FC_3" onclick="filtro_cliente(3)">
		<small class="form-text text-light">TRANSICION</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FC_4" onclick="filtro_cliente(4)">
		<small class="form-text text-light">INACTIVOS</small>
	</button>
	<input type="text" id="filtro_cliente" value="<?= $_SESSION['filtro_cliente'];?>" hidden>
</div>

<div class="text-center mb-2">
	<button type="button" class="btn btn-secondary btn-sm" id="FG_1" onclick="filtro_gestion(1)">
		<small class="form-text text-light">HOY(<?= $cantidad[0]['cantidad'] ;?>)</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FG_2" onclick="filtro_gestion(2)">
		<small class="form-text text-light">AYER(<?= $cantidad[1]['cantidad'] ;?>)</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FG_3" onclick="filtro_gestion(3)">
		<small class="form-text text-light">VENC.(<?= $cantidad[2]['cantidad'] ;?>)</small>
	</button>
	<button type="button" class="btn btn-secondary btn-sm" id="FG_4" onclick="filtro_gestion(4)">
		<small class="form-text text-light">GEST.(<?= $cantidad[3]['cantidad'] ;?>)</small>
	</button>
	<input type="text" id="filtro_gestion" value="<?= $_SESSION['filtro_gestion'];?>" hidden>
</div>

<div class="row">
	<?php
	$cartera['cartera'] = $vendedor->cartera($filtro_documento,$filtro_cuenta,$_SESSION['filtro_cliente'],$_SESSION['filtro_gestion'],$pagina);
	for($i=0; $i < count($cartera['cartera']); $i++){	
		?>
		<div class="col-sm-4 my-2">
			<div class="card">
				<div class="card-header"><?php 
				if(trim($cartera['cartera'][$i]['origen'])=='WEB'){
				?>
				<img src="<?= IMAGE."crown.png" ?>" alt="" width="20px">
				<?php	
				}
				echo  $cartera['cartera'][$i]['cuenta'].' '.$cartera['cartera'][$i]['cliente'];?></div>
				<div class="card-body">
					<h6 class="card-title my-0">Documento:<?= $cartera['cartera'][$i]['documento']?></h6>
					<p class="card-text mb-2"><?= ucwords(strtolower($cartera['cartera'][$i]['direccion']))?></p>

					<small class="form-text text-muted">
						<b>Tel :</b><?= $cartera['cartera'][$i]['telefono1'];?><br>
						<b>Tel :</b><?= $cartera['cartera'][$i]['telefono2'];?><br>
						<b>Cel :</b><?= $cartera['cartera'][$i]['celular'];?><br>
					</small>
					<br>


					<div class="row">	
						<div class="col-6">
							<?php 
							switch ($cartera['cartera'][$i]['estado']) {
								case 'ACTIVO':
								$estilo = "btn btn-success btn-sm";
								break;
								case 'INACTIVO':
								$estilo = "btn btn-warning btn-sm";
								break;
								default:
								$estilo = "btn btn-danger btn-sm";
								break;
							}
							?>
							<button type="button" class="<?= $estilo;?>"><?= $cartera['cartera'][$i]['estado'];?></button>

							<small class="form-text text-muted">
								<b>Recurrente :</b><?= $cartera['cartera'][$i]['recurrente'];?><br>
								<b>Calificación :</b><?= $cartera['cartera'][$i]['situacion'];?><br>
							</small>
						</div>

						<div class="col-6">
							<!--<div class="text-left">
								<small class="form-text text-muted">
									<b>Gestor :</b><?= $cartera['cartera'][$i]['gestor'];?>
								</small>	
							</div>-->
							<div>
								<ul class="list-group">
									<li class="list-group item"><small>Linea Asignada : <span class=""><?= number_format($cartera['cartera'][$i]['asignada'],0,',','.'); ?></span></small></li>
									<li class="list-group item"><small>Linea Disponible : <span class="text-success font-weight-bold"><?= number_format($cartera['cartera'][$i]['saldo'],0,',','.'); ?></span></small></li>
								</ul>
							</div>

						</div>
						<!--
						<div class="col-2">
							<div class="col text-center">
								<form action="gestion.php" method="GET">	
									<button	name="cuenta" class="btn btn-vinculo" type="submit"	value="<?= $cartera['cartera'][$i]['cuenta'];?>">
										<i class="fa fa-cog" aria-hidden="true" title="Gestión"></i>
									</button>
								</form>
							</div>				
						</div>
					-->
					</div>
					<form action="gestion.php" method="GET">	
						<button	name="cuenta" class="btn btn-primary" type="submit"	value="<?= $cartera['cartera'][$i]['cuenta'];?>">
							<!--<i class="fa fa-cog" aria-hidden="true" title="Gestión"></i>-->
							Gestionar
						</button>
					</form>
				</div>
			</div>
		</div>

		<?php
	}
	echo "</div>";
	$total_items = (isset($cartera['cantidad_total'])) ? $cartera['cantidad_total'] : 0 ;	
	include('paginador.php'); 
	?>
	<script>
		function pagina(pagina){
			$.ajax({
				type:'POST',
				url:"cartera_detalle.php",
				data:{
					pagina : pagina
				},
				success:function(resp){
					$("#resultado").html(resp);
				}
			});
		}

		function filtro_buscar_cliente(){

			var documento = document.getElementById('buscar_documento').value;
			var cuenta = document.getElementById('buscar_cuenta').value;

			$.ajax({
				type:'POST',
				url:"cartera_detalle.php",
				data:{
					filtro_documento : documento,
					filtro_cuenta : cuenta,
					pagina : 1
				},
				success:function(resp){
					$("#resultado").html(resp);
				}
			});
		}

		function filtro_cliente(valor){
			$.ajax({
				type:'POST',
				url:"cartera_detalle.php",
				data:{
					filtro_cliente : valor,
					pagina : 1
				},
				success:function(resp){
					$("#resultado").html(resp);
				}
			});
		}

		function filtro_gestion(valor){
			$.ajax({
				type:'POST',
				url:"cartera_detalle.php",
				data:{
					filtro_gestion : valor,
					pagina : 1
				},
				success:function(resp){
					$("#resultado").html(resp);
				}
			});
		}


		function estado_filtro(){

			var filtro_cliente 	= document.getElementById('filtro_cliente');
			var filtro_gestion 	= document.getElementById('filtro_gestion');

			filtro_cliente  = '#FC_'+filtro_cliente.value.toString();
			filtro_gestion  = '#FG_'+filtro_gestion.value.toString();

			$(filtro_cliente).removeClass("btn-secondary").addClass("btn-primary");
			$(filtro_gestion).removeClass("btn-secondary").addClass("btn-primary");
		}

		document.onload = estado_filtro();


	</script>