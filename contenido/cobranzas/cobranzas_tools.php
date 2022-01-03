<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
	
	class Conexion{

		public function conn(){	
			try {
				if(!defined('host')){
					define('host',' host=192.168.4.200;');
					define('bd',' dbname=base_fc');
					define('usuario','postgres');
					define('password','postgres');
				}

				return new PDO('pgsql:'.host.bd,usuario,password);

			} catch (Exception $e) {

				return $e->getMessage().'<br>';
				die();
				
			}
		}
	}


	class Cobranzas extends Conexion
	{
		
		# Propiedades
		public $cuenta;
		public $operacion;
		public $gestor;
		public $asociacion;
		public $mensaje;

		# Metodos

		public function cliente_fallecido(){
			$db = $this->conn();
			$row = array();

			$sql="UPDATE fsd0122 SET bfesta=19 WHERE aacuen = $this->cuenta AND bfesta=7 RETURNING 1;";
			$result = $db->prepare($sql); 
			$result->execute(); 
			$this->mensaje = ($result->fetchColumn()>0) ? 1 : 0;
			print $this->mensaje_alerta();
		}

		public function operacion_da_normal(){
			$db = $this->conn();
			$sql="UPDATE fsd0122 SET bfasoc=0, bfagen=240, 
			bfoper=
				case 
					when bfoper=402 then 401
					when bfoper=410 then 401
					when bfoper=415 then 401
					when bfoper=420 then 401

					when bfoper=302 then 301
					when bfoper=310 then 301
					when bfoper=315 then 301
					when bfoper=320 then 301

					when bfoper=202 then 201
					when bfoper=210 then 201
					when bfoper=215 then 201
					when bfoper=220 then 201
					else bfoper
				end
			WHERE bfempr=1 AND bfesta=7 AND bfasoc!=0 AND aacuen=$this->cuenta";
			$db->query($sql); 
			
			$sql="UPDATE fsd0011 SET aaso=0	WHERE aaso!=0  AND  aacuen=$this->cuenta";
			$db->query($sql);
			$this->asignar_gestor();
		}
		public function operacion_normal_da(){
			$db = $this->conn();
				
			$sql="UPDATE fsd0011 SET aaso=$this->asociacion	WHERE aaso!=$this->asociacion  AND  aacuen=$this->cuenta";
			$db->query($sql);	

			$sql="UPDATE fsd0122 SET bfasoc=$this->asociacion, bfagen=250 WHERE bfempr=1 AND bfope1=$this->operacion RETURNING 1;";
			$result = $db->prepare($sql); 
			$result->execute();
			$this->mensaje = ($result->fetchColumn()>0) ? 1 : 0;
			print $this->mensaje_alerta();
		}


		public function asignar_gestor(){
			$db = $this->conn();
			$sql="UPDATE tfinh44 SET thgest='$this->gestor',thprod=1,thtipo=1, thtram=(select thtram from tfinh44 where thgest='$this->gestor' limit 1)
			 WHERE thempr=1
			AND thcuen=$this->cuenta RETURNING 1;";
			$result = $db->prepare($sql); 
			$result->execute();
			$this->mensaje = ($result->fetchColumn()>0) ? 1 : 0;
			print $this->mensaje_alerta();		}

		public function gestores(){
			$db = $this->conn();
			$i  = 0;
			
			$sql="SELECT  'Tramo '||
						case 
							when thtram =21 then '1-1'
							when thtram =22 then '1-2'
							else thtram::text
						end 
					tramo, thgest gestor FROM tfinh44 WHERE thempr=1 GROUP BY 1, 2 ORDER BY 1";

			foreach ( $db -> query($sql) as $row ) {
			
				$resultado[$i]['tramo'] =  $row['tramo'];
				$resultado[$i]['gestor']=  $row['gestor'];
				$i++;	
			
			}
			return $resultado;
		}
		public function asociaciones(){
			$db = $this->conn();
			$i  = 0;
			
			$sql="SELECT biasoc id, binomb descripcion FROM public.fsd033 ORDER BY 1;";

			foreach ( $db -> query($sql) as $row ) {
			
				$resultado[$i]['id'] 	=  $row['id'];
				$resultado[$i]['descripcion']  =  $row['descripcion'];
				$i++;	
			
			}
			return $resultado;
		}

		public function mensaje_alerta(){


				if($this->mensaje==1){
					$msg = '<div class="alert alert-success" role="alert">Se proceso correctamente la solicitud!!!</div>';	

				}else{
					$msg = '<div class="alert alert-danger" role="alert">Error en procesamiento, intente nuevamente</div>';
				}	

				return $msg;
		}


	}

	$cobranza = new Cobranzas;

	if(isset($_POST['fallecidos'])){
		$cobranza->cuenta = $_POST['cuenta'];
		$cobranza->cliente_fallecido();
		
	}

	if(isset($_POST['normal-da'])){
		$cobranza->operacion = $_POST['operacion'];
		$cobranza->asociacion = $_POST['asociacion'];
		$cobranza->operacion_normal_da();
	}

	if(isset($_POST['da-normal'])){
		$cobranza->cuenta = $_POST['cuenta'];
		$cobranza->gestor = $_POST['gestor'];
		$cobranza->operacion_da_normal();
	}

	if(isset($_POST['asignar'])){
		$cobranza->cuenta = $_POST['cuenta'];
		$cobranza->gestor = $_POST['gestor'];
		$cobranza->asignar_gestor();
	}	

