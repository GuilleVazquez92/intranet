<?php 
	include("../header.php");
	$cuenta = 9999999999999;

	if(isset($_POST['cuenta']) && is_numeric($_POST['cuenta'])){

		echo $cuenta = $_POST['cuenta'];
		var_dump($_SESSION);
	}
?>
<br>
<div class="container">
	<h3>Consulta de estado</h3>

	<form action="" method="POST" class="form-inline">
		<div class="form-group">
			<label for="cuenta" class="sr-only">Cuenta</label>
			<input class="form-control form-control-sm" id="cuenta" name="cuenta" type="text" placeholder="Número de cuenta" />	
		</div>
		<button class="btn btn-sm btn-info">Consultar</button>
	</form>
	<br>

<?php 

	$sql = "SELECT bfope1 operacion,bfcant cantidad,bfpend pendiente,bsituacion calificacion, bftasa tasa FROM fsd0122 WHERE bfesta=7 AND aacuen=$cuenta AND bfempr={$_SESSION['empresa']}";
	$query_act = pg_query($sql);
	while($row_act = pg_fetch_array($query_act)){

?>		
	<div class="table-responsive">
		<?Php 
									
			$var 		= 0;
			$fecha 		= 'current_date';

			$capital    = 0;   
			$mora 	    = 0;   
			$punitorio  = 0;   
			$gasto 	    = 0;   
			$abogado    = 0;   
			$judiciales = 0;   
			$iva        = 0;   
			$total      = 0;   

			echo $sql = "SELECT * from public.get_mora({$row_act['operacion']}::bigint,$fecha::date,{$row_act['tasa']}::numeric);";

			$query  = pg_query($sql);
			while($row=pg_fetch_array($query)) {

				$capital   +=$row['capital'];
				$mora 	   +=$row['mora'];
				$punitorio +=$row['punitorio'];
				$gasto 	   +=$row['gasto'];
				$abogado   +=$row['abogado'];
				$judiciales+= 0;//$row['judiciales'];
				$iva       +=$row['iva'];
				$total     +=$row['total'];

				if($var!=$row['operacion']){

					$var=$row['operacion']
		?>	
		<br>
		<table>
			<tbody>
				<tr>
					<th scope="row">Operacion:</th>
					<td align="right"><?= $row_act['operacion'];?></td>
				</tr>
				<tr>
					<th scope="row">Cuotas:</th>
					<td align="right"><?= $row_act['cantidad']-$row_act['pendiente'].'/'.$row_act['cantidad'];?></td>
				</tr>
				<tr>
					<th scope="row">Calificación:</th>
					<td align="right"><?= $row_act['calificacion'];?></td>
				</tr>
			</tbody>
		</table>

		<table class="table table-striped table-sm">			
			<thead>	
				<tr class="table-warning">
					<th scope="col">CUOTA</th>
					<th scope="col">ESTADO</th>
					<th scope="col">VENCIMIENTO</th>
					<th scope="col">PAGADO</th>
					<th scope="col">ATRASO</th>
					<th scope="col">CAPITAL</th>
					<th scope="col">MOR+PUN+GTO+IVA</th>
					<th scope="col">HON.ABOG.</th>
					<th scope="col">SALDO</th>
				</tr>
			</thead>
			<tbody>	
			<?Php
				}
			?>	
				<tr>
					<td align="center"><?= $row['cuota'];?></td>
					<td align="center">
						<?Php 
							if ($row['estado']=='PENDIENTE' && $row['atraso']>0) {
								echo '<span class="text-danger">'.$row['estado'].'</span>';
							}else{
								echo '<span class="text-info">'.$row['estado'].'</span>';	
							}
						?>
						
					</td>
					<td align="center"><?= $row['vencimiento'];?></td>
					<td align="center"><?= ($row['pagado']>='2001-01-01')?$row['pagado']:'';?></td>
					<td align="center"><?= $row['atraso'];?></td>
					<td align="center"><?= number_format($row['capital'],0,',','.');?></td>
					<td align="center">
						<?Php 
							if($row['atraso']==0){
								echo 0;
							}else {
								echo number_format($mora+$punitorio+$gasto+$iva,0,',','.');								
							}
						?>
					</td>	
					<td align="center"><?= number_format($abogado,0,',','.');?></td>
					<td align="center"><?= number_format($total,0,',','.');?></td>
				</tr>
		<?Php 
			} 
		?>
			</tbody>
			<tfoot>
				<tr class="table-warning">
					<td>TOTAL</td>
					<td colspan="4"></td>
					<td><?= number_format($capital,0,',','.');?></td>
					<td><?= number_format($mora+$punitorio+$gasto+$iva,0,',','.');?></td>
					<td><?= number_format($abogado,0,',','.');?></td>
					<td><?= number_format($total,0,',','.');?></td>
				</tr>
			</tfoot>					
		
		</table>		
	</div>
<?Php 
	}
?>
</div>
<?php include('../footer.php');?>