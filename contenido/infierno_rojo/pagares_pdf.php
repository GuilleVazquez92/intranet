<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require(INCLUDES.'CifrasEnLetras.php');
require(INCLUDES.'PHPPdf/fpdf.php');
require( CONTROLADOR . 'ir.php');

$ir = new IR();
$ir->cuenta = $_GET['cuenta'];
$tipo  		= $_GET['tipo'];
$result 	= $ir->operacion_consultar();
$cliente 	= $ir->consultar_cliente();


class PDF extends FPDF
{

	protected $B;
	protected $I;
	protected $U;
	protected $HREF;
	protected $fontList;
	protected $issetfont;
	protected $issetcolor;

	function WriteHTML($html)
	{
		 // Intérprete de HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				 // Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				 // Etiqueta
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					 // Extraer atributos
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag, $attr)
	{
		 // Etiqueta de apertura
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag)
	{
		 // Etiqueta de cierre
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}

	function SetStyle($tag, $enable)
	{
		 // Modificar estilo y escoger la fuente correspondiente
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}

	function PutLink($URL, $txt)
	{
		 // Escribir un hiper-enlace
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}


	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		$k=$this->k;
		if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
		{
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			{
				$this->ws=$ws;
				$this->_out(sprintf('%.3F Tw',$ws*$k));
			}
		}
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$s='';
		if($fill || $border==1)
		{
			if($fill)
				$op=($border==1) ? 'B' : 'f';
			else
				$op='S';
			$s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		}
		if(is_string($border))
		{
			$x=$this->x;
			$y=$this->y;
			if(is_int(strpos($border,'L')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'T')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(is_int(strpos($border,'R')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'B')))
				$s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		}
		if($txt!='')
		{
			if($align=='R')
				$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
			elseif($align=='C')
				$dx=($w-$this->GetStringWidth($txt))/2;
			elseif($align=='FJ')
			{
				//Set word spacing
				$wmax=($w-2*$this->cMargin);
				$this->ws=($wmax-$this->GetStringWidth($txt))/substr_count($txt,' ');
				$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
				$dx=$this->cMargin;
			}
			else
				$dx=$this->cMargin;
			$txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
			if($this->ColorFlag)
				$s.='q '.$this->TextColor.' ';
			$s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt);
			if($this->underline)
				$s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			if($this->ColorFlag)
				$s.=' Q';
			if($link)
			{
				if($align=='FJ')
					$wlink=$wmax;
				else
					$wlink=$this->GetStringWidth($txt);
				$this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$wlink,$this->FontSize,$link);
			}
		}
		if($s)
			$this->_out($s);
		if($align=='FJ')
		{
			//Remove word spacing
			$this->_out('0 Tw');
			$this->ws=0;
		}
		$this->lasth=$h;
		if($ln>0)
		{
			$this->y+=$h;
			if($ln==1)
				$this->x=$this->lMargin;
		}
		else
			$this->x+=$w;
	}

	function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){

		$wide = $baseline;
		$narrow = $baseline / 3 ; 
		$gap = $narrow;

		$barChar['0'] = 'nnnwwnwnn';
		$barChar['1'] = 'wnnwnnnnw';
		$barChar['2'] = 'nnwwnnnnw';
		$barChar['3'] = 'wnwwnnnnn';
		$barChar['4'] = 'nnnwwnnnw';
		$barChar['5'] = 'wnnwwnnnn';
		$barChar['6'] = 'nnwwwnnnn';
		$barChar['7'] = 'nnnwnnwnw';
		$barChar['8'] = 'wnnwnnwnn';
		$barChar['9'] = 'nnwwnnwnn';
		$barChar['A'] = 'wnnnnwnnw';
		$barChar['B'] = 'nnwnnwnnw';
		$barChar['C'] = 'wnwnnwnnn';
		$barChar['D'] = 'nnnnwwnnw';
		$barChar['E'] = 'wnnnwwnnn';
		$barChar['F'] = 'nnwnwwnnn';
		$barChar['G'] = 'nnnnnwwnw';
		$barChar['H'] = 'wnnnnwwnn';
		$barChar['I'] = 'nnwnnwwnn';
		$barChar['J'] = 'nnnnwwwnn';
		$barChar['K'] = 'wnnnnnnww';
		$barChar['L'] = 'nnwnnnnww';
		$barChar['M'] = 'wnwnnnnwn';
		$barChar['N'] = 'nnnnwnnww';
		$barChar['O'] = 'wnnnwnnwn'; 
		$barChar['P'] = 'nnwnwnnwn';
		$barChar['Q'] = 'nnnnnnwww';
		$barChar['R'] = 'wnnnnnwwn';
		$barChar['S'] = 'nnwnnnwwn';
		$barChar['T'] = 'nnnnwnwwn';
		$barChar['U'] = 'wwnnnnnnw';
		$barChar['V'] = 'nwwnnnnnw';
		$barChar['W'] = 'wwwnnnnnn';
		$barChar['X'] = 'nwnnwnnnw';
		$barChar['Y'] = 'wwnnwnnnn';
		$barChar['Z'] = 'nwwnwnnnn';
		$barChar['-'] = 'nwnnnnwnw';
		$barChar['.'] = 'wwnnnnwnn';
		$barChar[' '] = 'nwwnnnwnn';
		$barChar['*'] = 'nwnnwnwnn';
		$barChar['$'] = 'nwnwnwnnn';
		$barChar['/'] = 'nwnwnnnwn';
		$barChar['+'] = 'nwnnnwnwn';
		$barChar['%'] = 'nnnwnwnwn';

		$this->SetFont('Arial','',10);
		$this->Text($xpos, $ypos + $height + 4, $code);
		$this->SetFillColor(0);

		$code = '*'.strtoupper($code).'*';
		for($i=0; $i<strlen($code); $i++){
			$char = $code[$i];
			if(!isset($barChar[$char])){
				$this->Error('Invalid character in barcode: '.$char);
			}
			$seq = $barChar[$char];
			for($bar=0; $bar<9; $bar++){
				if($seq[$bar] == 'n'){
					$lineWidth = $narrow;
				}else{
					$lineWidth = $wide;
				}
				if($bar % 2 == 0){
					$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
				}
				$xpos += $lineWidth;
			}
			$xpos += $gap;
		}
	}
}

