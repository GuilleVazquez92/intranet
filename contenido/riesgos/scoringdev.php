<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require('../../header.php');
require( CONTROLADOR . 'riesgos.php');
$data = new Riesgos();
$data->usuario = $_COOKIE['usuario'];
$data->perfil = $_COOKIE['cod_perfil'];
?>
<br>
<div class="container-fluid">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="index.php">Inicio</a>
			</li>
			<li class="breadcrumb-item">
				<a href="lista_scoring.php">Clientes Scoring</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Scoring</li>
		</ol>
	</nav>

	<?php
	$cuenta =3889254;
	if(isset($cuenta)){		
		$data->cuenta 			= $cuenta;
		$data->tipo_solicitud 	= 'AMPLIACION DE LINEA';
		$datos = $data->resumen();
		$datos_01 = $data->deuda();
		?>
		<span id="cuenta" class="small text-muted d-none" data-verificacion="<?= $datos['check_veri'];?>" data-analisis="<?= $datos['check_anal'];?>"><?= $datos['cuenta'];?></span>	
		<h3 class="font-weight-light"><?= $datos['cuenta'];?> - <span  id="nombre_cliente" class="datos_resumen"><?= $datos['nombre_cliente'];?></span></h3>
		<h4 id="tipo_solicitud" class="datos_resumen"><?= $data->tipo_solicitud;?></h4>
		<h4 id="response">&nbsp;</h4>
		
		<div class="row">
			<div class="col-sm-3 mb-2">
				<div class="card border-info">
					<div class="card-header bg-info text-white">Datos Básicos</div>
					<div class="card-body text-info">

						<div class="form-group row">
							<label for="edad" class="col-sm-6 col-form-label col-form-label-sm">Edad</label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="edad" placeholder="Edad" readonly value="<?= $datos['edad'];?>">
							</div>
						</div>

						<div class="form-group row">
							<label for="sexo" class="col-sm-6 col-form-label col-form-label-sm">Sexo</label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="sexo" placeholder="Sexo" readonly value="<?= $datos['sexo'];?>">
							</div>
						</div>

						<div class="form-group row">
							<label for="estado_civil" class="col-sm-6 col-form-label col-form-label-sm">Estado Civil</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="estado_civil" data-id="<?= $datos['estado_civil'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">CASADO</option>
									<option value="2">SOLTERO</option>
									<option value="3">DIVORCIADO</option>
									<option value="4">VIUDO</option>
									<option value="5">CONCUBINATO</option>
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label for="cant_hijos" class="col-sm-6 col-form-label col-form-label-sm">Cantidad de Hijos</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="cant_hijos" min="0" max="12" step="1" 
								data-id="<?= $datos['cant_hijos'];?>"	
								value="<?= $datos['cant_hijos'];?>" onchange="consultar_puntos()">
							</div>
						</div>
					</div>
					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==33 or $_COOKIE['cod_perfil']==15){
								?>
								<input class="form-check-input verificado" type="checkbox" onchange="check_verificacion()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Verificado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2">
				<div class="card border-info">
					<div class="card-header bg-info text-white">Datos Personales</div>
					<div class="card-body text-info">

						<div class="form-group row">
							<label for="vivienda" class="col-sm-6 col-form-label col-form-label-sm">Vivienda</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="vivienda" data-id="<?= $datos['vivienda'];?>" onchange="consultar_puntos()">
									<option></option>
									<option>SIN DATOS</option>
									<option>PROPIA</option>
									<option>ALQUILADA</option>
									<option>DE LOS PADRES</option>
									<option>PRESTADA</option>
								</select>
							</div>
						</div>	
						<div class="form-group row">
							<label for="servicios_basicos" class="col-sm-6 col-form-label col-form-label-sm">Servicios Básicos</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="servicios_basicos" data-id="<?= $datos['servicios_basicos'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">PROPIA</option>
									<option value="3">FAMILIAR</option>
									<option value="4">OTROS</option>
								</select>
							</div>
						</div>	
						<div class="form-group row">
							<label for="conyuge" class="col-sm-6 col-form-label col-form-label-sm">Conyuge</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="conyuge" data-id="<?= $datos['conyuge'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">TRABAJA</option>
									<option value="3">NO TRABAJA</option>
									<option value="4">NO TIENE</option>
								</select>
							</div>
						</div>	
						<div class="form-group row">
							<label for="" class="col-sm-6 col-form-label col-form-label-sm"></label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm" placeholder="" readonly style="visibility:hidden">
							</div>
						</div>
					</div>
					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==33 or $_COOKIE['cod_perfil']==15){
								?>
								<input class="form-check-input verificado" type="checkbox" onchange="check_verificacion()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Verificado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2">
				<div class="card border-info">
					<div class="card-header bg-info text-white">Datos Laborales</div>
					<div class="card-body text-info">

						<div class="form-group row">
							<label for="situacion_laboral" class="col-sm-6 col-form-label col-form-label-sm">Situación Laboral</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="situacion_laboral" data-id="<?= $datos['situacion_laboral'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">PROPIETARIO</option>
									<option value="3">JUBILADO</option>
									<option value="4">EMPLEADO</option>
									<option value="5">A DESTAJO</option>
									<option value="6">CONTRATO TEMPORAL</option>
									<option value="7">INFORMAL</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="antiguedad_lab" class="col-sm-6 col-form-label col-form-label-sm">Antiguedad Laboral</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="antiguedad_lab" data-id="<?= $datos['antiguedad_lab'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">MENOS 6 MESES</option>
									<option value="3">6 A 11 MESES</option>
									<option value="4">1 AÑO</option>
									<option value="5">2 AÑOS</option>
									<option value="6">3 AÑOS</option>
									<option value="7">4 AÑOS</option>
									<option value="8">5 AÑOS A MAS</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="mercado_laboral" class="col-sm-6 col-form-label col-form-label-sm">Mercado Laboral</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="mercado_laboral" data-id="<?= $datos['mercado_laboral'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">PRESENTA I.V.A.</option>
									<option value="3">FACTURA DE COMPRA</option>
									<option value="4">PATENTE COMERCIAL</option>
									<option value="5">APORTA I.P.S.</option>
									<option value="6">CERT.LAB. S/ I.P.S.</option>
									<option value="7">NO PRESENTA</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="" class="col-sm-6 col-form-label col-form-label-sm"></label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm" placeholder="" readonly style="visibility:hidden">
							</div>
						</div>
					</div>
					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==33 or $_COOKIE['cod_perfil']==15){
								?>
								<input class="form-check-input verificado" type="checkbox" onchange="check_verificacion()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<small class="text-muted">Verificado</small>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2">
				<div class="card border-info">
					<div class="card-header bg-info text-white">Teléfono</div>
					<div class="card-body text-info">

						<div class="form-group row">
							<label for="telefono" class="col-sm-6 col-form-label col-form-label-sm">Línea Baja</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="telefono" data-id="<?= $datos['telefono'];?>" multiple size="6" onchange="consultar_puntos()">
									<option selected></option>
									<option value="1">PARTICULAR</option>
									<option value="2">VECINO</option>
									<option value="3">LABORAL</option>
									<option value="4">FAMILIAR</option>
									<option value="5">NO TIENE</option>
								</select>
								<input type="text" class="datos_input" id="datos_telefono" value="<?= $datos['telefono'];?>" hidden>
								<small id="" class="form-text">
									Ctrl para eligir mas de una opción.
								</small>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==33 or $_COOKIE['cod_perfil']==15){
								?>
								<input class="form-check-input verificado" type="checkbox" onchange="check_verificacion()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Verificado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-3 mb-2">
				<div class="card border-warning">
					<div class="card-header bg-warning">Información Comercial</div>
					<div class="card-body text-info">

						<div class="form-group row">
							<div class="col-sm-6">
									<label for="faja" class="col-form-label col-form-label-sm">Faja Informconf</label>
									<div class="text-right">
										<?php 
											if($datos['dias_ult_consulta']>30 or is_null($datos['dias_ult_consulta'])){
										?>
												<img src="<?= IMAGE."update.png" ?>" alt="" width="22px" height="22px" onclick="faja_actualizar()">
										<?php
											}
										
											if(!is_null($datos['dias_ult_consulta'])){
										?>
										<img src="<?= IMAGE."pdf22x22.png"?>" alt="" width="22px" height="22px"  onclick="ver_pdf('<?= $datos['archivo'];?>')" />
										<?php 
											}
										?> 									
									</div>
								</div>
							<div class="col-sm-6">
								
								<input type="text" class="form-control form-control-sm datos_input" id="faja" placeholder="Faja Informconf" readonly value="<?= $datos['faja'];?>">
								<small class="text-muted">Ult.Consul. : <?= $datos['ult_consulta'];?></small>
								<small class="text-muted" id="dias_consulta"  hidden=""><?= $datos['dias_ult_consulta'];?></small>
								<small class="text-muted" id="documento" hidden=""><?= $datos['documento'];?></small>
							</div>
						</div>
						<div class="form-group row">
							<label for="cliente" class="col-sm-6 col-form-label col-form-label-sm">Cliente</label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="cliente" placeholder="Cliente" readonly value="<?= $datos['cliente'];?>">
								<div class="form-check">
									<input class="form-check-input datos_input" type="checkbox" id="nuevo_mundo" value="<?= $datos['nuevo_mundo'];?>" data-id="2" onchange="consultar_puntos()">
									<label class="form-check-label form-check-label-sm" for="nuevo_mundo">
										<small class="">Nuevo en el mundo</small>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="cuenta_bancaria" class="col-sm-6 col-form-label col-form-label-sm">Cuenta Bancaria</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="cuenta_bancaria" data-id="<?= $datos['cuenta_bancaria'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">De 6 MESES A 1 AÑO</option>
									<option value="3">De 2 AÑOS A 3 AÑOS</option>
									<option value="4">De 3 AÑOS A MAS</option>
									<option value="5">NO POSEE CUENTA</option>
								</select>

								<div class="form-check">
									<input class="form-check-input datos_input" type="checkbox" value="<?= $datos['mas_cuenta'];?>" data-id="2" id="mas_cuenta" 
									onchange="consultar_puntos()">
									<label class="form-check-label form-check-label-sm" for="mas_cuenta">
										<small class="">Mas de una cuenta</small>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="insitu" class="col-sm-6 col-form-label col-form-label-sm">In-Situ</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="insitu" data-id="<?= $datos['insitu'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">POSTIVO</option>
									<option value="2">NEGATIVO</option>
								</select>	
							</div>
						</div>
					</div>

					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==32 or $_COOKIE['cod_perfil'] == 15){
								?>
								<input class="form-check-input analizado" type="checkbox" onchange="check_analisis()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Analizado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2">
				<div class="card border-warning">
					<div class="card-header bg-warning">Información de la Solicitud</div>
					<div class="card-body text-info">
						<div class="form-group row">
							<label for="producto" class="col-sm-6 col-form-label col-form-label-sm">Producto</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="producto" data-id="<?= $datos['producto'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">PRIMERA NECESIDAD</option>
									<option value="3">CONFORT</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="mercado" class="col-sm-6 col-form-label col-form-label-sm">Mercado</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="mercado" data-id="<?= $datos['mercado'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">OBJETIVO</option>
									<option value="3">NO OBJETIVO</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="monto_cuota" class="col-sm-6 col-form-label col-form-label-sm">Monto de Cuotas</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="monto_cuota" value="<?= $datos['monto_cuota'];?>" placeholder="Monto de Cuotas" readonly>
								<div class="form-check">
									<input class="form-check-input datos_input" type="checkbox" value="<?= $datos['entrega'];?>" id="entrega" disabled>
									<label class="form-check-label form-check-label-sm" for="entrega_inicial">
										<small class="text-muted">Entrega Inicial</small>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label for="cant_cuotas" class="col-sm-6 col-form-label col-form-label-sm">Cantidad de Cuotas</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="cantidad_cuota" value="<?= $datos['cantidad_cuota'];?>" placeholder="Cantidad de Cuotas" readonly>
							</div>
						</div>

						<div class="form-group row">
							<label for="ref_comercial" class="col-sm-6 col-form-label col-form-label-sm">Referencia Comercial</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="ref_comercial" data-id="<?= $datos['ref_comercial'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SI</option>
									<option value="2">NO</option>
								</select>	
							</div>
						</div>


					</div>
					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil']==32 or $_COOKIE['cod_perfil'] == 15){
								?>
								<input class="form-check-input analizado" type="checkbox" onchange="check_analisis()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Analizado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2">
				<div class="card border-warning">
					<div class="card-header bg-warning">Información Financiera</div>
					<div class="card-body text-info">
						<div class="form-group row">
							<label for="ingreso" class="col-sm-6 col-form-label col-form-label-sm">Ingreso/Salario</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="ingreso" value="<?= $datos['ingreso'];?>" data-id="<?= $datos['ingreso'];?>" placeholder="Ingreso/Salario" onchange="consultar_puntos()">
							</div>
						</div>
						<div class="form-group row">
							<label for="mora_interna" class="col-sm-6 col-form-label col-form-label-sm">Mora INTERNA</label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="mora_interna" placeholder="Mayor mora INTERNA" value="<?= $datos['mora_interna'];?>" readonly>
							</div>
						</div>
						<div class="form-group row">
							<label for="mora_externa" class="col-sm-6 col-form-label col-form-label-sm">Mora EXTERNA</label>
							<div class="col-sm-6">
								<select class="custom-select custom-select-sm datos_select" id="mora_externa" data-id="<?= $datos['mora_externa'];?>" onchange="consultar_puntos()">
									<option></option>
									<option value="1">SIN DATOS</option>
									<option value="2">0 DIAS</option>
									<option value="3">1 A 15 DIAS</option>
									<option value="4">16 A 30 DIAS</option>
									<option value="5">31 A 60 DIAS</option>
									<option value="6">61 A 90 DIAS</option>
									<option value="7">91 A MAS</option>
									<option value="8">NUEVO</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label for="deuda_mensual" class="col-sm-6 col-form-label col-form-label-sm">Deuda Interna Mensual</label>
							<?php 
							$cuota = 0;
							$total = 0;
							foreach ($datos_01 as $d) {
								$cuota += $d['cuota'];
								$total += $d['total'];
								}
							 ?>

							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="deuda_interna" placeholder="Mayor mora INTERNA" value="<?= $cuota;?>" readonly>
							</div>

						</div>
						<div class="form-group row">
							<label for="deuda_mensual" class="col-sm-6 col-form-label col-form-label-sm">Deuda Mensual Externa</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="deuda_mensual" 
								value="<?= $datos['deuda_mensual'];?>" 
								data-id="<?= $datos['deuda_mensual'];?>" 
								placeholder="Deuda Externa Mensual" onchange="consultar_puntos()">
							</div>
						</div>
						<div class="form-group row">
							<label for="total_deuda_ex" class="col-sm-6 col-form-label col-form-label-sm">Total Deuda Externa</label>
							<div class="col-sm-6">
								<input type="number" class="form-control form-control-sm datos_input" id="total_deuda_ex" value="<?= $datos['total_deuda_ex'];?>" data-id="<?= $datos['total_deuda_ex'];?>" placeholder="Total Deuda Externa" onchange="consultar_puntos()">
							</div>
						</div>
						<div class="form-group row">
							<label for="deuda_mensual" class="col-sm-6 col-form-label col-form-label-sm">Total Deuda Interna </label>
							<div class="col-sm-6">
								<input type="text" class="form-control form-control-sm datos_input" id="total_deuda_in" placeholder="Mayor mora INTERNA" value="<?= $total;?>" readonly>
							</div>

						</div>
					</div>

					<small class="text-muted">&nbsp</small>

					<div class="card-footer">
						<div class="form-check">
							<?php 
							if($_COOKIE['cod_perfil'] == 32 or $_COOKIE['cod_perfil'] == 15){
								?>
								<input class="form-check-input analizado" type="checkbox" onchange="check_analisis()">
								<?php 
							}else{
								?>
								<input class="form-check-input" type="checkbox" disabled="">
								<?php	
							} 
							?>
							<label class="form-check-label form-check-label-sm" for="">
								<small class="text-muted">Analizado</small>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3 mb-2 bg-secundary">
				<div class="card">
					<div class="card-header bg-secundary">
						<h5 class="card-title text-center">Puntos acumulados : <span class="badge badge-success datos_resumen" id="total_puntos"></span></h5></div>
						<div class="card card-body">
							<table class="table table-sm table-borderless">
								<tr>
									<td>Ingreso BRUTO</td>
									<td class="text-right">Gs.
										<span id="ingreso_valor">0</span>
									</td>	
								</tr>
								<tr>
									<td>Deuda Externa Mensual</td>
									<td class="text-right">Gs.
										<span id="deuda_mensual_valor">0</span>
									</td>	
								</tr>
								<tr>
									<td>Total Deuda Externa</td>
									<td class="text-right">Gs.
										<span id="total_deuda_ex_valor" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Riesgo solicitado</td>
									<td class="text-right">Gs.
										<span id="riesgo_solicitado_valor" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Capacidad de Cuota REAL</td>
									<td class="text-right">Gs.
										<span id="capacidad_valor" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Saldo Cuota</td>
									<td class="text-right">Gs.
										<span id="saldo_cuota" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Limite prestable</td>
									<td class="text-right">Gs.
										<span id="limite_prestable_valor" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Saldo de Linea Total</td>
									<td class="text-right">Gs.
										<span id="saldo_total" class="datos_resumen">0</span>
									</td>	
								</tr>
								<tr>
									<td>Ultima Verificación</td>
									<td class="text-left"><span id="verificador" class="datos_resumen"><?= $datos['verificador'].'</span><br><span id="ultima_verificacion" class="datos_resumen">'.$datos['ultima_verificacion'];?></span></td>	
								</tr>
								<tr>
									<td>Ultimo Analisis</td>
									<td class="text-left"><span id="analista" class="datos_resumen"><?= $datos['analista'].'</span><br><span id="ultimo_analisis" class="datos_resumen">'.$datos['ultimo_analisis'];?></span></td>	
								</tr>								
							</table>
						</div>
						<div class="card-footer text-center">
							<?php 
							if($_COOKIE['cod_perfil'] == 33 or $_COOKIE['cod_perfil'] == 15){
								?>
								<button class="btn btn-primary" id="btn_verificado" onclick="firmar_scoring('<?= $_COOKIE['usuario'];?>','verificador')" disabled>Verificado</button>
								<?php
							} 
							if($_COOKIE['cod_perfil'] == 32 or $_COOKIE['cod_perfil'] == 15){
								
								if($_POST['tipo_solicitud']=='AMPLIACION DE LINEA'){
									?>
									<button class="btn btn-success btns_analisis" id="btn_aprobar"  onclick="ampliacion_linea('<?= $_COOKIE['usuario'];?>','analista','A')" disabled>Aprobar</button>
									<button class="btn btn-danger btns_analisis" id="btn_rechazar"  onclick="ampliacion_linea('<?= $_COOKIE['usuario'];?>','analista','R')" disabled>Rechazar</button>
									<?php
								}else{
									?>	
									<button class="btn btn-primary btns_analisis" id="btn_analizado"  onclick="firmar_scoring('<?= $_COOKIE['usuario'];?>','analista')" disabled>Analizado</button>

								<?php }
							} 




		if($_COOKIE['cod_perfil']==15){
		?>	
			<button onclick="asignar_linea()">test</button>

		<?php
		}


							?>
						</div>
					</div>
				</div>
			</div>
		</div>		
		<?php 
	}else{
		?>
		<br>
		<br>
		<div class="jumbotron jumbotron-fluid">
			<div class="container-fluid">
				<h3 class="font-weight-light text-center">No se ingreso el numero de cuenta.</h3>
			</div>		
		</div>				
		<?php
	}
	?>

	<div class="modal fade" id="ModalAdjuntos" tabindex="-1" role="dialog" aria-labelledby="ModalCrearClienteTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalCrearClienteTitle">Documentos</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="pdf-resultado"style="height: 600px;"></div>
				</div>
				<div class="modal-footer">
					<div id="spiner" class="spinner-border text-success text-center d-none" role="status">
						<span class="sr-only">Loading...</span>
					</div>
					<button type="button" id="agregar_pdf" class="btn btn-primary d-none" onclick="agregar_pdf()">Subir Imagen</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="staticBackdropLabel">Actualizando</h5>
	      </div>
	      <div class="modal-body">
				<div class="d-flex align-items-center">
				  <strong>Conectandose a Informconf...</strong>
				  <div class="spinner-border ml-2 text-secondary" role="status" aria-hidden="true"></div>
				</div>
	      </div>
	      <div class="modal-footer">
	      </div>
	    </div>
	  </div>
	</div>

	<script src="../../js/PDFObject.js"></script>
	<script type="text/javascript" src="jsprueba.js?version=2.25"></script>

	<?php
	require('../../footer.php');
	?>
<script type="text/javascript">
		
			function ver_pdf(archivo){
			var file;
			file = "../../informconf/"+archivo;
			
			$('#ModalAdjuntos').modal('toggle');
			$("#spiner").addClass("d-none");
			PDFObject.embed(file, "#pdf-resultado");
		}

		function faja_actualizar(){

			var cuenta  = $('#cuenta').html();
			var documento  = $('#documento').html();
			$('#staticBackdrop').modal('show');
			
			$.ajax({
				type:'POST',
				url:"https://intranet.facilandia.com.py/api/v3/index.php/get_informconf",
				data:{
					cuenta:cuenta,
					documento:documento
				},
				success:function(resp){
					location.reload();
				}
			});
		
		}

</script>
