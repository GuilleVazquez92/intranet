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

class UserLogin extends Conexion{

		// Propiedades	
	public $usuario;
	public $password;
	public $nombre;
	public $departamento;
	public $empresa;

		// Metodos

	public function login(){

		$db = $this->conn();
			/*
				Juntar las tablas de usuarios y fsd050 colocando un id a cada una, realizar una busqueda con un limit 1 ordenado por id
				el mismo validara si existe o no el usuario, el cual el id determinara que tipo de login utilizara
				1 por AD 2 por BD de base.. 
				de no existir el cliente colocar la leyenda que no existe el mismo.
			*/

				$sql = "SELECT id,usuario,alias,base,nombre,empresa,cod_canal,trim(bccnom) canal,cod_perfil,perfil, rol 
				FROM 
				(	
				/* Usuarios AD */
				SELECT 1 id, a.id usuario,a.id alias,usr_base base, upper(trim(a.descripcion)) nombre,empresa,canal cod_canal, perfil cod_perfil,upper(trim(b.descripcion)) perfil, rol
				FROM intranet.usuarios a, intranet.usuarios_perfil b
				WHERE a.perfil=b.id 
				AND activo='S'

				UNION
				/* Usuarios varios */
				SELECT 2, clusu usuario, clnom1 alias,clusu base, trim(clnom1||' '||clnom2||' '||clape1||' '||clape2) nombre,ckemp empresa,9999,a.cjcar,upper(trim(cjabr)),0 
				FROM fsd050 a, fst053 b	
				WHERE clhab='S'
				AND a.cjcar=b.cjcar
				AND clusu NOT IN (SELECT bzcort FROM fst062 WHERE bzvact='S')
				AND (a.cjcar=611 or a.cjcar=614 or a.cjcar=616 or a.cjcar=831 or a.cjcar=832 or a.cjcar=9991)

				UNION
				/* Usuarios ALIADOS */	
				SELECT 3, usuario, usuario alias, usuario base, usuario::text nombre, 1::numeric empresa, 9999 cod_canal, 9992 cod_perfil,'ALIANZAS'::text cod_perfil,0 rol
				FROM pagares.entidad WHERE activo='S'

				UNION 
				/* Vendedores */
				SELECT 2, clusu usuario, clnom1 alias,clusu base,trim(clnom1||' '||clnom2||' '||clape1||' '||clape2) nombre,ckemp empresa,equicana,28,'VENDEDOR',bzclav 
				FROM fsd050 a, fst062 b, fst076 c	
				WHERE clusu=bzcort
				AND clhab=bzvact
				AND b.equicodi=c.equicodi
				AND clhab='S'
				AND bzfchba IS NULL 
				AND BZNIVE>0 
				AND BZCLAV>=98 
				AND BZCLAV!=119
				UNION
				/* Usuarios de LENNUX */
				SELECT 2, clusu usuario, clusu alias,clusu base, trim(clnom1||' '||clnom2||' '||clape1||' '||clape2) nombre,ckemp empresa,9999,a.cjcar,upper(trim(cjabr)),0 
				FROM fsd050 a, fst053 b	
				WHERE clhab='S'
				AND a.cjcar=b.cjcar
				AND ckemp=2
				AND a.cjcar!=214


				) AS datos, fst025
				WHERE cod_canal=bccana
				AND upper(trim(usuario)) = upper(trim('$this->usuario'))
				ORDER BY 1,9,2
				LIMIT 1;";	

				$data = $db ->prepare($sql); 
				$data->execute();

				if($data->rowCount()==1){
					$control = 0;
					$datos = $data->fetchAll();

					if($datos[0]['id']==1){

					## Logueo desde al Active Directory
						$sEmpresa 	= "svr-dns1";
						$sDominio 	= "facilandia.com.py";
						$dn 		= 'dc=$sEmpresa,dc=$sDominio'; 
						$ldapconn 	= ldap_connect("$sEmpresa.$sDominio",389) or die("ERROR: No se pudo conectar con el Servidor LDAP."); 

						ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3); 
						ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0); 

						$ldapbind = @ldap_bind($ldapconn, $this->usuario."@$sDominio", $this->password); 
						ldap_close($ldapconn);

						$control = (isset($ldapbind) && $ldapbind==1) ? 1 : 0; 						

					} elseif ($datos[0]['id']==2) {
						
					## Logueo desde la base de datos base ##
						require( MODELO . 'verificar.php');
						$sClave	= encriptar(strtoupper(trim($this->usuario)),$this->password);

						$sql = "SELECT clusu FROM fsd050 WHERE clhab='S' AND clusu='".strtoupper(trim($this->usuario))."' AND clcon='".$sClave."'";
						$usuario = $db->prepare($sql); 
						$usuario->execute();
						$control = ($usuario->rowCount()==1) ? 1 : 0;
					}else{
						
						$sql = "SELECT usuario FROM pagares.entidad WHERE activo='S' AND usuario='".strtoupper(trim($this->usuario))."' AND password='".strtoupper(trim($this->password))."'";
						$usuario = $db->prepare($sql); 
						$usuario->execute();
						$control = ($usuario->rowCount()==1) ? 1 : 0;
					}

