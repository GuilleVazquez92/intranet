<?php 
require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();
$convenio->id = $_COOKIE['id'];
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

	.sin-borde{
		border: none;
	}

</style>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Nota de Créditos</li>
		</ol>
	</nav>
	<?php 
		if($_COOKIE['cod_perfil']<90){ 
	?>
	<button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target=".bd-example-modal-xl">Agregar</button>
	<?php 
		} 
	?>
	<table class="table table-sm table-responsive-sm">
		<thead>				
			<tr class="table-warning">
				<th scope="col">FECHA</th>
				<th scope="col">PROVEEDOR</th>
				<th scope="col">NOTA CREDITO</th>
				<th scope="col">FACTURA</th>
				<th scope="col">PRODUCTO</th>
				<th scope="col" class="text-center">CANTIDAD</th>
				<th scope="col" class="text-right">PRECIO</th>
				<th scope="col" class="text-right">TOTAL</th>
			</tr>
		</thead>
		<tbody>	
			<?php
			$datos   = $convenio->nc();
			$total 	 = 0;
			$control = 0;  

			for ($i=0; $i < count($datos); $i++) {
				$total += $datos[$i]['precio_total'];
				$date=date_create($datos[$i]['fecha']);

				$id_datos = trim($datos[$i]['control']);
				if($control!=$id_datos){
					$control=$id_datos;
					?>
					<tr>
						<td class="text-left"><b><?= date_format($date,"d-m-Y");?></b></td>
						<td><b><?= $datos[$i]['proveedor'];?></b></td>	
						<td><b><?= trim($datos[$i]['nc']);?></b></td>	
						<td><b><?= trim($datos[$i]['factura']);?></b></td>

						<td class="text-left"><?= $datos[$i]['codigo'].' '.$datos[$i]['producto'];?></td>
						<td class="text-center"><?= $datos[$i]['cantidad'];?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_unitario'],0,',','.')?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_total'],0,',','.') ?></td>
					</tr>

					<?php
				}else{
					?>
					<tr>
						<td class="text-left"></b></td>
						<td></td>	
						<td></td>	
						<td></td>

						<td class="text-left"><?= $datos[$i]['codigo'].' '.$datos[$i]['producto'];?></td>
						<td class="text-center"><?= $datos[$i]['cantidad'];?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_unitario'],0,',','.')?></td>
						<td class="text-right"><?= number_format($datos[$i]['precio_total'],0,',','.') ?></td>
					</tr>
					<?php
				}
			}	
			?>
			<tr class="table-warning">
				<th colspan="7"><b>TOTAL</b></td>
					<th class="text-right"><?= number_format($total,0,',','.') ?></th>
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

		<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="myModal">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Agregar Nota de Crédito</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="input-group input-group-sm mb-1 row">
							<label for="nc_proveedor" class="col-form-label col-sm-2">Proveedor</label>
							<select name="nc_proveedor" id="nc_proveedor" class="form-control col-sm-4" required="required">
								<option value="0"></option>
								<?Php 
								$proveedor = $convenio->consulta_proveedor();
								for ($i=0; $i < count($proveedor); $i++) {
									?>
									<option value="<?= $proveedor[$i]['proveedor']?>"><?= $proveedor[$i]['descripcion']?></option>
									<?Php
								}
								?>
							</select>


							<label for="" class="col-form-label col-sm-2">Nota Crédito</label>
							<input type="text" id="nc_suc" aria-label="Sucursal" class="form-control col-sm-1" 
							maxlength="3" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
							<input type="text" id="nc_boca" aria-label="Boca" class="form-control col-sm-1" 
							maxlength="3" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
							<input type="text" id="nc_numero" aria-label="Numero" class="form-control col-sm-2" 
							maxlength="7" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
						</div>

						<div  class="input-group input-group-sm mb-1 row">
							<label for="nc_fecha" class="col-form-label col-sm-2">Fecha</label>
							<input id="nc_fecha"  name="fecha_inicial" type="date" 
							placeholder="" required="required" class="form-control col-sm-4">

							<label for="" class="col-form-label col-sm-2">Num. Factura</label>
							<input type="text" id="fact_suc" aria-label="Sucursal" class="form-control col-sm-1" 
							maxlength="3" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
							<input type="text" id="fact_boca" aria-label="Boca" class="form-control col-sm-1" 
							maxlength="3" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
							<input type="text" id="fact_numero" aria-label="Numero" class="form-control col-sm-2" 
							maxlength="7" 
							onchange="validar()" 
							onKeyPress="return soloNumeros(event)">
						</div>

						<!--<button class="btn btn-success btn-sm mb-2">Validar</button>-->
						<div id="add-body">	
						</div>
					</div>
					<div class="modal-footer">
						<!--	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>--->
						<button type="button" id="guardar_nc" class="btn btn-primary" onclick="guardar_nc_procesar()" disabled="disabled">Guardar y Procesar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 

	require('../../footer.php'); 
	?>
	<script>
		
		function soloNumeros(e){
			var key = window.Event ? e.which : e.keyCode
			return (key >= 48 && key <= 57)
		}

		function validar(){

			var valido 		= 0;
			var nc_proveedor= document.getElementById('nc_proveedor');
			var nc_fecha	= document.getElementById('nc_fecha');
			var nc_suc 		= document.getElementById('nc_suc');
			var nc_boca 	= document.getElementById('nc_boca');
			var nc_numero 	= document.getElementById('nc_numero');
			var fact_suc 	= document.getElementById('fact_suc');
			var fact_boca 	= document.getElementById('fact_boca');
			var fact_numero = document.getElementById('fact_numero');

			/** NOTA CREDITO **/
			/* Numero de Sucursal*/
			if(nc_suc.value.length>0 && nc_suc.value.length<3){
				var n_suc = nc_suc.value.padStart(3,"0");

				if(n_suc.replace(/0/g,"")!=''){
					nc_suc.value = n_suc;	
				}else{
					alert("Ingrese un numero válido");
					nc_suc.focus()
				}
			}

			/* Numero de boca*/
			if(nc_boca.value.length>0 && nc_boca.value.length<3){
				var n_boca = nc_boca.value.padStart(3,"0");
				if(n_boca.replace(/0/g,"")!=''){
					nc_boca.value = n_boca;
				}else{
					alert("Ingrese un numero válido");
					nc_boca.focus();
				}
			}

			/* Numero de Nota de credito*/
			if(nc_numero.value.length>0 && nc_numero.value.length<7){
				var n_numero = nc_numero.value.padStart(7,"0");
				if(n_numero.replace(/0/g,"")!=''){
					nc_numero.value = n_numero;	
				}else{
					alert("Ingrese un numero válido");
					nc_numero.focus();
				}
			}

			/** FACTURA **/
			/* Numero de Sucursal*/
			if(fact_suc.value.length>0 && fact_suc.value.length<3){
				var f_suc = fact_suc.value.padStart(3,"0");
				if(f_suc.replace(/0/g,"")!=''){
					fact_suc.value = f_suc;
				}else{
					alert("Ingrese un numero válido");
					fact_suc.focus();
				}
			}

			/* Numero de boca*/
			if(fact_boca.value.length>0 && fact_boca.value.length<3){
				var f_boca = fact_boca.value.padStart(3,"0");
				if(f_boca.replace(/0/g,"")!=''){
					fact_boca.value = f_boca;
				}else{
					alert("Ingrese un numero válido");
					fact_boca.focus();
				}
			}

			/* Numero de factura*/
			if(fact_numero.value.length>0 && fact_numero.value.length<7){
				var f_numero = fact_numero.value.padStart(7,"0");
				if(f_numero.replace(/0/g,"")!=''){
					fact_numero.value = f_numero;
				}else{
					alert("Ingrese un numero válido");
					fact_numero.focus();
				}
			}

			if(nc_proveedor.value == 0){
				alert("Seleccione el proveedor de la NOTA DE CREDITO");
			}

			if(nc_fecha.value.length == 0){
				alert("Ingrese la fecha de la NOTA DE CREDITO");	
			}

			if(nc_proveedor.value!=0 && nc_fecha.value.length!=0){
				if(nc_suc.value.length==3 && nc_boca.value.length==3 && nc_numero.value.length==7){
					if(nc_suc.value.replace(/0/g,"")!='' && nc_boca.value.replace(/0/g,"")!='' && nc_numero.value.replace(/0/g,"")!=''){
						if(fact_suc.value.length==3 && fact_boca.value.length==3 && fact_numero.value.length==7){
							if(fact_suc.value.replace(/0/g,"")!='' && fact_boca.value.replace(/0/g,"")!='' && fact_numero.value.replace(/0/g,"")!=''){

								var nota_credito = nc_suc.value +'-'+ nc_boca.value+'-'+ nc_numero.value;
								var factura = fact_suc.value +'-'+ fact_boca.value+'-'+ fact_numero.value;	

								$.ajax({
									type:'POST',
									url:"nota_credito_agregar.php",
									data:{
										proveedor: nc_proveedor.value, 
										factura: factura
									},
									success:function(resp){
										$("#add-body").show();
										$("#add-body").html(resp);	
									}
								});   /** FIN **/
							}	
						}
					}	
				}
			}	
		}	

		function consultar_orden(orden,codigo){
			$.ajax({
				type:'POST',
				url:"orden_consultar.php",
				data:{
					orden: orden, 
					codigo: codigo
				},
				success:function(resp){
					$(".modal-body").html(resp);	
				}
			});
		}

		function guardar_nc_procesar(){

			if(confirm("Esta seguro que desea guardar y procesar la Nota de Crédito?") == true ){

				var nc_proveedor= document.getElementById('nc_proveedor');
				var nc_fecha	= document.getElementById('nc_fecha');
				var nc_suc 		= document.getElementById('nc_suc');
				var nc_boca 	= document.getElementById('nc_boca');
				var nc_numero 	= document.getElementById('nc_numero');
				var fact_suc 	= document.getElementById('fact_suc');
				var fact_boca 	= document.getElementById('fact_boca');
				var fact_numero = document.getElementById('fact_numero');
				var btn_guardar = document.getElementById('guardar_nc').disabled = true;

				var nota_credito = nc_suc.value +'-'+ nc_boca.value+'-'+ nc_numero.value;
				var factura 	= fact_suc.value +'-'+ fact_boca.value+'-'+ fact_numero.value;	
				
				$.ajax({
					type:'POST',
					url:"nota_credito_agregar.php",
					data:{
						accion 		: 'Procesar',
						proveedor 	: nc_proveedor.value,
						fecha 		: nc_fecha.value,
						nota_credito: nota_credito, 
						factura 	: factura
					},
					success:function(resp){
						location.reload();
						 //$("#modal-body").html(resp);
						 nc_proveedor.selectedIndex = "0";
						 nc_fecha.value		= null; 
						 nc_suc.value 		= null;
						 nc_boca.value		= null; 
						 nc_numero.value  	= null;
						 fact_suc.value 	= null;
						 fact_boca.value	= null; 
						 fact_numero.value  = null;
						 $("#add-body").hide();
						 $('#myModal').modal('hide');
						}
					});
			}
		}
	</script>	