?>
	<div class="container">
	
	<br>
		<h4 class="text-secondary">HERRAMIENTAS DE COBRANZAS</h4>
	<br>
		<div>
			<h6 class="text-secondary">Clientes Fallecidos</h6>
			<form action="" method="POST">
				<div class="form-group">
					<label for="cuenta" class="sr-only">Cuenta</label>
					<input type="number" placeholder="Cuenta" name="cuenta" class="form-control form-control-sm" required="">
				</div>
				<button type="submit" name="fallecidos" class="btn btn-primary btn-sm">Procesar</button>
			</form>
		</div>
		<br>
		<br>

		<div>
			<h6 class="text-secondary">Asignar clientes DA a NORMAL</h6>
				<form action="" method="POST" class="form">
					<div class="form-group">
						<input type="number" placeholder="Cuenta" name="cuenta" class="form-control form-control-sm" required="">
					</div>

					<div class="form-group">
						<select name="gestor" id="gestor" class="form-control form-control-sm" required="">
								<option value=""></option>
							<?php
								$tramo = ""; 
								$datos = $cobranza->gestores();
								for ($i=0; $i < count($datos) ; $i++) { 
								
								if($tramo!=$datos[$i]['tramo']){
							?>			
								<optgroup label="<?=$datos[$i]['tramo'];?>">
							<?php 
									$tramo = $datos[$i]['tramo'];
								}	
							?>		
								<option><?= $datos[$i]['gestor'];?></option>
							<?php
								}
							?>
						</select>
					</div>
					<button type="submit" name="da-normal" class="btn btn-primary btn-sm">Procesar</button>
				</form>
		</div>
		<br>
		<br>
		<div>
			<h6 class="text-secondary">Asignar Operación de NORMAL a DA</h6>
				<form action="" method="POST" class="form">
					<div class="form-group">
						<input type="number" placeholder="Operación" name="operacion" class="form-control form-control-sm" required="">
					</div>
					<div class="form-group">
						<select name="asociacion" id="asociacion" class="form-control form-control-sm" required="">
							<option value=""></option>
							<?php 
								$datos = $cobranza->asociaciones();
								for ($i=0; $i < count($datos) ; $i++) { 
							?>		
								<option value="<?= $datos[$i]['id'];?>"><?= $datos[$i]['descripcion'];?></option>
							<?php
								}
							?>
						</select>
					</div>					
					<button type="submit" name="normal-da" class="btn btn-primary btn-sm">Procesar</button>
				</form>
		</div>
		<br>
		<br>
		<div>
			<h6 class="text-secondary">Asignar Clientes a Gestor</h6>
				<form action="" method="POST" class="form">
					<div class="form-group">
						<input type="number" placeholder="Cuenta" name="cuenta" class="form-control form-control-sm" required="">
					</div>
					<div class="form-group">
						<select name="gestor" id="gestor" class="form-control form-control-sm" required="">
								<option value=""></option>
							<?php
								$tramo = ""; 
								$datos = $cobranza->gestores();
								for ($i=0; $i < count($datos) ; $i++) { 
								
								if($tramo!=$datos[$i]['tramo']){
							?>			
								<optgroup label="<?=$datos[$i]['tramo'];?>">
							<?php 
									$tramo = $datos[$i]['tramo'];
								}	
							?>		
								<option><?= $datos[$i]['gestor'];?></option>
							<?php
								}
							?>
						</select>
					</div>

					<button type="submit" name="asignar" class="btn btn-primary btn-sm">Procesar</button>
				</form>
		</div>
		<br>
		<br>
	

	</div>


	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
</body>
</html>