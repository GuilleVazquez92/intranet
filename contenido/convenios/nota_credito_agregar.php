<?php 

	require('../../controlador/main.php');
	require( CONTROLADOR . 'convenio.php');
	$convenio = new Convenios();
	$convenio->id = $_COOKIE['id'];
	$convenio->proveedor = $_POST['proveedor'];
	$convenio->factura = $_POST['factura'];
	$datos_factura = $convenio->consulta_factura();

	if(isset($_POST['accion']) && $_POST['accion']=='Agregar'){

		$i = 0;
		if(isset($_SESSION['nc'])){
			$i += count($_SESSION['nc']);
		}
			$_SESSION['nc'][$i]['codigo'] 		= $_POST['codigo'];			
			$_SESSION['nc'][$i]['descripcion'] 	= $_POST['descripcion'];
			$_SESSION['nc'][$i]['cantidad']		= $_POST['cantidad'];
			$_SESSION['nc'][$i]['precio'] 		= $_POST['precio'];
	}


	if(isset($_POST['accion']) && $_POST['accion']=='Quitar'){

		$i = 0;
	
		foreach ($_SESSION['nc'] as $key ) {
			if($i==0){
				unset($_SESSION['nc']);
			}

			if($key['codigo']!=$_POST['codigo']){
				$_SESSION['nc'][$i]['codigo'] 		= $key['codigo'];			
				$_SESSION['nc'][$i]['descripcion'] 	= $key['descripcion'];
				$_SESSION['nc'][$i]['cantidad']		= $key['cantidad'];
				$_SESSION['nc'][$i]['precio'] 		= $key['precio'];
				$i++;
			}
		}	
	}

	if(isset($_POST['accion']) && $_POST['accion']=='Procesar' && isset($_SESSION['nc'])){

		
		$convenio->id 			= $_COOKIE['id'];
		$convenio->usuario		= $_COOKIE['usuario'];
		$convenio->fecha 		= $_POST['fecha'];
		$convenio->proveedor 	= $_POST['proveedor'];
		$convenio->nc 			= $_POST['nota_credito'];
		$convenio->factura 		= $_POST['factura'];

		foreach ($_SESSION['nc'] as $key) {
			unset($_SESSION['nc']);
			$convenio->codigo 	= $key['codigo'];			
			$convenio->cantidad	= $key['cantidad'];
			$convenio->precio 	= $key['precio'];
			$convenio->guardar_nc_procesar();
		}
	}

	if(!isset($_SESSION['nc'])  || count($_SESSION['nc'])==0) {
?>	
		<script>
			document.getElementById('guardar_nc').disabled = true;
		</script>
<?php
	}else{
?>	
		<script>
			document.getElementById('guardar_nc').disabled = false;
		</script>	
<?php 
	}

	if(count($datos_factura)>0){

		if(!isset($_SESSION['factura'])){
			$_SESSION['factura'] = $_POST['factura'];
		}

		if($_SESSION['factura'] != $_POST['factura']){
			unset($_SESSION['nc']);
			$_SESSION['factura'] = $_POST['factura'];	
		}
?>		
		<table class="table table-bordered table-sm">
			<tr class="table-warning">
				<th width="14%" class="text-center">Codigo</th>
				<th width="38%">Descripción</th>
				<th width="10%" class="text-center">Cant.</th>
				<th width="14%" class="text-center">Precio Unit.</th>
				<th width="14%" class="text-center">Precio Total</th>
				<th width="10%"></th>
			</tr>
<?php
			for ($i=0; $i < count($_SESSION['nc']); $i++) {
				$nc_total_producto =$_SESSION['nc'][$i]['cantidad']*$_SESSION['nc'][$i]['precio'];
				$nc_total +=$nc_total_producto;

?>				<tr>
					<td class="text-center"><?= $_SESSION['nc'][$i]['codigo'];?></td>
					<td><?= $_SESSION['nc'][$i]['descripcion'];?></td>
					<td class="text-center"><?= $_SESSION['nc'][$i]['cantidad'];?></td>
					<td class="text-right"><?= number_format($_SESSION['nc'][$i]['precio'],0,',','.');?></td>
					<td class="text-right"><?= number_format($nc_total_producto,0,',','.');?></td>
					<td class="text-center"><button class="btn btn-danger btn-sm" onclick="quitar_nc_producto(<?= $_SESSION['nc'][$i]['codigo'];?>)">Quitar</button></td>
				</tr>
<?php
			}
?>
			<tr>
				<td>
					<div class="col-xs-1">
						<input id="ind_codigo" type="number" class="form-control form-control-sm sin-borde text-center" readonly="readonly">
					</div>	
				</td>
				<td>
					<select name="" id="ind_producto" required="required" class="form-control form-control-sm" onchange="validar_producto()" >
						
				<?php 
						$control = 0;
						for ($i=0; $i < count($datos_factura) ; $i++) { 
							
							$mostrar = 'S';
							
							if(isset($_SESSION['nc'])){

								for ($x=0; $x < count($_SESSION['nc']); $x++) { 
									
									if($datos_factura[$i]['codigo']==$_SESSION['nc'][$x]['codigo']){
										$mostrar = 'N';
									}   		
								}
							}

							if($mostrar == 'S'){
								if($control==0){
									echo "<option></option>";
									$control = 1;
								}
				?>					
								<option value="<?= $i.'-'.$datos_factura[$i]['producto'];?>"><?= $datos_factura[$i]['producto']?></option>
				<?php
							}
						}
				?>	
				</select>
				</td>
				<td><div class="col-xs-1"><input id="ind_cantidad" type="number" class="form-control form-control-sm text-center" min="1" onchange="validar_producto()" readonly="readonly"></div></td>
				<td><div class="col-xs-1"><input id="ind_precio" type="number" class="form-control form-control-sm text-right" min="1" onchange="validar_producto()" readonly="readonly"></div></td>
				<td><div class="col-xs-1"><input id="ind_precio_total" type="number" class="form-control form-control-sm sin-borde text-right" value="" readonly="readonly"></div></td>
				<td class="text-center"><button class="btn btn-success btn-sm" id="nc_agregar" onclick="agregar_nc_producto()" style="display: none;">Agregar</button></td>
			</tr>				
			<tr>
				<td colspan="4">TOTAL</td>
				<td class="text-right"><b><?= number_format($nc_total,0,',','.');?></b></td>
			</tr>
		</table>
<?php
	}else{
?>		
	<div class="alert alert-danger" role="alert">
		No se encontro el número de factura con el proveedor!!!
	</div>	
<?php
	}
 ?>
