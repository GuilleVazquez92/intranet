<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');

$datos = new Vendedores();
$datos->cuenta = $_POST['cuenta'];
?>
<input type="text" id="cuenta" value="<?= $_POST['cuenta'];?>" hidden>
<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<a class="nav-link active" id="datos-basicos-tab" data-toggle="tab" href="#datos-basicos" role="tab" aria-controls="datos-basicos" aria-selected="true">Básicos</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link" id="particular-tab" data-toggle="tab" href="#particular" role="tab" aria-controls="particular" aria-selected="false">Particular</a>
	</li>
	<li class="nav-item" role="presentation">
		<a class="nav-link" id="laboral-tab" data-toggle="tab" href="#laboral" role="tab" aria-controls="laboral" aria-selected="false">Laboral</a>
	</li>
</ul>
<p class="text-success"><small>Para editar doble click sobre la etiqueta.</small></p>
<div class="tab-content" id="myTabContent">
	<!-- DATOS BASICOS -->
	<div class="tab-pane fade show active" id="datos-basicos" role="tabpanel" aria-labelledby="datos-basicos-tab">
		<?php 
		$datos_basicos = $datos->datos_basicos();
		?>
		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="nombre1">Primer Nombre</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="nombre1" ondblclick="desbloquear(this.id) " 
					value="<?=$datos_basicos['nombre1'];?>" readonly>
					<div id="nombre1_editar" class="input-group-append d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('nombre1',1)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-6">
				<label for="nombre2">Segundo Nombre</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="nombre2" 
					value="<?=$datos_basicos['nombre2'];?>" readonly>
					<div id="nombre2_editar" class="input-group-append d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('nombre2',1)">Guardar</button>
					</div>
				</div>				
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="apellido1">Primer Apellido</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="apellido1" ondblclick="desbloquear(this.id)"
					value="<?=$datos_basicos['apellido1'];?>" readonly>
					<div id="apellido1_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('apellido1',1)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="apellido2">Segundo Apellido</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="apellido2" ondblclick="desbloquear(this.id)"
					value="<?=$datos_basicos['apellido2'];?>" readonly>
					<div id="apellido2_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('apellido2',1)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="apellido3">Apellido Casada</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="apellido3" ondblclick="desbloquear(this.id)"
					value="<?=$datos_basicos['apellido3'];?>" readonly>
					<div id="apellido3_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('apellido3',1)">Guardar</button>
					</div>
				</div>
			</div>			
		</div>

		<div class="form-row">
			<div class="form-group col-md-6">
				<label for="estado_civil">Estado Civil</label>
				<select name="" id="estado_civil" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
					<?php 
					$selected_estado1 = $selected_estado2 = $selected_estado3 = $selected_estado4 = $selected_estado5 = "";
					switch ($datos_basicos['cod_estado_civil']) {
						case 1: $selected_estado1 = 'selected';	break;
						case 2: $selected_estado2 = 'selected';	break;
						case 3: $selected_estado3 = 'selected';	break;
						case 4: $selected_estado4 = 'selected';	break;
						case 5: $selected_estado5 = 'selected';	break;
					}
					?>
					<option value="1" <?=$selected_estado1;?>>CASADO/A</option>
					<option value="2" <?=$selected_estado2;?>>SOLTERO/A</option>
					<option value="3" <?=$selected_estado3;?>>DIVORCIADO/A</option>
					<option value="4" <?=$selected_estado4;?>>VIUDO/A</option>
					<option value="5" <?=$selected_estado5;?>>CONCUVINO/A</option>
				</select>
			</div>
			<div class="form-group col-md-6">
				<label for="reg_conyugal">Reg. Conyugal</label>
				<select name="" id="reg_conyugal" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
					<?php 
					$selected_sin_sep = ($datos_basicos['reg_conyugal'] == 0) ? 'selected':''; 
					$selected_con_sep = ($datos_basicos['reg_conyugal'] == 1) ? 'selected':'';
					?>					
					<option value="0" <?=$selected_sin_sep;?>>SIN SEP. DE BIENES</option>
					<option value="1" <?=$selected_con_sep;?>>CON SEP. DE BIENES</option>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label for="ruc">RUC</label>
			<div class="input-group">
				<input type="text" class="form-control form-control-sm" id="ruc" placeholder="RUC" ondblclick="desbloquear(this.id)" 
				value="<?=$datos_basicos['ruc'];?>" readonly>
				<div id="ruc_editar" class="input-group-append  d-none">
					<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('ruc',1)">Guardar</button>
				</div>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="fecha_nac">Fecha de Nacimiento</label>
				<input type="date" class="form-control form-control-sm" id="fecha_nac" ondblclick="desbloquear(this.id)"
				value="<?=$datos_basicos['fecha_nac'];?>" readonly onfocusout="guardar(this.id,1)">
			</div>
			<div class="form-group col-md-4">
				<label for="edad">Edad</label>
				<input type="text" class="form-control form-control-sm" id="edad"
				value="<?=$datos_basicos['edad'];?>" readonly>
			</div>
			<div class="form-group col-md-4">
				<label for="sexo">Sexo</label>
				<select name="" id="sexo" class="form-control form-control-sm"  onchange="guardar(this.id,1)" readonly>
					<?php 
					$selected_m = ($datos_basicos['sexo'] == 'M') ? 'selected':'';
					$selected_f = ($datos_basicos['sexo'] == 'F') ? 'selected':''; 
					?>		
					<option value="M" <?=$selected_m;?>>MASCULINO</option>
					<option value="F" <?=$selected_f;?>>FEMENINO</option>
				</select>
			</div>			
		</div>

		<div class="form-group">
			<label for="pais">País</label>
			<select name="" id="pais" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
				<?php
				$pais = $datos->pais($datos_basicos['cod_pais']);
				for ($i=0; $i < count($pais); $i++) {
					?> 
					<option value="<?=$pais[$i]['cod_pais'];?>" <?=$pais[$i]['selected'];?>><?=$pais[$i]['pais'];?></option>
					<?php
				}
				?>
			</select>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="actividad">Actividad</label>
				<select name="" id="actividad" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
					<?php
					$actividad = $datos->actividad($datos_basicos['cod_actividad']);			
					for ($i=0; $i < count($actividad); $i++) {
						?> 
						<option value="<?=$actividad[$i]['cod_actividad'];?>" <?=$actividad[$i]['selected'];?>><?=$actividad[$i]['actividad'];?></option>
						<?php
					}
					?>
				</select>
			</div>

			<div class="form-group col-md-4">
				<label for="sector">Sector</label>
				<select name="" id="sector" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
					<?php
					$sector = $datos->sector($datos_basicos['cod_sector']);			
					for ($i=0; $i < count($sector); $i++) {
						?> 
						<option value="<?=$sector[$i]['cod_sector'];?>" <?=$sector[$i]['selected'];?>><?=$sector[$i]['sector'];?></option>
						<?php
					}
					?>
				</select>
			</div>
			<!--
			<div class="form-group col-md-4">
				<label for="ips">Aporta IPS</label>
				<select name="" id="ips" class="form-control form-control-sm" readonly onchange="guardar(this.id,1)">
					<?php 
					$selected_no_ips = ($datos_basicos['ips'] == 0) ? 'selected':''; 
					$selected_si_ips = ($datos_basicos['ips'] == 1) ? 'selected':'';
					?>					
					<option value="0" <?=$selected_no_ips;?>>NO APORTA IPS</option>
					<option value="1" <?=$selected_si_ips;?>>SI APORTA IPS</option>
				</select>
			</div> 
			-->
		</div>
	</div>





	<!-- DATOS PARTICULARES -->
	<div class="tab-pane fade" id="particular" role="tabpanel" aria-labelledby="particular-tab">
		<?php 
		$datos_particular = $datos->datos_particular();
		?>
		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="departamento">Departamento</label>
				<select name="" id="departamento_part" class="form-control form-control-sm" readonly onchange="guardar(this.id,2)">
					<?php
					$departamento = $datos->departamento($datos_particular['cod_departamento_part']);
					var_dump($departamento);
					for ($i=0; $i < count($departamento); $i++) {
						?> 
						<option value="<?=$departamento[$i]['cod_departamento'];?>" <?=$departamento[$i]['selected'];?>><?=$departamento[$i]['departamento'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>	
		<div class="form-row">			
			<div class="form-group col-md-6">
				<label for="ciudad">Ciudad</label>
				<select name="" id="ciudad_part" class="form-control form-control-sm" readonly onchange="guardar(this.id,2)">
					<?php
					$cod_departamento = $datos_particular['cod_departamento_part'];
					$ciudad = $datos->ciudad($cod_departamento,$datos_particular['cod_ciudad_part']);
					for ($i=0; $i < count($ciudad); $i++) {
						?> 
						<option value="<?=$ciudad[$i]['cod_ciudad'];?>" <?=$ciudad[$i]['selected'];?>><?=$ciudad[$i]['ciudad'];?></option>
						<?php
					}
					?>
				</select>
			</div>

			<div class="form-group col-md-6">
				<label for="barrio">Barrio</label>
				<?php  
					$cod_departamento = $datos_particular['cod_departamento_part'];
					$cod_ciudad = $datos_particular['cod_ciudad_part'];
					$barrio = $datos->barrio($cod_departamento,$cod_ciudad,$datos_particular['cod_barrio_part']);
				?>
				<select name="" id="barrio_part" class="form-control form-control-sm" readonly onchange="guardar(this.id,2)">
					<?php
					for ($i=0; $i < count($barrio); $i++) {
						?> 
						<option value="<?=$barrio[$i]['cod_barrio'];?>" <?=$barrio[$i]['selected'];?>><?=$barrio[$i]['barrio'];?></option>
						<?php
					}
					?>
				</select>
			</div>			
		</div>


		<div class="form-row">
			<div class="form-group col-md-5">
				<label for="calle_part">Calle</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="calle_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['calle_part'];?>" readonly>
					<div id="calle_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('calle_part',2)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-5">
				<label for="esquina_part">Esquina</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="esquina_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['esquina_part'];?>" readonly>
					<div id="esquina_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('esquina_part',2)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-2">
				<label for="numero_part">Número</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="numero_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['numero_part'];?>" readonly>
					<div id="numero_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('numero_part',2)">Guardar</button>
					</div>
				</div>
			</div>			
		</div>

		<div class="form-row">
			<!--
			<div class="form-group col-md-4">
			
				<label for="vivienda_part">Vivienda</label>
				<select name="" id="ips" class="form-control form-control-sm" readonly onchange="guardar(this.id,2)">
					<?php 
					/*
					$selected_vivienda = ($datos_particular['vivienda_part'] == 0) ? 'selected':''; 
					$selected_si_ips = ($datos_particular['ips'] == 1) ? 'selected':'';
					*/
					?>					
					<option value="0" <?=$selected_no_ips;?>>Vivienda</option>
					<option value="1" <?=$selected_si_ips;?>>...</option>
				</select>
			</div>

		-->
			<div class="form-group col-md-4">
				<label for="habita_part">Habita desde</label>
				<input type="date" class="form-control form-control-sm" id="habita_part" ondblclick="desbloquear(this.id)"
				value="<?=$datos_particular['habita_part'];?>" readonly onfocusout="guardar(this.id,2)">
			</div>

			<div class="form-group col-md-4">
				<label for="grupo_part">Int.Grupo Familiar</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="grupo_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['grupo_part'];?>" readonly>
					<div id="grupo_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('grupo_part',2)">Guardar</button>
					</div>
				</div>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="telefono_part">Teléfono</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="telefono_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['telefono_part'];?>" readonly>
					<div id="telefono_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('telefono_part',2)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="celular_part">Celular</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="celular_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['celular_part'];?>" readonly>
					<div id="celular_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('celular_part',2)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="fax_part">Fax</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="fax_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['fax_part'];?>" readonly>
					<div id="fax_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('fax_part',2)">Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="email_part">Email</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="email_part" ondblclick="desbloquear(this.id)"
					value="<?=$datos_particular['email_part'];?>" readonly>
					<div id="email_part_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('email_part',2)">Guardar</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- DATOS LABORALES -->
	<div class="tab-pane fade" id="laboral" role="tabpanel" aria-labelledby="laboral-tab">
		<?php 
			$datos_laboral = $datos->datos_laboral();
		?>		
		<div class="form-row">			
			<div class="form-group col-md-6">
				<label for="empresa_laboral">Empresa</label>
				<div class="input-group">
				<input type="text" class="form-control form-control-sm" id="empresa_laboral" 
				ondblclick="desbloquear(this.id)"
				value="<?php
				if(empty($datos_laboral['empresa_laboral'])){

				$datos_laboral['empresa_laboral'] = '';	
				}else{
				$datos_laboral['empresa_laboral'];
				echo $datos_laboral['empresa_laboral'];
				}
			?>" readonly>
			<div id="empresa_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('empresa_laboral',3)">Guardar</button>
					</div>
			</div>
			</div>
			<div class="form-group col-md-6">
				<label for="cargo_laboral">Cargo</label>
				<select name="" id="cargo_laboral" class="form-control form-control-sm" readonly onchange="guardar(this.id,3)">
					<?php
					if (empty($datos_laboral['cod_cargo_laboral'])) {
						$datos_laboral['cod_cargo_laboral']=1;
						$cargo = $datos->cargo($datos_laboral['cod_cargo_laboral']);
						
					}else{
					$cargo = $datos->cargo($datos_laboral['cod_cargo_laboral']);
					}
					for ($i=0; $i < count($cargo); $i++) {
						?> 
						<option value="<?=$cargo[$i]['cod_cargo'];?>" <?=$cargo[$i]['selected'];?>><?=$cargo[$i]['cargo'];?></option>
						<?php
					}
					?>
				</select>
			</div>			
		</div>

		<div class="form-row">
				<div class="form-group col-md-4">
				<label for="ingreso_laboral">Ingreso Mensual</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="ingreso_laboral" ondblclick="desbloquear(this.id)"
					value="<?php 
					if(empty($datos_laboral['ingreso_laboral'])){
					$datos_laboral['ingreso_laboral'] = 0;
					echo round($datos_laboral['ingreso_laboral']);	
					}else{
					echo round($datos_laboral['ingreso_laboral']);
					}
				?>" readonly>
					<div id="ingreso_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('ingreso_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>

			<div class="form-group col-md-4">
				<label for="fecha_laboral">Fecha ingreso</label>
				<input type="date" class="form-control form-control-sm" id="fecha_laboral" ondblclick="desbloquear(this.id)"
				value="<?=$datos_laboral['fecha_laboral'];?>" readonly onclick="guardar(this.id,3)">
			</div>

			<div class="form-group col-md-2">
				<label for="horario_laboral1">Horario desde</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="horario_laboral1" ondblclick="desbloquear(this.id)"
					value="<?php
					if(empty($datos_laboral['horario_laboral1'])){
						$datos_laboral['horario_laboral1'] = '';

						}else{
					$datos_laboral['horario_laboral1'];
				}				
				?>" readonly>
					<div id="horario_laboral1_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('horario_laboral1',3)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-2">
				<label for="horario_laboral2">Horario hasta</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="horario_laboral2" ondblclick="desbloquear(this.id)"
					value="<?php
					if(empty($datos_laboral['horario_laboral2'])){
						$datos_laboral['horario_laboral2'] = '';
					}else{
					$datos_laboral['horario_laboral2'];
					}
				?>" readonly>
					<div id="horario_laboral2_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('horario_laboral2',3)">Guardar</button>
					</div>
				</div>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-12">
				<label for="departamento_laboral">Departamento</label>
				<select name="" id="departamento_laboral" class="form-control form-control-sm" readonly onchange="guardar(this.id,3)">
					<?php
					if (empty($datos_laboral['cod_departamento_laboral'])) {
						$datos_laboral['cod_departamento_laboral'] = 0;
						$departamento = $datos->departamento($datos_laboral['cod_departamento_laboral']);
					}else{
					$departamento = $datos->departamento($datos_laboral['cod_departamento_laboral']);
					}
					for ($i=0; $i < count($departamento); $i++) {
						?> 
						<option value="<?=$departamento[$i]['cod_departamento'];?>" <?=$departamento[$i]['selected'];?>><?=$departamento[$i]['departamento'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>	
		<div class="form-row">			
			<div class="form-group col-md-6">
				<label for="ciudad">Ciudad</label>
				<select name="" id="ciudad_laboral" class="form-control form-control-sm" readonly onchange="guardar(this.id,3)">
					<?php
					if (empty($datos_laboral['cod_ciudad_laboral'])) {
						$cod_departamento = 0;
						$datos_laboral['cod_ciudad_laboral'] = 1;
						$ciudad = $datos->ciudad($cod_departamento,$datos_laboral['cod_ciudad_laboral']);
					}else{
					$cod_departamento = $datos_laboral['cod_departamento_laboral'];
					$ciudad = $datos->ciudad($cod_departamento,$datos_laboral['cod_ciudad_laboral']);
					}
					for ($i=0; $i < count($ciudad); $i++) {
						?> 
						<option value="<?=$ciudad[$i]['cod_ciudad'];?>" <?=$ciudad[$i]['selected'];?>><?=$ciudad[$i]['ciudad'];?></option>
						<?php
					}
					?>
				</select>
			</div>

			<div class="form-group col-md-6">
				<label for="barrio_laboral">Barrio</label>
				<select name="" id="barrio_laboral" class="form-control form-control-sm" readonly onchange="guardar(this.id,3)">
					<?php
					$cod_departamento = $datos_laboral['cod_departamento_laboral'];
					$cod_ciudad = $datos_laboral['cod_ciudad_laboral'];
					$barrio = $datos->barrio($cod_departamento,$cod_ciudad,$datos_laboral['cod_barrio_laboral']);
					for ($i=0; $i < count($barrio); $i++) {
						?> 
						<option value="<?=$barrio[$i]['cod_barrio'];?>" <?=$barrio[$i]['selected'];?>><?=$barrio[$i]['barrio'];?></option>
						<?php
					}
					?>
				</select>
			</div>
						
		</div>

		<div class="form-row">
			<div class="form-group col-md-5">
				<label for="calle_laboral">Calle</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="calle_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['calle_laboral'];?>" readonly>
					<div id="calle_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('calle_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-5">
				<label for="esquina_laboral">Esquina</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="esquina_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['esquina_laboral'];?>" readonly>
					<div id="esquina_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('esquina_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-2">
				<label for="numero_laboral">Número</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="numero_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['numero_laboral'];?>" readonly>
					<div id="numero_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('numero_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>			
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="telefono_laboral">Teléfono</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="telefono_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['telefono_laboral'];?>" readonly>
					<div id="telefono_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('telefono_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="fax_laboral">Fax</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="fax_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['fax_laboral'];?>" readonly>
					<div id="fax_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('fax_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>
			<div class="form-group col-md-4">
				<label for="email_laboral">Email</label>
				<div class="input-group">
					<input type="text" class="form-control form-control-sm" id="email_laboral" ondblclick="desbloquear(this.id)"
					value="<?=$datos_laboral['email_laboral'];?>" readonly>
					<div id="email_laboral_editar" class="input-group-append  d-none">
						<button class="btn btn-sm btn-outline-success" type="button" onclick="guardar('email_laboral',3)">Guardar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function desbloquear(elemento) {

		//console.log(elemento);
		document.getElementById(elemento).readOnly = false;
		document.getElementById(elemento+'_editar').classList.remove("d-none");

	

/*		
		document.getElementById('nombre1').readOnly = false;
		document.getElementById('nombre2').readOnly = false;		
		document.getElementById('apellido1').readOnly = false;		
		document.getElementById('apellido2').readOnly = false;		
		document.getElementById('apellido3').readOnly = false;		
		document.getElementById('ruc').readOnly = false;
		document.getElementById('fecha_nac').readOnly = false;

		document.getElementById('calle_part').readOnly = false;
		document.getElementById('numero_part').readOnly = false;
		document.getElementById('esquina_part').readOnly = false;			
		document.getElementById('grupo_part').readOnly = false;
		document.getElementById('telefono_part').readOnly = false;
		document.getElementById('celular_part').readOnly = false;
		document.getElementById('fax_part').readOnly = false;			
		document.getElementById('email_part').readOnly = false;

		document.getElementById('ingreso_laboral').readOnly = false;
		document.getElementById('horario_laboral1').readOnly = false;
		document.getElementById('horario_laboral2').readOnly = false;
		document.getElementById('calle_laboral').readOnly = false;
		document.getElementById('numero_laboral').readOnly = false;
		document.getElementById('esquina_laboral').readOnly = false;
		document.getElementById('telefono_laboral').readOnly = false;
		document.getElementById('fax_laboral').readOnly = false;
		document.getElementById('email_laboral').readOnly = false;

		document.getElementById('nombre1_editar').removeAttribute("d-none");		
		document.getElementById('nombre2_editar').classList.add("d-none");
		document.getElementById('apellido1_editar').classList.add("d-none");		
		document.getElementById('apellido2_editar').classList.add("d-none");	
		document.getElementById('apellido3_editar').classList.add("d-none");	
		document.getElementById('ruc_editar').classList.add("d-none");

		document.getElementById('calle_part_editar').classList.add("d-none");
		document.getElementById('numero_part_editar').classList.add("d-none");
		document.getElementById('esquina_part_editar').classList.add("d-none");			
		document.getElementById('grupo_part_editar').classList.add("d-none");
		document.getElementById('telefono_part_editar').classList.add("d-none");
		document.getElementById('celular_part_editar').classList.add("d-none");
		document.getElementById('fax_part_editar').classList.add("d-none");			
		document.getElementById('email_part_editar').classList.add("d-none");

		document.getElementById('ingreso_laboral_editar').classList.add("d-none");
		document.getElementById('horario_laboral1_editar').classList.add("d-none");
		document.getElementById('horario_laboral2_editar').classList.add("d-none");
		document.getElementById('calle_laboral_editar').classList.add("d-none");
		document.getElementById('numero_laboral_editar').classList.add("d-none");
		document.getElementById('esquina_laboral_editar').classList.add("d-none");
		document.getElementById('telefono_laboral_editar').classList.add("d-none");
		document.getElementById('fax_laboral_editar').classList.add("d-none");
		document.getElementById('email_laboral_editar').classList.add("d-none");

		document.getElementById(elemento).readOnly = false;
		document.getElementById(elemento+'_editar').removeAttribute("d-none");*/
	}

	function guardar(elemento, zona){

		var campo;
		var cuenta = document.getElementById('cuenta').value;
		var valor = document.getElementById(elemento).value;

		

		switch(elemento) {
			case 'nombre1'  : campo = 'aanom1'; break;
			case 'nombre2'  : campo = 'aanom2';break;
			case 'apellido1': campo = 'aaape1'; break;
			case 'apellido2': campo = 'aaape2'; break;
			case 'apellido3': campo = 'aaape3'; break;
			case 'pais'		: campo = 'ahpais'; break;
			case 'sexo'		: campo = 'aasexo'; break;
			case 'fecha_nac': campo = 'aafech'; break;
			case 'sector'	: campo = 'ansect'; break;
			case 'actividad': campo = 'alacti'; break;
			case 'ips'		: campo = 'aavivi'; break;
			case 'ruc'		: campo = 'aaruc'; break;
			case 'estado_civil': campo = 'aaesta'; break;
			case 'reg_conyugal': campo = 'aaregi'; break;

			case 'departamento_part': campo = 'aidept'; break;
			case 'ciudad_part': campo = 'apciud'; break;
			case 'barrio_part': campo = 'ajbarr'; break;
			case 'calle_part': campo = 'awcalle'; break;
			case 'numero_part': campo = 'awnume'; break;
			case 'esquina_part': campo = 'awesq'; break;
			case 'vivienda_part': campo = 'awprop'; break;
			case 'grupo_part': campo = 'awinte'; break;
			case 'habita_part': campo = 'awdesd'; break;
			case 'telefono_part': campo = 'awtel1'; break;
			case 'celular_part': campo = 'awcelu'; break;
			case 'fax_part': campo = 'awfax'; break;
			case 'email_part': campo = 'awemai'; break;

			case 'empresa_laboral': campo = 'aaempr'; break;
			case 'departamento_laboral': campo = 'aidept'; break;
			case 'ciudad_laboral': campo = 'apciud'; break;
			case 'barrio_laboral': campo = 'ajbarr'; break;
			case 'cargo_laboral': campo = 'amcarg'; break;
			case 'fecha_laboral': campo = 'aaifec'; break;			
			case 'ingreso_laboral': campo = 'basala'; break;
			case 'horario_laboral1': campo = 'bahord'; break;
			case 'horario_laboral2': campo = 'bahora'; break;			
			case 'calle_laboral': campo = 'bacalle'; break;
			case 'numero_laboral': campo = 'banume'; break;
			case 'esquina_laboral': campo = 'baesq'; break;
			case 'telefono_laboral': campo = 'batel1'; break;
			case 'fax_laboral': campo = 'bafax'; break;
			case 'email_laboral': campo = 'baemai'; break;
		}

		console.log(zona, cuenta, campo, valor);
		
/*		$.ajax({
			type:'POST',
			url:"modificacion_datos.php",
			data:{
				zona : zona,
				cuenta : cuenta, 
				campo : campo,
				valor : valor
			},
			success:function(resp){
			document.getElementById(elemento).readOnly = true;
				if(	elemento=='nombre1' || elemento=='nombre2' || elemento=='apellido1' || elemento=='apellido2' || elemento=='apellido3' || elemento=='ruc' || elemento=='calle_part'|| elemento=='esquina_part'|| elemento=='numero_part'|| elemento=='grupo_part'|| elemento=='telefono_part'||	elemento=='celular_part'|| elemento=='fax_part'|| elemento=='email_part' || elemento=='ingreso_laboral'||	elemento=='horario_laboral1'|| elemento=='horario_laboral2'|| elemento=='calle_laboral'||
					elemento=='numero_laboral'|| elemento=='esquina_laboral'|| elemento=='telefono_laboral'|| elemento=='fax_laboral'||	elemento=='email_laboral'){
					document.getElementById(elemento+'_editar').classList.add("d-none");	

			}

			
		}
	});*/

	}




</script>