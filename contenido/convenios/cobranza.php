<?php 
//	error_reporting(E_ALL);
//	ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();
$filtro_fecha = "";
$convenio->id = $_COOKIE['id'];	
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Operaciones Cobradas</li>
		</ol>
	</nav>
	<?php
	require('filtro_fecha.php');
	?>
	<a href="cobranza_expor_excel.php?entidad=1"><?Php echo '<img src="'.IMAGE.'excel.png" alt="">';?></a>
	<table class="table table-sm">
		<thead>				
			<tr class="table-warning">
				<th scope="col" class="text-center">OPERACION</th>
				<th scope="col">CUENTA</th>
				<th scope="col" class="text-center">FECHA PGO.</th>
				<th scope="col" class="text-left">CAJA</th>
				<th scope="col" class="text-center">MOVIMIENTO</th>
				<th scope="col" class="text-center">NRO.CUOTA</th>
				<th scope="col" class="text-right">VALOR</th>
				<th scope="col">&nbsp</th>
			</tr>
		</thead>
		<tbody>	
			<?php

			$total 		= 0;
			$datos 		= $convenio->cobranza();
			$usuario 	= (isset($_COOKIE['usuario'])) ? $_COOKIE['usuario'] : '';

			for ($i=0; $i < count($datos); $i++) {
				
				$total 			+= $datos[$i]['valor_total'];
				$date 			= date_create($datos[$i]['fecha_pago']);
				$dato 			= $usuario.','.$datos[$i]['movimiento'].','.$datos[$i]['nro_cuota'];
				$id 			= $datos[$i]['movimiento'].$datos[$i]['nro_cuota'];
				$fecha_verifica = date_create($datos[$i]['fecha_verificacion']);
				?>
				<tr>
					<td><?= $datos[$i]['operacion'];?></td>	
					<td><?= $datos[$i]['cuenta'].' - '.$datos[$i]['cliente'];?></td>
					<td class="text-center"><?= date_format($date,"d-m-Y");?></td>
					<td class="text-left"><?= $datos[$i]['cajero']; ?></td>
					<td class="text-center"><?= $datos[$i]['movimiento'];?></td>
					<td class="text-center"><?= $datos[$i]['nro_cuota'];?></td>
					<td class="text-right"><?= number_format($datos[$i]['valor_total'],0,',','.') ?></td>
					<td class="text-right">
						<?php  
						if($datos[$i]['verificado'] == 'S'){
							?>	
							<img src="<?= IMAGE.'check_in.png'?>" alt="" width="14px" height='14px' title="Verificado por: <?= strtoupper($datos[$i]['usuario']);?> en la fecha: <?= date_format($fecha_verifica, 'd-m-Y');?> a las <?= date_format($fecha_verifica, 'H:m');?>hs.">

							<?php	
						}else{
							?>
							<input class="form-check-input" type="checkbox" id="<?= $id;?>" value="<?= $dato;?>" onclick="verificar_pago('<?= $id;?>')">
						</td>
					<?php } ?>
				</tr>							
				<?php
			}	
			?>
			<tr class="table-warning">
				<th colspan="6"><b>TOTAL</b></td>
					<th class="text-right"><?= number_format($total,0,',','.') ?></th>
					<th></th>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="container-fluid" id="respuesta"></div>
	<?php 
	require('../../footer.php'); 
	?>
	<script>

		function verificar_pago(valor){

			var data 		= document.getElementById(valor);
			var datos 		= data.value.split(",");
			var usuario 	= datos[0];
			var movimiento 	= datos[1];
			var orden 		= datos[2];
			var estado		= 'S';

			if(data.checked == false){
				estado = 'N';
			}

			$.ajax({
				type:'POST',
				url:"verificar_pago.php",
				data:{
					usuario: usuario, 
					movimiento: movimiento,
					orden: orden,
					estado: estado
				},
				success:function(resp){
				// alert('ok');
				// $("#respuesta").html(resp);	
			}
		});
		}

	</script>