<?php 
	require('../../header.php');
	require( CONTROLADOR . 'convenio.php');
	$convenio = new Convenios();
	$filtro_fecha = "disabled";
	
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
		    <li class="breadcrumb-item active" aria-current="page">Operaciones Atrasadas</li>
		  </ol>
		</nav>
		<?php
//			require('filtro_fecha.php');
		?>
		<table class="table table-sm">
			<thead>				
			<tr class="table-warning">
				<th scope="col">DOCUMENTO</th>
				<th scope="col">CUENTA</th>
				<th scope="col" class="text-center">OPERACION</th>
				<th scope="col" class="text-center">ESTADO</th>
				<th scope="col" class="text-center">FACTURADO</th>
				<th scope="col" class="text-center">CANTIDAD</th>
				<th scope="col" class="text-center">PENDIENTE</th>
				<th scope="col" class="text-center">ATRASO</th>
				<th scope="col" class="text-right" >CUOTA</th>
				<th scope="col" class="text-right" >TOTAL</th>
			</tr>
			</thead>
			<tbody>	
			<?php
				$cuota = 0;
				$total = 0;
				$datos = $convenio->atrasadas();
				
				for ($i=0; $i < count($datos); $i++) {
					$cuota += $datos[$i]['cuota'];
					$total += $datos[$i]['total'];
					$date=date_create($datos[$i]['vigencia']);
			?>
			<tr>
				<td><?= $datos[$i]['documento'];?></td>	
				<td><?= $datos[$i]['cuenta'].' - '.$datos[$i]['cliente'];?></td>
				<td class="text-center"><?= ucwords(strtolower($datos[$i]['operacion']))?></td>
				<td class="text-center"><?= $datos[$i]['estado'];?></td>
				<td class="text-center">
					<button type="button" class="btn btn-info btn-circle" data-toggle="modal" data-target="#ModalCenter" 
						onclick="consultar_vencimiento(<?= $datos[$i]['operacion'];?>,<?= $datos[$i]['cuenta'];?>)">
					</button>
					<?= date_format($date,"d-m-Y");?></td>
				<td class="text-center"><?= number_format($datos[$i]['cantidad'],0,',','.') ?></td>
				<td class="text-center"><?= number_format($datos[$i]['pendiente'],0,',','.') ?></td>
				<td class="text-center"><?= number_format($datos[$i]['atraso'],0,',','.') ?></td>
				<td class="text-right"><?= number_format($datos[$i]['cuota'],0,',','.') ?></td>
				<td class="text-right"><?= number_format($datos[$i]['total'],0,',','.') ?></td>
			</tr>							
			<?php
				}	
			 ?>
			 <tr class="table-warning">
				<th colspan="8"><b>TOTAL</b></th>
				<th class="text-right"><?= number_format($cuota,0,',','.') ?></th>
				<th class="text-right"><?= number_format($total,0,',','.') ?></th>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl" role="document">
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
	</script>