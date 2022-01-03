<?php

class Compras extends Conexion
{

	public $cod_vendedor;
	public $cod_proveedor;	
	public $codigo;
	public $target;
	public $deposito;
	public $stock;
	public $nombre;
	public $descripcion;
	public $detalle;
	public $familia;
	public $clase;
	public $costo;
	public $lista;

	public function orden_consultar(){
		
		$i 	= 0;
		$result = array();
		$db = $this->conn();			

		$sql = "SELECT operacion,cuenta,trim(aanom) cliente,forma,tipo,neto,bcaux1 posicion 
		FROM web_operaciones_estado, fsd0011, fsd014 
		WHERE cuenta=fsd0011.aacuen 
		AND fsd014.bcope1=operacion 
		AND (estado=50 or estado=5) 
		and (select bcaux1 from fsd014pl where bcope1=operacion order by (bbfecha||' '||bbhora)::timestamp desc limit 1)=10
		order by case when bcaux1 = 0 then 99 else bcaux1 end  asc;";

		foreach ($db -> query($sql) as $row ) {

			$result[$i]['operacion'] 	= $row['operacion'];
			$result[$i]['cuenta'] 		= $row['cuenta'];
			$result[$i]['cliente'] 		= $row['cliente'];
			$result[$i]['neto'] 		= $row['neto'];
			$result[$i]['posicion'] 	= $row['posicion'];	
			$i++;
		}
		return $result;
	}