/** Fecha de operacion ***/	
$date 			= new DateTime($result['fecha']);
$fecha 			= $date->format('j');
$num_mes 		= $date->format('n');
$año 			= $date->format('Y');

/** Fecha de 1ervencimiento ***/	
$pri_vence 			= new DateTime($result['p_vencimiento']);
$fecha_1ervence 	= $pri_vence->format('j');
$num_mes_1ervence	= $pri_vence->format('n');
$año_1ervence 		= $pri_vence->format('Y');

/** Fecha de vencimiento ***/	
$vence 			= new DateTime($result['vencimiento']);
$fecha_vence 	= $vence->format('j');
$num_mes_vence	= $vence->format('n');
$año_vence 		= $vence->format('Y');

$mes = array(1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'setiembre',10=>'octubre',11=>'noviembre',12=>'diciembre');

$pdf = new PDF('P','mm','A4');
$pdf->SetMargins(15, 10, 15,15);

if($tipo == 105){

	$pdf->AddPage();
	$pdf->Code39(15,20,$result['operacion'],1,8);
/*	$pdf->Code39(148,262,$result['operacion'],1,8);*/
	$pdf->SetFillColor(255,255,255);

	$pdf->SetFont('Arial','B',22);
	$pdf->Cell(180,10,'PAGARE A LA ORDEN  A LA VISTA',0,1,'C');

	$html  = str_pad("El día ........ de ........................ de ................ pagare/mos  solidariamente y sin protesto a la orden de FACIL CUOTAS S.A., en su domicilio de Avda. Rca. Argentina e/ José Martí, la suma de guaraníes ".mb_strtoupper(CifrasEnLetras::convertirNumeroEnLetras($result['monto']))." (Gs ".number_format($result['monto'],0,',','.').").",372," ");

	$line1  = str_pad("La falta de pago de este documento y desde la constitución en mora por EL DEUDOR, originara automáticamente un interés del ........ por ciento mensual (........%), además de un interés punitorio del ........ por ciento mensual (........%), y gastos administrativos, sin que ello implique novación. Todas la partes intervinientes en este documento se someten a la jurisdicción y competencia de los jueces y tribunales de la ciudad de Asunción, y declaran prorroga desde ya cualquier otra que pudiera corresponder. El o los libradores de este documento fija domicilio especial, a los efectos del cumplimiento del mismo el o los que aparece/n más abajo. El plazo de presentación al cobro de este pagare a la orden queda ampliado por el librador hasta el ".$fecha_vence." de ".$mes[$num_mes_vence]." de ".$año_vence.".",868," ");

	$line2 = str_pad("Se deja constancia de que los firmantes, autoriza para que en caso de un atraso superior a los 90 días en el pago del presente documento o de cualquier otra deuda vigente que mantenga con EL ACREEDOR, incluyan mi (nuestro) nombre personal o Razón Social en el Registro General de Morosos de Informconf, o de cualquier otra empresa de actividad similar que opere en plaza, como así también proporcionar es información a terceros interesados.",496," ");


	$pdf->SetFont('Arial','',9);
	$pdf->Ln(8);
	$pdf->Cell(180,5,utf8_decode('Asunción, '.$fecha." de ".$mes[$num_mes]." de ".$año),0,0,'R');
	$pdf->Ln(8);

	$pdf->MultiCell(180,5,utf8_decode($html),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line1),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line2),0,'FJ',1);

	$pdf->Ln(10);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,0,'L');
	$pdf->Cell(10,5,'',0,0,'L');
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,1,'L');

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'DEUDOR : '.$result['cliente'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'CODEUDOR : ......................................................................',0,'L',1);

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'C. IDENTIDAD : '.$result['documento'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'C. IDENTIDAD :  ..................................................................',0,'L',1);

	$pdf->Ln(5);	
	$pdf->Cell(180,5,'___________________________________________________________________________________________________________________________________',0,1,'C');

	$pdf->Ln(5);	
	$pdf->Cell(85,4,$result['cliente'],1,1,'C');
	$pdf->Cell(28,4,'CUOTA',1,0,'C');
	$pdf->Cell(28,4,'FECHA',1,0,'C');
	$pdf->Cell(29,4,'MONTO',1,1,'C');

}



