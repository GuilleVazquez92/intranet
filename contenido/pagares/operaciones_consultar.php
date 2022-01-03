<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$pagares = new PAGARES();
$pagares->lote = $_POST['lote'];
$pagares->verificado = $_POST['verificado'];

if(isset($_POST['accion']) && $_POST['operacion']){

	$pagares->operacion = $_POST['operacion'];
	$pagares->estado 	= $_POST['accion'];
	//$pagares->verificado = $_POST['verificado'];
	$pagares->modificar_estado();

}
?>
<div class="table-responsive">
	<h3>Lote <?= $_POST['lote'];?></h3>
	<table class="table table-sm">
		<thead>
			<tr class="table-warning text-center" align="middle">
				<th>CUENTA</th>
				<th>DOCUMENTO</th>
				<th>CLIENTE</th>
				<th>OPERACION</th>
				<th>ESTADO</th>
				<th>ACCION</th>
				<th>ATRASO</th>												
				<th>CANT CUOTA</th>
				<th>CUOTA</th>
				<th>VALOR OPERACION</th>
				<th>PAGADO</th>
				<th>SALDO</th>
				<th>LOTE ORIGEN</th>
			</tr>
		</thead>	
		<tbody>
			<?php
			$ix = 0;
			$valor = 0;
			$total_cuota = 0;
			$total_bruto = 0;
			$total_saldo = 0;
			$total_abonado = 0;
			foreach ($pagares->consultar_operaciones() as $key => $datos[]) {

					$total_cuota += $datos[$ix]['valor_cuota']; 
					$total_bruto += $datos[$ix]['valor_operacion'];
					$total_saldo += $datos[$ix]['saldo_capital'];
					$total_abonado += $datos[$ix]['valor_abonado'];
	

				?>
				<tr
				<?php  
				if($datos[$ix]['estado_operacion']==1 || $datos[$ix]['estado_operacion']==3){
							#rojo
					echo "class='table-warning'";
				}elseif ($datos[$ix]['estado_operacion']==4) {
							#amarillo
					echo "class='table-secondary'";
				}else{
							#verde
					echo "class='table-ligh'";
				}
				?>
				>
				<td><?= $datos[$ix]['cuenta']?></td>
				<td><?= $datos[$ix]['documento']?></td>
				<td class="text-left"><?= $datos[$ix]['cliente']?></td>

				<td class="text-center"><?= $datos[$ix]['operacion']?></td>
				<td><?= $datos[$ix]['estado_descripcion']?></td>
		
				<td>
					<?php
					$id = $_POST['lote'].','.$datos[$ix]['operacion'].','.$_POST['verificado'];  
					if($datos[$ix]['estado_operacion']==0 || $datos[$ix]['estado_operacion']==1){
						if(isset($_COOKIE['id'])){
							?>
							<button class="btn btn-outline-success btn-sm" onclick="accion(<?= $id;?>,2)">Activar</button>
							<?php
						}
					}elseif ($datos[$ix]['estado_operacion']==2 && $datos[$ix]['atraso']>60) {
						if(isset($_COOKIE['id'])){
							?>

							<button class="btn btn-outline-danger btn-sm" onclick="accion(<?= $id;?>,3)">Reemplazar</button>
							
							<?php
						}
					}else{
								#verde
						echo "";
					}
					?>
				</td>
				<td 
				<?php  
				if($datos[$ix]['atraso']>60){
							#rojo
					echo "class='text-center table-danger'";
				}elseif ($datos[$ix]['atraso']>30) {
							#amarillo
					echo "class='text-center table-warning'";
				}else{
							#verde
					echo "class='text-center table-success'";
				}
				?>	
				><?= number_format($datos[$ix]['atraso'],0,',','.');?></td>

					<td class="text-center"><?= number_format($datos[$ix]['cant_cuota'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['valor_cuota'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['valor_operacion'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['valor_abonado'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['saldo_capital'],0,',','.');?></td>
					
					<?php
					if (empty($datos[$ix]['origen'])){
					?>
					<td class="text-right">--</td>
					<?php
					}else{
						?>
					<td class="text-right"><?= $datos[$ix]['origen'];?></td>
					
					<?php
					}
					?>

			</tr>
			<?php
			$ix++;
		}
		?>
		<tr>
			<th colspan="8"></th>
			<th class="text-right"><?= number_format($total_cuota,0,',','.');?></th>			
			<th class="text-right"><?= number_format($total_bruto,0,',','.');?></th>
			<th class="text-right"><?= number_format($total_abonado,0,',','.');?></th>
			<th class="text-right"><?= number_format($total_saldo,0,',','.');?></th>
			<th></th>
		</tr>
	</tbody>
</table>
</div>
<script>
	function accion(lote,operacion,verificado,accion) {

		$.ajax({
			type:'POST',
			url:"operaciones_consultar.php",
			data:{
				lote: lote,
				operacion : operacion,
				verificado:verificado,
				accion: accion
			},
			success:function(resp){
				//alert("ok");
				$("#operaciones").html(resp);	
			}
		});
	}

</script>