	public function consultar_proveedor(){

		$sql = "SELECT epcodi codigo, trim(cod_producto) cod_producto, trim(epdesc) nombre,trim(epdescl) descripcion_larga, trim(epobs) detalle, familia, clase, epcosto precio_costo, epprelis precio_lista,epacti estado 
		from productos.importar,tef005
		left join (select effami cod_familia, trim(efdesc) familia from tef001) as familia on effami=familia.cod_familia
		left join (select effami cod_familia, efclas cod_clase,  trim(efclasdes) clase from tef002) as clase on effami=clase.cod_familia and efclas=cod_clase
		where trim(epcodpro)=trim(trim(cod_proveedor::text)||'_'||trim(cod_producto))
		and epacti='S'
		and cod_proveedor=$this->cod_proveedor
		ORDER BY 1";

		$result = array();
		$db = $this->conn();
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function procesar_importacion(){

		$result = array();
		$db 	= $this->conn();
		$sql = "INSERT INTO productos.importar(cod_proveedor, cod_producto, ult_actualizacion) 
		VALUES ($this->cod_proveedor,regexp_replace('$this->codigo', '[^a-zA-Z0-9]','','g'),now()) ON CONFLICT(cod_proveedor,cod_producto) DO UPDATE SET ult_actualizacion=now();";
		$db->query($sql);


		$sql 	= "SELECT epcodi codigo FROM tef005 WHERE trim(epcodpro)=trim(trim('$this->cod_proveedor')||'_'||regexp_replace('$this->codigo', '[^a-zA-Z0-9]','','g'));";
		$result = $db->query($sql);
		
		if($db->query($sql)->rowCount()>0){

			$codigo = $result->fetchAll()[0]['codigo'];
			$sql 	= "UPDATE tef005 
			SET epdesc=regexp_replace('$this->nombre', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g'), epdescl=regexp_replace('$this->descripcion', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g'),epobs=regexp_replace('$this->detalle', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g') ,epcosto=$this->costo, epprelis=$this->lista, effami=familia, efclas=clase, eftipo=tipo,	eflinea=1, epacti='S'
			FROM (select eftifami tipo,tef001.effami familia,efclas clase from tef001, tef002 where tef001.effami=tef002.effami and tef001.effami=$this->familia and efclas=$this->clase) as tipo
			WHERE epcodi=$codigo;";	
			$db->query($sql);		

		}else{
			
			$rango = ($this->familia==52) ? 2020000:3000000;
			$sql = "SELECT epcodi+1 codigo FROM tef005 mo WHERE NOT EXISTS (SELECT NULL FROM tef005 mi WHERE mi.epcodi = mo.epcodi+1) and epcodi>$rango ORDER BY epcodi LIMIT 1";				
			$codigo = $db->query($sql)->fetchAll()[0]['codigo'];
			
			$sql = "INSERT INTO public.tef005(
			epcodi, epdesc, epdescl, effami, efclas, efmarca, eflinea, efunid, 
			efcodcon, efcodlis, epprelis, epdeta, epfoto, epstock, epstmin, 
			epstres, epstexp, epstsol, epcosto, epprec1, epprec2, eftipo, 
			efaltu, eflarg, efanch, efpeso, efmed, efpes, efcol, epcodpro, 
			epmone, epmerser, epcomb, epiva, produlcom, epacti, gtiacod, 
			epdatdep, epcat, epdatzon, epferi, epprefer, epgarantia, epcostreal, 
			eppromcost, epzona, epobs, epstockalt)
			SELECT $codigo,regexp_replace('$this->nombre', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g'),regexp_replace('$this->descripcion', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g'),familia,clase,0,1,0,0,0,$this->lista,'','',0,0,0,0,0,$this->costo,$this->lista,0,tipo,0,0,0,0,'','','',trim('$this->cod_proveedor')||'_'||regexp_replace('$this->codigo', '[^a-zA-Z0-9]', '', 'g'),6900,'M','N',2,current_date,'S',0,0,'','','N',0,'',0,0,'',regexp_replace('$this->detalle', '[^a-zA-Z0-9|°&*()+-/ ]', '', 'g'),0
			FROM (select eftifami tipo,tef001.effami familia,efclas clase from tef001, tef002 where tef001.effami=tef002.effami and tef001.effami=$this->familia and efclas=$this->clase) as tipo;";
			$db->query($sql);

		}

	}

	public function finalizar_importacion(){

		$db 	= $this->conn();
		$sql 	= "UPDATE tef005 SET epacti='N' FROM productos.importar WHERE epcodpro=cod_proveedor||'_'||cod_producto AND epacti='S' AND cod_proveedor=$this->cod_proveedor;";
		$db->query($sql);

		$sql 	= "UPDATE tef005 SET epacti='S' FROM productos.importar WHERE epcodpro=cod_proveedor||'_'||cod_producto AND epacti='N' AND ult_actualizacion::date=current_date AND cod_proveedor=$this->cod_proveedor;";
		$db->query($sql);

	}

	public function productos_406(){

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT epcodi codigo,epdescl descripcion,epstock stock, epprelis precio FROM tef005 WHERE epcodi BETWEEN 22000000 AND 24000000  AND epacti='S' ORDER BY 1;";
		$result = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
		return $result;

	}

	public function agregar_promo406(){

		$db 	= $this->conn();
		$sql 	= "INSERT INTO public.tef005( epcodi, epdesc, epdescl, effami, efclas, efmarca, eflinea, efunid, efcodcon, efcodlis, epprelis, epdeta, epfoto, epstock, epstmin, epstres, epstexp, epstsol, epcosto, epprec1, epprec2, eftipo, efaltu, eflarg, efanch, efpeso, efmed, efpes, efcol, epcodpro, epmone, epmerser, epcomb, epiva, produlcom, epacti, gtiacod, epdatdep, epcat, epdatzon, epferi, epprefer, epgarantia, epcostreal, 
		eppromcost, epzona, epobs, epstockalt)

		SELECT $this->codigo+20000000, epdesc, epdescl, effami, efclas, efmarca, eflinea, efunid, efcodcon, efcodlis, epprelis, epdeta, epfoto, 0, epstmin, epstres, epstexp, epstsol, epcosto, epprec1, epprec2, eftipo, efaltu, eflarg, efanch, efpeso, efmed, efpes, efcol, epcodpro, epmone, epmerser, epcomb, epiva, produlcom, epacti, gtiacod, epdatdep, epcat, epdatzon, epferi, epprefer, epgarantia, epcostreal, eppromcost, epzona, epobs, epstockalt 
		FROM tef005
		WHERE epcodi=$this->codigo
		ON CONFLICT(epcodi) DO UPDATE SET epacti='S'
		RETURNING 1 as resultado;";
		$result = $db->query($sql)->fetchAll();
		return count($result);		
	}