$ir->operacion = $result['operacion'];
$cuota = $ir->cuotero_consultar();

$cantidad = count($cuota);
$max = round($cantidad/2);
$min_cuota = 0;
$max_cuota = 0;

for ($i=0; $i < $cantidad ; $i++) { 

	if($i == 0)
		$min_cuota = $cuota[$i]['monto'];

	if($min_cuota != $cuota[$i]['monto'])
		$max_cuota =$cuota[$i]['monto']; 			

	if($tipo==105){	
		$z = $i+$max;
		if($max > $i){

			$x = $pdf->GetY();
			$pdf->MultiCell(85,4,$pdf->Cell(28,4,$cuota[$i]['cuota'],1,0,'C'),$pdf->Cell(28,4,date("d-m-Y", strtotime($cuota[$i]['vencimiento'])),1,0,'C'),$pdf->Cell(29,4,number_format($cuota[$i]['monto'],0,',','.'),1,0,'C'),0,'L',0);
			$pdf->SetXY(110,$x);
			
			if($z < $cantidad)
				$pdf->MultiCell(85,4,$pdf->Cell(28,4,$cuota[$z]['cuota'],1,0,'C'),$pdf->Cell(28,4,date("d-m-Y", strtotime($cuota[$z]['vencimiento'])),1,0,'C'),$pdf->Cell(29,4,number_format($cuota[$z]['monto'],0,',','.'),1,0,'C'),0,'L',0);
		}
	}
}



								################################## PAGINA 2  #################################################
