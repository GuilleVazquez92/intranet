<?php 
require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();
$filtro_fecha = "";

?>
<style>
	.btn-circle {
		width: 20px;
		height: 20px;
		text-align: center;
		padding: 1px 0;
		font-size: 12px;
		line-height: 1.4285714;
		border-radius: 15px;
	}
</style>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Compras</li>
		</ol>
	</nav>
	<?php
			//require('filtro_fecha.php');
	$convenio->fecha_inicial = '2019-08-25';	
	$convenio->fecha_final 	 = date('Y-m-d');
	?>
	<table class="table table-sm table-responsive-sm">
		<thead>				
			<tr class="table-warning">
				<th scope="col">FECHA</th>
				<th scope="col">ORDEN</th>
				<th scope="col">PROVEEDOR</th>
				<th scope="col">FACTURA</th>
				<th scope="col">PRODUCTO</th>
				<th scope="col" class="text-center">OP.RELACION</th>
				<th scope="col" class="text-center">CANTIDAD</th>
				<th scope="col" class="text-right">PRECIO</th>
				<th scope="col" class="text-right">TOTAL</th>
				<th scope="col" class="text-right">CANT.CUOTAS</th>
				<th scope="col" class="text-right">VALOR CUOTA</th>
			</tr>
		</thead>
		<tbody>	
			<?php
			$nc		='N';
			$cuota = 0;
			$total = 0;
			$var   = 1;
			$datos = $convenio->compras($nc);

			for ($i=0; $i < count($datos); $i++) {

					$var 	= $datos[$i]['cantidad']-$datos[$i]['relacion'];

				if($var>0){

					$cuota += $datos[$i]['valor_cuotas'];
					$total += $datos[$i]['precio_total'];
					$date 	= date_create($datos[$i]['fecha_factura']);					
					?>
					<tr>
						<td class="text-left"><?= date_format($date,"d-m-Y");?></td>
						<td><?= $datos[$i]['orden'];?></td>	
						<td><?= $datos[$i]['proveedor'];?></td>	
						<td><?= trim($datos[$i]['factura']);?></td>
						<td class="text-left"><?= $datos[$i]['codigo'].' '.$datos[$i]['producto'];?></td>
						<td class="text-center">
							<?php 
							$color_boton = ($datos[$i]['relacion']==$datos[$i]['cantidad']) ? 'btn-success': 'btn-danger';
							?>	
							<button type="button" class="btn <?= $color_boton; ?> btn-circle" data-toggle="modal" data-target="#ModalCenter" 
								onclick="consultar_orden(<?= $var;?>,<?= $datos[$i]['orden'];?>,<?= $datos[$i]['codigo'];?>)"><?= $datos[$i]['relacion'];?>
							</button>
						</td>				
						<td class="text-center"><?= $datos[$i]['cantidad'];?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_unitario'],0,',','.')?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_total'],0,',','.') ?></td>
						<td class="text-right"><?= number_format($datos[$i]['cant_cuotas'],0,',','.') ?></td>
						<td class="text-right"><?= number_format($datos[$i]['valor_cuotas'],0,',','.') ?></td>
					</tr>							
					<?php
				}
			}	
			?>
			<tr class="table-warning">
				<th colspan="8"><b>TOTAL</b></td>
					<th class="text-right"><?= number_format($total,0,',','.') ?></th>
					<th></th>
					<th class="text-right"><?= number_format($cuota,0,',','.') ?></th>
				</tr>
			</tbody>
		</table>

		<!-- Modal -->
		<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="ModalCenterTitle">Operaciones Relacionadas</h5>
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
	</div>
	<?php 
	require('../../footer.php'); 
	?>
	<script>
		function consultar_orden(variable,orden,codigo){

			var url;
			if(variable==0){
				url = "orden_consultar.php";

			}else{
				url = "orden_vincular.php";	
			}

			$.ajax({
				type:'POST',
				url: url,
				data:{
					orden: orden, 
					codigo: codigo
				},
				success:function(resp){
					$(".modal-body").html(resp);	
				}
			});
		}

		function vincular_orden(orden,codigo,codigo_1,operacion){
			$.ajax({
				type:'POST',
				url:"orden_vincular.php",
				data:{
					orden: orden, 
					codigo: codigo,
					codigo_1: codigo_1,
					operacion: operacion
				},
				success:function(resp){
					$(".modal-body").html(resp);	
				}
			});
		}
	</script>	