	public function modificar_promo406(){

		$usuario = $_COOKIE['usuario'];
		$db 	= $this->conn();

		if($this->codigo >=20000000){
			$proceso = 'A NORMAL';
			$codigo  = $this->codigo-20000000;
			$codigo2 = $this->codigo;
		}else{
			$proceso = 'A PROMOCION';
			$codigo  = $this->codigo;
			$codigo2 = $this->codigo+20000000;
		}

		$sql = "SELECT epcodi FROM tef006 WHERE epcodi=$this->codigo AND dpcodi=$this->deposito AND epstact>=$this->stock;";
		$result = $db->query($sql)->fetchAll();
		if(count($result)>=1){

			if($proceso == "A NORMAL"){

				$sql = "INSERT INTO public.tef006(epcodi, dpcodi, epstact) VALUES ($codigo, $this->deposito, $this->stock) ON CONFLICT(epcodi,dpcodi) DO UPDATE SET epstact=tef006.epstact+$this->stock;";
				$db->query($sql);

				$sql = "UPDATE tef006 SET epstact=epstact-$this->stock WHERE epcodi=$codigo2 AND dpcodi=$this->deposito;";
				$db->query($sql);


				$sql= "INSERT INTO public.tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov,testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec) 

				SELECT 1, $codigo,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Entrada',0,'',$this->stock,
				(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo), $this->deposito, 0,0,0,
				'Conversion codigo 2','$usuario', current_date 
				UNION
				SELECT 2, $codigo2,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Salida',0,'',$this->stock,
				(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo2), $this->deposito, 0,0,0,
				'Conversion codigo 2','$usuario', current_date;";
				$db->query($sql);

			}

			if($proceso == "A PROMOCION"){

				$sql = "INSERT INTO public.tef006(epcodi, dpcodi, epstact) VALUES ($codigo2, $this->deposito, $this->stock) ON CONFLICT(epcodi,dpcodi) DO UPDATE SET epstact=tef006.epstact+$this->stock;";
				$db->query($sql);

				$sql = "UPDATE tef006 SET epstact=epstact-$this->stock WHERE epcodi=$codigo AND dpcodi=$this->deposito;";
				$db->query($sql);


				$sql= "INSERT INTO public.tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov,testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec) 

				SELECT 1, $codigo2,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Entrada',0,'',$this->stock,
				(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo2), $this->deposito, 0,0,0,
				'Conversion codigo 2','$usuario', current_date 
				UNION
				SELECT 2, $codigo,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Salida',0,'',$this->stock,
				(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo), $this->deposito, 0,0,0,
				'Conversion codigo 2','$usuario', current_date;";
				$db->query($sql);
			}


			$sql = "UPDATE tef005 SET epstock=cant FROM (SELECT epcodi codigo,sum(epstact) cant FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 GROUP BY epcodi ) AS datos	WHERE epcodi=codigo	AND epcodi=$codigo;";
			$db->query($sql);

			$sql = "UPDATE tef005 SET epstock=cant FROM ( SELECT epcodi codigo,sum(epstact) cant FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi)	AND dptipo=1 GROUP BY epcodi) AS datos WHERE epcodi=codigo and epcodi=$codigo2;";
			$db->query($sql);

		}
	}


	public function quitar_promo406(){

		$db 	= $this->conn();
		$sql 	= "UPDATE tef005 SET epacti='N' WHERE  epacti='S' AND epcodi={$this->codigo}  returning 1 as procesado";
		$result = $db->query($sql)->fetchAll();
		return count($result);
	}

	public function consultar_promo406($codigo){

		$result = array();
		$db 	= $this->conn();
		$sql = "SELECT epcodi codigo,a.dpcodi cod_deposito, a.dpcodi||' '||dpdesc deposito,
		case when a.dpcodi<6000 then 'PEND.TRANS'
		else 'NORMAL'
		end estado,
		epstact stock 
		FROM TEF006 a,TEF010 b 
		WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi)
		AND epstact>0
		AND dptipo=1
		AND epcodi=$codigo
		ORDER BY 2";
		
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function productos_promo600(){

		$result = array();
		$db 	= $this->conn();
		$sql = "SELECT
				    epcodi codigo,
				    trim(epdescl) descripcion,
				    epacti estado,
				    epprelis precio,
				   (select array_to_json
					(array_agg(row_to_json(d)))
					from (
					SELECT  codigo, promocion, trim(epdescl) descripcion
					FROM tef005,promocion6m

					WHERE epcodi=promocion
						) as d
					where epcodi=codigo
						)::text productos
				FROM tef005
				WHERE epcodi between 600000 and 690000 
				order by 3 desc,1;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function consultar_promo600(){

		$result = array();
		$db 	= $this->conn();
		$sql = "SELECT  promocion codigo, trim(epdescl) descripcion, estado FROM tef005,promocion6m 
				LEFT JOIN (select epcodi codigo, epacti estado from tef005) AS estado on promocion6m.codigo=estado.codigo
				WHERE epcodi=promocion and promocion6m.codigo=$this->codigo";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function activar_600(){

		$db 	= $this->conn();
		$sql 	= "UPDATE TEF005 SET epacti='$this->target' WHERE epcodi=$this->codigo;";
		$result = $db->query($sql);
	}

	public function agregar_600(){

		$db 	= $this->conn();
		$sql 	= "INSERT INTO public.promocion6m(codigo, promocion) SELECT $this->codigo,epcodi FROM tef005 WHERE epcodi=$this->target and epacti='S';";
		$result = $db->query($sql);
	}

	public function quitar_600(){

		$db 	= $this->conn();
		$sql 	= "DELETE FROM  promocion6m WHERE promocion=$this->target and codigo=$this->codigo";
		$result = $db->query($sql);
	}
}	
?>	