					if($control==1){

						setcookie("usuario",	$this->usuario,		 	time()+43200);
						setcookie("empresa",	$datos[0]['empresa'],	time()+43200);
						setcookie("nombre",		$datos[0]['nombre'],	time()+43200);
						setcookie("alias",		$datos[0]['alias'],	 	time()+43200);
						setcookie("cod_canal",	$datos[0]['cod_canal'],	time()+43200);
						setcookie("canal",		$datos[0]['canal'],	 	time()+43200);
						setcookie("cod_perfil",	$datos[0]['cod_perfil'],time()+43200);
						setcookie("perfil",		$datos[0]['perfil'],	time()+43200);
						setcookie("rol",		$datos[0]['rol'],	 	time()+43200);
						setcookie("expira",		time()+43200,    		time()+43200);

						if($datos[0]['cod_perfil']==9991 || $datos[0]['cod_perfil']==9992){

					print		$sql = "SELECT id, nombre, fecha_inicio, deposito 
										FROM (
										SELECT id, descripcion nombre, fecha_inicio, deposito FROM convenios.convenios
										union 
										SELECT entidad, usuario,fecha_inicio, deposito FROM pagares.entidad WHERE activo='S') AS 
										datos where deposito>0 and nombre=upper('$this->usuario');";
							$db = $this->conn();
							foreach ( $db -> query($sql) as $row ) {

								setcookie("id",$row['id'],time()+43200);
								setcookie("nombre",$row['nombre'],time()+43200);	
								setcookie("deposito",$row['deposito'],time()+43200);
								setcookie("fecha_inicio",$row['fecha_inicio'],time()+43200);
							}						
						}
						header('Location: ' . ROOT);

					}else{

						$link = array('error' => 'Contraseña incorrecta, verifique la contraseña...');
						return $link;					
					}

				}else{

					$link = array('error' => 'Usuario no existe o no está habilitado, verifique el usuario...');
					return $link;		
				}
			} 

			public function logout(){

				if(isset($_SERVER['HTTP_COOKIE'])){

					$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
					foreach($cookies as $cookie) {
						$parts = explode('=', $cookie);
						$name = trim($parts[0]);
						unset($_COOKIE['$name']);
						setcookie($name, '', time()-43200);
						setcookie($name, '', time()-43200, '/');
					}
				}    
			}

			public function user_menu(){

				$i 	 = 0;
				$result = array();
				$sql = "SELECT a.id,a.descripcion,a.sub_menu,'contenido/'||lower(a.descripcion)||'/'||a.link enlace
				FROM 	intranet.usuarios_menu a,
				intranet.usuario_perfil_detalle b,
				intranet.usuarios_perfil c
				WHERE a.id=b.menu
				AND b.id=c.id
				AND c.id=$_COOKIE[cod_perfil]
				AND a.estado=1 
				ORDER BY 1;";

				$db = $this->conn();
				foreach ( $db -> query($sql) as $row ) {

					$result[$i]['id'] 		=  $row['id'];
					$result[$i]['menu']  	=  str_replace("_"," ", $row['descripcion']) ;
					$result[$i]['sub_menu'] =  $row['sub_menu'];
					$result[$i]['enlace'] 	=  $row['enlace'];
					$_SESSION['menu'][$i] 	=  $row['enlace'];
					$i++;	
				}
				return $result;
			}
		} 
		?>