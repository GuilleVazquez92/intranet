<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require('../../header.php');
	
	$selected1 = ""; 
	$selected2 = "";

	if(isset($_POST['tipo']) && isset($_POST['precio_lista'])){

		$precio_lista 	= $_POST['precio_lista'];
		$tipo 			= $_POST['tipo'];

		if($tipo == 1){

			$selected1 = "selected"; 
			$selected2 = "";

		}else{

			$selected1 = ""; 
			$selected2 = "selected";
		}

		switch ($_POST['tipo']) {
			case '1': 

				# electrodomestico
				$tasa = 4.5;
				$maximo_cuotas = 16;
				break;
			
			case '2': 
				# motos
				$tasa = 2.5;
				$maximo_cuotas = 25;
				break;

			default:
				# efectivo
				$tasa = 3;
				$maximo_cuotas = 12;
				break;
		}
	}	
?>
	<div class="container">
	<br>
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
	    <li class="breadcrumb-item active" aria-current="page">Calcular Cuota</li>
	  </ol>
	</nav>

	<form method="POST" action="">
		<div class="form-group row">
			<div class="col-sm-3">
				<input type="number" class="form-control form-control-sm mb-2" id="precio_lista" name="precio_lista" placeholder="Precio Lista" min="0" 
				required="required" value="<?= $precio_lista;?>">
			</div>
			<div class="col-sm-3">
				<select class="custom-select custom-select-sm mb-2" id="tipo" name="tipo" required="required">
				  <option value="1" <?= $selected1;?> >Mercaderias</option>
				  <option value="2" <?= $selected2;?> >Motos</option>
				</select>
			</div>
			<div class="col-sm-6">
				<button type="submit" class="btn btn-primary btn-sm mb-2" name="calcular">Calcular</button>
			</div>
		</div>
	</form>
	<?php 
		if(isset($_POST['tipo']) && isset($_POST['precio_lista'])){
	 ?>
	<table class="table table-resposive-sm">
		<thead>
			<tr>
				<th class="text-center">Cant.Cuota</th>
				<th class="text-center">Con entrega</th>
				<th class="text-center">A 30 dias</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			for ($i=0; $i < $maximo_cuotas; $i++) { 

				# Calculo de cuota con entrega
				$cuota1 = $i+5;	
				$valor1 = ($precio_lista*$tasa*$i/100)+$precio_lista;
				$valor1 = round((((($valor1-$precio_lista)*0.1)+$valor1)/$cuota1)+499,-3);

				$valor2 = ($precio_lista*$tasa*($i+1)/100)+$precio_lista;
				$valor2 = round((((($valor2-$precio_lista)*0.1)+$valor2)/$cuota1)+499,-3);	
		?>
			<tr>
				<td class="text-center"><?= $cuota1;?></td>
				<td class="text-center"><?= number_format($valor1,0,",",".");?></td>
				<td class="text-center"><?= number_format($valor2,0,",",".");?></td>
			</tr>
		<?php 
				if($cuota1==$maximo_cuotas){
					break;
				}	
			}
		 ?>
		</tbody>
	</table>
	<?php
		}
		echo '</div>';
		require('../../footer.php'); 
	?>