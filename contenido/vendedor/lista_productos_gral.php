<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../../controlador/main.php');
require_once( CONTROLADOR . 'vendedores.php');
$datos = new Vendedores();

$pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

if(isset($_POST['contenido']) && strlen($_POST['contenido'])>1){
	$contenido = $_POST['contenido'];
}else{
	$contenido = "0";	
}


if(isset($_POST['familia'])){

	$_SESSION['familia'] = $_POST['familia'];
}
if(!isset($_SESSION['familia'])){
	$_SESSION['familia'] = 0;
}
$familia = $_SESSION['familia'];


if(!isset($_SESSION['filtro_stock'])){ 
	$_SESSION['filtro_stock'] = 0;
}

$filtro_stock = $_SESSION['filtro_stock'] = (isset($_POST['filtro_stock'])) ? $_POST['filtro_stock'] : $_SESSION['filtro_stock'];
$filtro_stock_check = ( $_SESSION['filtro_stock'] == 0) ? "":"checked";

if(strlen($contenido)>2){
	if(isset($_POST['codigo']) && $_POST['codigo']>=600000){

		$filtro_add =  $_POST['codigo'];

	}else{
		if(isset($_POST['descripcion'])){
			$filtro_add =  strtoupper($_POST['descripcion']);
		}
	}
}

$products = $datos->lista_productos_gral($filtro_stock, $contenido, $familia, $pagina);

?>
<div class="container-fluid">
	<form>
		<div class="form-row">
			<div class="form-group col-md-2 mb-2">
				<input type="number" class="form-control form-control-sm" id="buscar_codigo" name="codigo" placeholder="Código" min="600000" max="94000000">
			</div>

			<div class="form-group col-md-9 mb-2">
				<input type="text" class="form-control form-control-sm" id="buscar_descripcion" name="descripcion" placeholder="Descripción">
			</div>
			<div class="form-group col-md-1 mb-2">
				<button type="button" class="btn btn-primary btn-sm" onclick="buscar()">Buscar</button>
			</div>
		</div>
	</form>
	
	<div class="form-group mb-2">
		<label for="familia">Familia</label>
		<select class="form-control form-control-sm" id="familia" onchange="familia()">
			<option value="0">TODAS</option>
			<?php 
				$familia_data = $datos->lista_familias();
				for ($i=0; $i < count($familia_data); $i++) {
					$selected = ($familia_data[$i]['cod_familia'] == $familia) ? 'selected' : '';
			?>	
					<option value="<?= $familia_data[$i]['cod_familia'];?>" <?= $selected;?>   ><?= $familia_data[$i]['familia'];?></option>
			<?php
				}
			?>		
		</select>
	</div>

	<div class="custom-control custom-switch">
		<input type="text" id="filtro_contenido" value="<?= $contenido;?>" hidden>
		<input type="checkbox" class="custom-control-input" id="con_stock" onclick="con_stock()" <?= $filtro_stock_check;?> >
		<label class="custom-control-label" for="con_stock">Con Stock</label>

	</div>
	<br>

	<?php 
	if(isset($filtro_add)){
		?>		
		<form>
			<input type="text" hidden="hidden" name="codigo" value="">
			<input type="text" hidden="hidden" name="descripcion" value="">	 
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				Filtro : <strong><?= $filtro_add; ?></strong>
				<button type="button" class="close" aria-label="Cerrar" onclick="buscar()">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</form>	
		<?php
	}



	foreach ($products as $product) 
	{
		?>
		<div class="container border mb-3">
			<div class="row">
				<div class="col">
					<small>
						<b><?= $product['codigo'].'</b> '.$product['nombre_producto'];?>
					</small>
				</div>
			</div>

			<div class="row bg-light text-dark">	
				<div class="col text-left">
					<small>Stock: <?= $product['stock'];?></small>
				</div>	
				<div class="col text-right">
					<small>Precio: <?= number_format($product['precio'],0,',','.');?></small>
				</div>
			</div>
		</div>
		<?php
	}
	?>
</div>
<?php

//$total_items = $products['cantidad_total'];
//include('paginador.php');
//require('../../footer.php'); 
?>

<script>
	function pagina(pagina){
		$.ajax({
			type:'POST',
			url:"lista_productos_gral.php",
			data:{
				filtro : 'filtro',
				pagina : pagina
			},
			success:function(resp){
				$("#body_productos").html(resp);
			}
		});
	}

	function familia(){

		var familia = document.getElementById('familia').value;
		var contenido = document.getElementById('filtro_contenido').value;

		$.ajax({
			type:'POST',
			url:"lista_productos_gral.php",
			data:{
				filtro : 'filtro',
				familia: familia,
				//descripcion: descripcion,
				contenido : contenido,
				pagina : 1
			},
			success:function(resp){
				$("#body_productos").html(resp);
			}
		});
	}


	function con_stock(){
		var con_stock = document.getElementById('con_stock');
		var contenido = document.getElementById('filtro_contenido').value;
		var filtro_stock = 0;
		if(con_stock.checked == true){
			filtro_stock = 1; 
		}

		$.ajax({
			type:'POST',
			url:"lista_productos_gral.php",
			data:{
				filtro : 'filtro',
				filtro_stock : filtro_stock,
				contenido : contenido,
				pagina : 1
			},
			success:function(resp){

				$("#body_productos").html(resp);
				if(filtro_stock==1){
					document.getElementById('con_stock').checked = true;	
				}else{
					document.getElementById('con_stock').checked = false;
				}
			}
		});		
	}

	function buscar(){
		var codigo = document.getElementById('buscar_codigo').value;	
		var descripcion = document.getElementById('buscar_descripcion').value;
		var forma_filtro = "0";
		var contenido = "";

		if(!codigo){
			codigo = 0;
		}else{
			if(codigo>=600000){
				contenido =  codigo.toString();
				contenido = "1_"+contenido;
			}else{
				codigo = 0;
			}			
		}	

		if(!descripcion){
			descripcion = "";
		}else{
			if(descripcion.length>0){
				descripcion = descripcion.toUpperCase();
				contenido = "2_"+descripcion;
			}else{
				descripcion = "";	
			}
		}

		$.ajax({
			type:'POST',
			url:"lista_productos_gral.php",
			data:{
				filtro : 'filtro',
				codigo: codigo,
				descripcion: descripcion,
				contenido : contenido,
				pagina : 1
			},
			success:function(resp){
				$("#body_productos").html(resp);
			}
		});
	}
</script>