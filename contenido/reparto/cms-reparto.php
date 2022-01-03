<?php  
	require('../../header.php');
	require( CONTROLADOR . 'reparto.php');
	$reparto = new Logistica();
	
	if(isset($_POST['carro']) && isset($_POST['cod_chofer'])){

		$reparto->carro 	 = $_POST['carro'];
		$reparto->cod_chofer = $_POST['cod_chofer'];
		$reparto->asignar_chofer();
		
	}
?>

<div class="container-fluid">
	<br>
	<div class="row">
		<div class="col-8">

			<h4>Control y asignación de clientes</h4>
			
			<table class="table table-sm table-hover table-bordered id="cms">

				<thead>
					<tr class="table-primary">
						<td>Cliente</td>
						<td>Carro</td>
						<td>Factura</td>
						<td>Fecha Factura</td>
						<td>Mover a:</td>
					</tr>
				</thead>

				<tbody>
				<?php
					
					$var_chofer = "QWERTY";
					$datos = $reparto->clientes();
					$chofer = $reparto->chofer();

					for ($i=0; $i < count($datos); $i++) 
					{
						if($var_chofer != trim($datos[$i]['chofer']))
						{
							$var_chofer = trim($datos[$i]['chofer']);
				?>
							<tr class="">
								<td colspan="5"><span><b>CHOFER : <?= $var_chofer;?></b></span></td>
							</tr>		
				<?php 
						}
				?>	
					<tr class="<?= $datos[$i]['clase'];?>">
						<td><?= $datos[$i]['cuenta'].' - '.$datos[$i]['cliente'];?></td>
						<td><?= $datos[$i]['carro'];?></td>
						<td><?= $datos[$i]['factura'];?></td>
						<td><?= $datos[$i]['fech_factura'];?></td>
						<td>
							<select name="chofer" id="<?= $datos[$i]['carro'];?>" onchange="change('<?= $datos[$i]['carro'];?>')">
									<?php  
										for ($x=0; $x < count($chofer) ; $x++) { 
											if(trim($chofer[$x]['chofer'])==trim($var_chofer)){

												echo "<option value='".$chofer[$x]['cod_chofer']."' selected>".$chofer[$x]['chofer']."</option>";

											}else{

												echo "<option value='".$chofer[$x]['cod_chofer']."'>".$chofer[$x]['chofer']."</option>";

											}
										} 
									?>
							</select>	
						</td>
					</tr>
				<?php 
					}
				?>
				</tbody>
			</table>	
		</div>
	</div>
</div>
<br>
<br>
<?php 
	require('../../footer.php');
?>

<script>
	function change(carro){
		
		var cod_chofer = document.getElementById(carro).value;
		alert('el codigo de chofer es : ' + cod_chofer + ' para el carro numero : ' + carro);

	   	$.ajax({
			url: 'cms-reparto.php',
			method: 'POST',
			dataType: 'text',
			data:{
				carro:carro,
				cod_chofer:cod_chofer

			}, success: function(response){
				console.log(response);

			}
		});
	}
</script>						