if($tipo==105){

	$pdf->AddPage();
	$pdf->Code39(15,20,$result['operacion'],1,8);
/*	$pdf->Code39(148,262,$result['operacion'],1,8);*/
	$pdf->SetFillColor(255,255,255);

	$pdf->SetFont('Arial','B',22);
	$pdf->Cell(180,10,'CONTRATO DE REFINANCIACIACION',0,1,'C');
	$pdf->Ln(15);


	$html = str_pad("En el presente contrato hacemos constar que el Sr/a: ".$result['cliente']." con cédula de identidad número:".$result['documento'].", domiciliado en ".$cliente['direccion_part']." con identificación de cliente: ".$cliente['cuenta']." adeuda a la empresa FACIL CUOTAS S.A. el monto total de guaraníes ".mb_strtoupper(CifrasEnLetras::convertirNumeroEnLetras($result['monto']))." (Gs ".number_format($result['monto'],0,',','.').") en la operación número ".$result['operacion'].", la cual será refinanciada en el plazo de ".($cantidad-1)." cuotas de Gs. ".number_format($result['monto_cuota'],0,',','.')." siendo un monto total de Gs.".number_format(($cantidad-1)*$result['monto_cuota'],0,',','.')." quedando así una quita condicionada de Gs. ".number_format($max_cuota,0,',','.')." que será condonado con el último pago de la cuota de ser abonado en plazo y tiempo.",744," "); 

	$line1 = str_pad("En el caso de incumplir con el pago de  la cuota al monto total refinanciado será sumado el valor de la quita condicionada que deberá ser pagada hasta la totalidad de la deuda al valor del pagare firmado.",248," "); 


	$line2 = str_pad("En conformidad, se realiza la firma al pie del certificado en dos copias de un mismo tenor y aun solo efecto, a los ".$fecha." días del mes de ".$mes[$num_mes]." de ".$año.".",248," "); 


	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(180,5,utf8_decode($html),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line1),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line2),0,'FJ',1);
	$pdf->Ln(3);

	$pdf->Ln(10);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,0,'L');
	$pdf->Cell(10,5,'',0,0,'L');
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,1,'L');

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'DEUDOR : '.$result['cliente'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'CODEUDOR : ......................................................................',0,'L',1);

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'C. IDENTIDAD : '.$result['documento'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'C. IDENTIDAD :  ..................................................................',0,'L',1);

} else{




								################################## PAGINA 3  #################################################	

	$pdf->AddPage();
	$pdf->Code39(15,20,$result['operacion'],1,8);
/*	$pdf->Code39(148,262,$result['operacion'],1,8);*/
	$pdf->SetFillColor(255,255,255);

	$pdf->SetFont('Arial','B',22);
	$pdf->Cell(180,10,'CONVENIO DE PAGO',0,1,'C');
	$pdf->Ln(15);

	// 124 caracteres por linea
	$html = str_pad("En la ciudad de Asunción, República del Paraguay, a los ".$fecha." días del mes de ".$mes[$num_mes]." de ".$año.", entre Sr./Sra.".$result['cliente'].", con cédula de identidad ".$result['documento'].", con domicilio en ".$cliente['direccion_part'].", en adelante EL/LA DEUDOR/A. y FACIL CUOTAS S.A., representado en este acto por la representante legal, Abogada Lilian Nancy Romero M., con domicilio en la casa de la calle Av. Avenida República Argentina 1819 esquina José Martí, de la ciudad de Asunción, en adelante EL ACREEDOR, se formaliza el presente Contrato Privado de Acuerdo de Pago, en virtud a la operación número ".$result['operacion'].", y conforme a las siguientes cláusulas y condiciones.",868," ");

	$line1 = str_pad("1- Asimismo, EL DEUDOR manifiesta que, EL ACREEDOR es el legítimo titular del crédito, y consecuentemente, manifiesta EL DEUDOR, que la obligación en líneas precedente citada, es correcta, liquida, vencida y exigible por EL ACREEDOR por la vía ejecutiva.",372," ");

	$line2= str_pad("2- EL DEUDOR y EL ACREEDOR, de común acuerdo, en forma libre y espontánea, manifiestan que la obligación reconocida por EL DEUDOR, a través del presente instrumento, será pagada de la siguiente forma y modo: el pago de GUARANIES ".mb_strtoupper(CifrasEnLetras::convertirNumeroEnLetras($result['monto']))." (Gs.".number_format($result['monto'],0,',','.')."), que se realizara en ".($cantidad-1)." cuotas de GS. ".number_format($result['monto_cuota'],0,',','.')." cada una y con vencimientos mensuales y consecutivas a partir del ".$fecha_1ervence." de ".$mes[$num_mes_1ervence]." de ".$año_1ervence.", el monto de GUARANIES ".mb_strtoupper(CifrasEnLetras::convertirNumeroEnLetras(round($max_cuota)))." (Gs. ".number_format($max_cuota,0,',','.')."), como quita condicionada al cumplimiento de los vencimientos en forma, el cual se otorgara en forma automática cancelando la deuda.",744," ");

	$line3= str_pad("3- EL DEUDOR se compromete al pago regular y puntual de las cuotas convenidas, en caso de atrasos, la operación generara intereses moratorios y punitorios que deben ser canceladas al momento del pago de la cuota vencida. La quita condicionada quedara sin efecto, pudiendo ser refinanciada dicha quita más el saldo de las cuotas pendientes.",372," ");

	$line4= str_pad("4- Si EL DEUDOR cumple en forma regular, puntual y estrictamente el presente acuerdo y/o cancelare totalmente el saldo deudor antes de los vencimientos pactados, EL ACREEDOR, entregará el documento original una vez cumplido las 72 horas de cancelación y en el acto se le otorgará un certificado o constancia de haber cancelado la presente obligación.",372," ");

	$line5= str_pad("5- Se deja expresa salvedad, que ambas partes concurren en forma libre y espontánea, no mediando vicios de voluntad ni por parte de los representantes de EL ACREEDOR ni por parte de EL DEUDOR, así mismo las partes manifiestan que no se dan los recaudos estipulados por los Art. 671 y 691, de Código Civil Paraguayo, vigente.",372," ");

	$line6= str_pad("6- Las partes, manifiestan en forma irrevocable, que en caso de incumplimiento o incluso diferencias derivadas de las cláusulas del presente acuerdo, se someterán única y exclusivamente a la jurisdicción de los jueces y tribunales de la ciudad de Asunción, Capital de la República del Paraguay.",372," ");

	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(180,5,utf8_decode($html),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line1),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line2),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line3),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line4),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line5),0,'FJ',1);
	$pdf->Ln(3);
	$pdf->MultiCell(180,5,utf8_decode($line6),0,'FJ',1);

	$pdf->Ln(10);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,0,'L');
	$pdf->Cell(10,5,'',0,0,'L');
	$pdf->Cell(85,5,'FIRMA : ................................................................................',0,1,'L');

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'DEUDOR : '.$result['cliente'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'REPRESENTANTE LEGAL : ...............................................',0,'L',1);

	$y = $pdf->GetY();
	$pdf->MultiCell(85,5,'C. IDENTIDAD : '.$result['documento'],0,'L',0);
	$pdf->SetXY(110,$y);
	$pdf->SetFillColor(255,255,255);
	$pdf->MultiCell(85,5,'C. IDENTIDAD :  ..................................................................',0,'L',1);



	$pdf->SetFont('Arial','',5);
	$ir->operacion = $result['operacion'];
	$cuota = $ir->cuotero_consultar();

	$cantidad = count($cuota);
	$max = round($cantidad/2);
	$min_cuota = 0;
	$max_cuota = 0;

	for ($i=0; $i < $cantidad ; $i++) { 

		if($i == 0)
			$min_cuota = $cuota[$i]['monto'];

		if($min_cuota != $cuota[$i]['monto'])
			$max_cuota =$cuota[$i]['monto']; 			

		if($tipo!=105){	
			$z = $i+$max;
			if($max > $i){

				$x = $pdf->GetY();
				$pdf->MultiCell(85,3,$pdf->Cell(28,3,$cuota[$i]['cuota'],1,0,'C'),$pdf->Cell(28,3,date("d-m-Y", strtotime($cuota[$i]['vencimiento'])),1,0,'C'),$pdf->Cell(29,3,number_format($cuota[$i]['monto'],0,',','.'),1,0,'C'),0,'L',0);
				$pdf->SetXY(110,$x);

				if($z < $cantidad)
					$pdf->MultiCell(85,3,$pdf->Cell(28,3,$cuota[$z]['cuota'],1,0,'C'),$pdf->Cell(28,3,date("d-m-Y", strtotime($cuota[$z]['vencimiento'])),1,0,'C'),$pdf->Cell(29,3,number_format($cuota[$z]['monto'],0,',','.'),1,0,'C'),0,'L',0);
			}
		}
	}
}

$pdf->Output($result['operacion'].'.pdf','I');
?>








