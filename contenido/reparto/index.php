<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 
require('../../header.php');
require( CONTROLADOR . 'reparto.php');
$reparto = new Logistica();
$reparto->chofer = $_COOKIE['usuario'];
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="">Inicio</a>
			</li>
		</ol>
	</nav>
	
	<div class="row">
		<div class="col-sm-2">
			<form action="carro.php" method="POST" class="">
				<div class="form-group">	
					<label for="carro">Ingrese el carro</label> 
					<input type="number"  class="form-control form-control-sm" name="carro" placeholder=""/>
					<button type="submit" class="btn btn-primary btn-sm mt-2">Buscar</button>
				</div>
			</form>
		</div>
	</div>
	
	<br>
	<div class="row">
		<?php
		$datos = $reparto->a_entregar();
		foreach ($datos as $key) {
			?>
			<div class="col-sm-3 mb-2">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-6">Carro : <?= $key['carro'];?></div>
							<div class="col-6 text-right">
								<small>
									<a href="#" id="detalles" data-toggle="modal" data-target="#Modal"  onclick="accion(this.id,<?= $key['carro']?>,<?= $key['cuenta'];?>)">Ver detalles</a>
								</small>
							</div>
						</div>
					</div>
					<div class="card-body">
						<h6 class="card-title"><?= $key['cliente'];?></h6>
						<p class="card-text"><?= ucwords(strtolower($key['entrega']))?></p>
						<small class="form-text text-muted">
							<?= (strlen(trim($key['telefono']))>=6) ? "<b>teléfono :</b>{$key['telefono']}<br>": "&nbsp;<br>";?>
							<?= (strlen(trim($key['celular']))>=6) ? "<b>celular :</b>{$key['celular']}<br>": "&nbsp;<br>";?>
							<br>
							<div class="table-resposive">
								<table class="table table-sm table-borderless">
									<tr class="table-warning">
										<th>Producto</th>
										<th class="text-center">Cant</th>
									</tr>
									<?php 
									$productos = $reparto->consultar_carrito_productos($key['carro']);

									for ($i=0; $i < 5 ; $i++) { 
										echo "<tr>";
										if(isset($productos[$i]['producto'])){
											?>		
											<td><?= $productos[$i]['producto'];?></td>
											<td class="text-center"><?= $productos[$i]['cantidad'];?></td>
											<?php
										}else{
											?>		
											<td>&nbsp;</td>
											<td class="text-center">&nbsp;</td>
											<?php
										}
										echo "</tr>";
									}
									?>	
								</table>
							</div>
						</small>
					</div>
					<div class="card-footer text-right">
						<button type="button" id="entrega" class="btn btn-sm btn-success" data-toggle="modal" data-target="#Modal" onclick="accion(this.id,<?= $key['carro'];?>,<?= $key['cuenta'];?>)">Entregar</button>
						<button type="button" id="no_entrega" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#Modal" onclick="accion(this.id,<?= $key['carro'];?>,<?= $key['cuenta'];?>)">No Entregar</button>
					</div>
				</div>
			</div>
			<?php
		}	
		?>
		<div class="modal fade" id="Modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog" >
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="ModalLabel"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">											
					</div>
					<div class="modal-footer">
						<button type="button" id="m1" class="btn btn-success" onclick="accion('entrega',<?= $key['carro'];?>,<?= $key['cuenta'];?>)">Entregar</button>
						<button type="button" id="m2" class="btn btn-danger" onclick="accion('no_entrega',<?= $key['carro'];?>,<?= $key['cuenta'];?>)">No Entregar</button>
						<button type="button" id="m3" class="btn btn-success" onclick="">Procesar</button>
						<button type="button" id="m4" class="btn btn-danger" onclick="">Procesar</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
require('../../footer.php'); 
?>
<script type="text/javascript">

	function accion(id,carro,cuenta){

		var title;
		var url;
		var titulo = document.getElementById('ModalLabel');
		var m1 = document.getElementById('m1');
		var m2 = document.getElementById('m2');
		var m3 = document.getElementById('m3');
		var m4 = document.getElementById('m4');

		m1.style.display = "none";
		m2.style.display = "none";
		m3.style.display = "none";
		m4.style.display = "none";

		switch (id) {
			
			case 'detalles':
			title = "Detalle";	
			url = "carro.php";
			m1.style.display = "initial";
			m2.style.display = "initial";
			break;
			
			case 'entrega':
			title = "Lugar de la entrega";				
			url = "entrega.php";
			m3.style.display = "initial";
			break;
			
			case 'no_entrega':
			title = "Por qué no se entrega?";			
			url = "no_entrega.php";
			m4.style.display = "initial";
			break;
		}		

		titulo.innerHTML = title;
		$.ajax({
			type:'POST',
			url:url,
			data:{
				cuenta : cuenta,
				carro : carro,
				opcion : id
			},
			success:function(resp){
				$(".modal-body").html(resp);	
			}
		});
	}
</script>