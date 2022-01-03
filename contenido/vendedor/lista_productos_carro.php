<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../../controlador/main.php');
require_once( CONTROLADOR . 'vendedores.php');
$datos = new Vendedores();

$pagina = (isset($_POST['pagina'])) ? $_POST['pagina'] : 1;

if(isset($_POST['contenido']) && strlen($_POST['contenido'])>1){
	//print("entra aqui");
	$contenido = $_POST['contenido'];
}else{
	$contenido = "0";	
}

if(isset($_POST['clase_venta'])){
	$_SESSION['tipo_filtro'] = $_SESSION['clase_venta'];
}

if(!$_SESSION['filtro_stock'] || !isset($_SESSION['filtro_stock'])){ 
	$_SESSION['filtro_stock'] = 0;
}

$_SESSION['filtro_stock'] = (isset($_POST['filtro_stock'])) ? $_POST['filtro_stock'] : $_SESSION['filtro_stock'];
$filtro_stock_check = ( $_SESSION['filtro_stock'] == 0) ? "":"checked";

if(strlen($contenido)>2){
	if(isset($_POST['codigo']) && $_POST['codigo']>=1000000){
		$filtro_add =  $_POST['codigo'];
	}else{
		$filtro_add =  strtoupper($_POST['descripcion']);
	}
}
$products = $datos->lista_productos($_SESSION['tipo_filtro'],$_SESSION['filtro_stock'],$_SESSION['detalle_carrito']['filtro'],$contenido,$pagina);
?>
<div class="container-fluid">
	<form>
		<div class="form-row">
			<div class="form-group col-md-2 mb-2">
				<input type="number" class="form-control form-control-sm" id="buscar_codigo" name="codigo" placeholder="Código" min="1000000" max="94000000">
			</div>

			<div class="form-group col-md-9 mb-2">
				<input type="text" class="form-control form-control-sm" id="buscar_descripcion" name="descripcion" placeholder="Descripción">
			</div>
			<div class="form-group col-md-1 mb-2">
				<button type="button" class="btn btn-primary btn-sm" onclick="buscar()">Buscar</button>
			</div>
		</div>
	</form>

	<div class="custom-control custom-switch">
		<input type="text" id="filto_contenido" value="<?= $contenido;?>" hidden>
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
			</div>
				<button type="button" class="close" aria-label="Cerrar" onclick="buscar()">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</form>	
				<?php 		
		foreach ($products as $product) {

		
		if ($filtro_add == $product['codigo']) {

			$datos = $product['codigo'].'_'.$product['precio'].'_'.$product['max_desc'].'_'.$product['tipo_filtro'];


		?>
		
		<div class="container border mb-2">
			<div class="row bg-light text-dark">
				<div class="col-8">
					<small>
						<b><?= $product['codigo'].'</b> '.$product['nombre_producto'];?>
					</small>

				</div>

				<div class="col-4 text-right">
					<button type="button" name="agregar" class="btn btn-success btn-sm my-2" onclick="producto_agregar('<?= $datos;?>');"><small>Agregar</small></button>
				</div>
			</div>

			<div class="row bg-warning text-dark">	
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
	}
}
	foreach ($products as $product) {

		$datos = $product['codigo'].'_'.$product['precio'].'_'.$product['max_desc'].'_'.$product['tipo_filtro'];

		?>
		<div class="container border mb-2">
			<div class="row bg-light text-dark">
				<div class="col-8">
					<small>
						<b><?= $product['codigo'].'</b> '.$product['nombre_producto'];?>
					</small>

				</div>

				<div class="col-4 text-right">
					<button type="button" name="agregar" class="btn btn-success btn-sm my-2" onclick="producto_agregar('<?= $datos;?>');"><small>Agregar</small></button>
				</div>
			</div>

			<div class="row bg-warning text-dark">	
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

$total_items = $products['cantidad_total'];
include('paginador.php');
?>

<script>
	function pagina(pagina){
		$.ajax({
			type:'POST',
			url:"lista_productos_carro.php",
			data:{
				filtro : 'filtro',
				pagina : pagina
			},
			success:function(resp){
				$("#resultado").html(resp);
			}
		});
	}

	function con_stock(){

		var con_stock = document.getElementById('con_stock');
		var contenido = document.getElementById('filto_contenido').value;
		var filtro_stock = 0;
		if(con_stock.checked == true){
			filtro_stock = 1; 
		}

		$.ajax({
			type:'POST',
			url:"lista_productos_carro.php",
			data:{
				filtro : 'filtro',
				filtro_stock : filtro_stock,
				contenido : contenido,
				pagina : 1
			},
			success:function(resp){

				$("#resultado").html(resp);
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
			if(codigo>=1000000){
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
			url:"lista_productos_carro.php",
			data:{
				filtro : 'filtro',
				codigo: codigo,
				descripcion: descripcion,
				contenido : contenido,
				pagina : 1
			},
			success:function(resp){
				$("#resultado").html(resp);
			}
		});
	}

</script>