<script>
	
		function validar_producto(){

			var data 			= <?= json_encode($datos_factura);?>;
			var producto 		= document.getElementById('ind_producto').value;
			var producto 		= producto.split('-');
			var i 				= producto[0];
			var codigo 			= document.getElementById('ind_codigo');
			var cantidad 		= document.getElementById('ind_cantidad');
			var precio 			= document.getElementById('ind_precio');
			var total 			= document.getElementById('ind_precio_total');
			var boton 			= document.getElementById('nc_agregar');

			if(i.length == 0){
				boton.style.display = "none";
			}else{
				boton.style.display = "inline";
			}

			if(codigo.value != data[i]['codigo']){
				codigo.value 	= data[i]['codigo'];
				cantidad.value 	= data[i]['cantidad'];
				cantidad.max 	= data[i]['cantidad'];
		 		precio.value   	= data[i]['precio'];
			 	precio.max   	= data[i]['precio'];
				cantidad.readOnly = false;
			 	precio.readOnly = false;

			} 	

			if(cantidad.value.length==0){
				cantidad.value = data[i]['cantidad']
			}

	 		if(precio.value.length==0){
	 			precio.value = data[i]['precio'];
	 		}

		 	total.value  = cantidad.value*precio.value;
		}


		function agregar_nc_producto(){

			var codigo 		= document.getElementById('ind_codigo');
			var producto 	= document.getElementById('ind_producto').value;
			var producto 	= producto.split('-');
			var cantidad 	= document.getElementById('ind_cantidad');
			var precio 		= document.getElementById('ind_precio');
			var nc_proveedor= document.getElementById('nc_proveedor');
			var fact_suc 	= document.getElementById('fact_suc');
			var fact_boca 	= document.getElementById('fact_boca');
			var fact_numero = document.getElementById('fact_numero');
			var factura 	= fact_suc.value +'-'+ fact_boca.value+'-'+ fact_numero.value;	

			$.ajax({
				type:'POST',
				url:"nota_credito_agregar.php",
				data:{
					accion 		: 'Agregar',
					codigo 		: codigo.value,
					descripcion : producto[1],
					cantidad 	: cantidad.value,
					precio 		: precio.value,
					proveedor 	: nc_proveedor.value, 
					factura 	: factura
				},
				success:function(resp){
					 $("#add-body").html(resp);
				}
			}); 
		}

		function quitar_nc_producto(e){

			var nc_proveedor= document.getElementById('nc_proveedor');
			var fact_suc 	= document.getElementById('fact_suc');
			var fact_boca 	= document.getElementById('fact_boca');
			var fact_numero = document.getElementById('fact_numero');
			var factura 	= fact_suc.value +'-'+ fact_boca.value+'-'+ fact_numero.value;	
			$.ajax({
				type:'POST',
				url:"nota_credito_agregar.php",
				data:{
					accion 		: 'Quitar',
					codigo 		: e,
					proveedor 	: nc_proveedor.value, 
					factura 	: factura
				},
				success:function(resp){
					 $("#add-body").html(resp);
				}
			}); 
		}
</script>