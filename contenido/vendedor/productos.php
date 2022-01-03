<?php 
	//	error_reporting(E_ALL);
	//	ini_set('display_errors', '1');
	//require('../../header.php');
$data = file_get_contents("../../../../intranet/api/productos.json");
$products = json_decode($data, true);

if(isset($_POST['filtro'])){

	if(strlen($_POST['codigo'])+strlen($_POST['descripcion'])>=2){

		if(isset($_POST['codigo']) && $_POST['codigo']>=2010000){

			$filtro_add =  $_POST['codigo'];
			$products = array_filter($products, function ($item) use ($filtro_add) {
				return ($item['codigo'] == $filtro_add);
			});

		}else{

			$filtro_add =  strtoupper($_POST['descripcion']);
			$products = array_filter($products, function ($item) use ($filtro_add) {
				if (stripos($item['nombre_producto'], $filtro_add) !== false) {
					return true;
				}
				return false;
			});
		}
	}	
}
?>
<div class="container-fluid">
	
	<form>
		<div class="form-row">
			<div class="form-group col-md-2 mb-2">
				<input type="number" class="form-control form-control-sm" id="buscar_codigo" name="codigo" placeholder="Código" min="2010000" max="4000000">
			</div>

			<div class="form-group col-md-9 mb-2">
				<input type="text" class="form-control form-control-sm" id="buscar_descripcion" name="descripcion" placeholder="Descripción">
			</div>
			<div class="form-group col-md-1 mb-2">
				<button type="button" class="btn btn-primary btn-sm" onclick="buscar()">Buscar</button>
			</div>
		</div>
	</form>

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
	?>
	<?php
	foreach ($products as $product) { 
		?>
		<form method="POST" action="">
			<input type="text" name="codigo" value="<?= $product['codigo'];?>" hidden>
			<input type="text" name="descripcion" value="<?= $product['nombre_producto'];?>" hidden>
			<input type="text" name="cantidad" value="1" hidden>
			<input type="text" name="precio" value="<?= $product['precio'];?>" hidden>

			<div class="container border mb-2">
				<div class="row bg-light text-dark">
					<div class="col-8">
						<small>
							<b><?= $product['codigo'].'</b> '.$product['nombre_producto'];?>
						</small>

					</div>

					<div class="col-4 text-right">
						<button type="submit" name="agregar" class="btn btn-success btn-sm my-2"><small>Agregar</small></button>
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
		</form>
		<?php
	}
	?>
</div>
<!--

	"codigo":3000895
	"nombre_producto":"BICICLETA ELIPTICAL ATEL5E ATHLETIC TRAINEE  90KG"
	"descripcion":""
	"precio":815000
	"precio_contado":815000
	"hasta_cuotas":16
	"cuota_desde":79000
	"codfamilia":3
	"familia":"GIMNASIA"
	"codclase":2001
	"clase":"EQUIPOS DE GIMNASIA"
	"codmarca":44
	"marca":"ATHLETIC"

-->
<script>
	function buscar(){
		var codigo = document.getElementById('buscar_codigo').value;	
		var descripcion = document.getElementById('buscar_descripcion').value;

		$.ajax({
			type:'POST',
			url:"productos.php",
			data:{
				filtro : 'filtro',
				codigo: codigo,
				descripcion: descripcion
			},
			success:function(resp){
				$("#resultado").html(resp);
				//window.location.reload(1);
			}
		});
	}
</script>