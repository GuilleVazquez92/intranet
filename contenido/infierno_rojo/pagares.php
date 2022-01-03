<?php

	require('../../pdf/fpdf.php');

	function hex2dec($couleur = "#000000"){
	    $R = substr($couleur, 1, 2);
	    $rouge = hexdec($R);
	    $V = substr($couleur, 3, 2);
	    $vert = hexdec($V);
	    $B = substr($couleur, 5, 2);
	    $bleu = hexdec($B);
	    $tbl_couleur = array();
	    $tbl_couleur['R']=$rouge;
	    $tbl_couleur['G']=$vert;
	    $tbl_couleur['B']=$bleu;
	    return $tbl_couleur;
	}

	//conversion pixel -> millimeter in 72 dpi
	function px2mm($px){
	    return $px*25.4/72;
	}

	function txtentities($html){
	    $trans = get_html_translation_table(HTML_ENTITIES);
	    $trans = array_flip($trans);
	    return strtr($html, $trans);
	}

	class PDF extends FPDF{

		protected $B;
		protected $I;
		protected $U;
		protected $HREF;
		protected $fontList;
		protected $issetfont;
		protected $issetcolor;

		// Cabecera de página
		function Header(){
		    $this->SetFont('Arial','B',14);
		    $this->Cell(128,10,'PAGARE A LA ORDEN  A LA VISTA',1,0,'C');
		    $this->Ln(10);
		}

		// Pie de página
		function Footer(){
		    $this->SetY(-15);
		    $this->SetFont('Arial','',8);
		    $this->Cell(30,10,date('d-m-Y H:i:s'),0,0,'C');
		}

		function WriteHTML($html)
		{
		    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>"); //remove all unsupported tags
		    $html=str_replace("\n",'',$html); //replace carriage returns with spaces
		    $html=str_replace("\t",'',$html); //replace carriage returns with spaces
		    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explode the string
		    foreach($a as $i=>$e)
		    {
		        if($i%2==0)
		        {
		            //Text
		            if($this->HREF)
		                $this->PutLink($this->HREF,$e);
		            elseif($this->tdbegin) {
		                if(trim($e)!='' && $e!="&nbsp;") {
		                    $this->Cell($this->tdwidth,$this->tdheight,$e,$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
		                }
		                elseif($e=="&nbsp;") {
		                    $this->Cell($this->tdwidth,$this->tdheight,'',$this->tableborder,'',$this->tdalign,$this->tdbgcolor);
		                }
		            }
		            else{
		                $this->Write(5,stripslashes(txtentities($e)),'FJ');
		            }
		        }
		        else
		        {
		            //Tag
		            if($e[0]=='/')
		                $this->CloseTag(strtoupper(substr($e,1)));
		            else
		            {
		                //Extract attributes
		                $a2=explode(' ',$e);
		                $tag=strtoupper(array_shift($a2));
		                $attr=array();
		                foreach($a2 as $v)
		                {
		                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
		                        $attr[strtoupper($a3[1])]=$a3[2];
		                }
		                $this->OpenTag($tag,$attr);
		            }
		        }
		    }
		}

		function OpenTag($tag, $attr)
		{
		    //Opening tag
		    switch($tag){

		        case 'SUP':
		            if( !empty($attr['SUP']) ) {    
		                //Set current font to 6pt     
		                $this->SetFont('','',6);
		                //Start 125cm plus width of cell to the right of left margin         
		                //Superscript "1" 
		                $this->Cell(2,2,$attr['SUP'],0,0,'L');
		            }
		            break;

		        case 'TABLE': // TABLE-BEGIN
		            if( !empty($attr['BORDER']) ) $this->tableborder=$attr['BORDER'];
		            else $this->tableborder=0;
		            break;
		        case 'TR': //TR-BEGIN
		            break;
		        case 'TD': // TD-BEGIN
		            if( !empty($attr['WIDTH']) ) $this->tdwidth=($attr['WIDTH']/4);
		            else $this->tdwidth=40; // Set to your own width if you need bigger fixed cells
		            if( !empty($attr['HEIGHT']) ) $this->tdheight=($attr['HEIGHT']/6);
		            else $this->tdheight=6; // Set to your own height if you need bigger fixed cells
		            if( !empty($attr['ALIGN']) ) {
		                $align=$attr['ALIGN'];        
		                if($align=='LEFT') $this->tdalign='L';
		                if($align=='CENTER') $this->tdalign='C';
		                if($align=='RIGHT') $this->tdalign='R';
		            }
		            else $this->tdalign='L'; // Set to your own
		            if( !empty($attr['BGCOLOR']) ) {
		                $coul=hex2dec($attr['BGCOLOR']);
		                    $this->SetFillColor($coul['R'],$coul['G'],$coul['B']);
		                    $this->tdbgcolor=true;
		                }
		            $this->tdbegin=true;
		            break;

		        case 'HR':
		            if( !empty($attr['WIDTH']) )
		                $Width = $attr['WIDTH'];
		            else
		                $Width = $this->w - $this->lMargin-$this->rMargin;
		            $x = $this->GetX();
		            $y = $this->GetY();
		            $this->SetLineWidth(0.2);
		            $this->Line($x,$y,$x+$Width,$y);
		            $this->SetLineWidth(0.2);
		            $this->Ln(1);
		            break;
		        case 'STRONG':
		            $this->SetStyle('B',true);
		            break;
		        case 'EM':
		            $this->SetStyle('I',true);
		            break;
		        case 'B':
		        case 'I':
		        case 'U':
		            $this->SetStyle($tag,true);
		            break;
		        case 'A':
		            $this->HREF=$attr['HREF'];
		            break;
		        case 'IMG':
		            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
		                if(!isset($attr['WIDTH']))
		                    $attr['WIDTH'] = 0;
		                if(!isset($attr['HEIGHT']))
		                    $attr['HEIGHT'] = 0;
		                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
		            }
		            break;
		        case 'BLOCKQUOTE':
		        case 'BR':
		            $this->Ln(5);
		            break;
		        case 'P':
		            $this->Ln(10);
		            break;
		        case 'FONT':
		            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
		                $coul=hex2dec($attr['COLOR']);
		                $this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
		                $this->issetcolor=true;
		            }
		            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
		                $this->SetFont(strtolower($attr['FACE']));
		                $this->issetfont=true;
		            }
		            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist) && isset($attr['SIZE']) && $attr['SIZE']!='') {
		                $this->SetFont(strtolower($attr['FACE']),'',$attr['SIZE']);
		                $this->issetfont=true;
		            }
		            break;
		    }
		}

		function CloseTag($tag)
		{
		    //Closing tag
		    if($tag=='SUP') {
		    }

		    if($tag=='TD') { // TD-END
		        $this->tdbegin=false;
		        $this->tdwidth=0;
		        $this->tdheight=0;
		        $this->tdalign="L";
		        $this->tdbgcolor=false;
		    }
		    if($tag=='TR') { // TR-END
		        $this->Ln();
		    }
		    if($tag=='TABLE') { // TABLE-END
		        $this->tableborder=0;
		    }

		    if($tag=='STRONG')
		        $tag='B';
		    if($tag=='EM')
		        $tag='I';
		    if($tag=='B' || $tag=='I' || $tag=='U')
		        $this->SetStyle($tag,false);
		    if($tag=='A')
		        $this->HREF='';
		    if($tag=='FONT'){
		        if ($this->issetcolor==true) {
		            $this->SetTextColor(0);
		        }
		        if ($this->issetfont) {
		            $this->SetFont('arial');
		            $this->issetfont=false;
		        }
		    }
		}

		function SetStyle($tag, $enable)
		{
		    //Modify style and select corresponding font
		    $this->$tag+=($enable ? 1 : -1);
		    $style='';
		    foreach(array('B','I','U') as $s) {
		        if($this->$s>0)
		            $style.=$s;
		    }
		    $this->SetFont('',$style);
		}

		function PutLink($URL, $txt)
		{
		    //Put a hyperlink
		    $this->SetTextColor(0,0,255);
		    $this->SetStyle('U',true);
		    $this->Write(5,$txt,$URL);
		    $this->SetStyle('U',false);
		    $this->SetTextColor(0);
		}
	}

	#########################################################################################################################################################################

	require('../../controlador/main.php');
	require('../CifrasEnLetras.php');
	require( CONTROLADOR . 'ir.php');
	


	$ir = new IR();
	$ir->cuenta = 2555851;
	$result = $ir->operacion_consultar();
					
	$result['cuenta'];	
	$result['operacion'];
	$result['cuotas_cant'];
	$result['monto_cuota'];
	$result['monto'];
	$result['tasa'];	

	$date 	= new DateTime($result['fecha_operacion']);
	$fecha 	= $date->format('j');
	$num_mes= $date->format('n');
	$año 	= $date->format('Y');
	$mes = array(1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'setiembre',10=>'octubre',11=>'noviembre',12=>'diciembre');


	$html = "<br>El día ".$fecha." de ".$mes[$num_mes]." de ".$año. " pagare/mos solidariamente y sin protesto a la orden de FACIL CUOTA S.A., en su domicilio de Avda. Rca. Argentina e/ José Martí, la suma de guaraníes <b>".mb_strtoupper(CifrasEnLetras::convertirNumeroEnLetras($result['monto']))." (Gs ".number_format($result['monto'],0,',','.').")</b>.<br><br>
La falta de pago de este documento y desde la constitución en mora por el deudor, originara automáticamente un interés del ................ por ciento mensual (....%), 
además de un interés punitorio del ................ por ciento mensual (....%), y gastos administrativos, sin que ello implique novación. Todas la partes intervinientes 
en este documento se someten a la jurisdicción y competencia de los jueces y tribunales de la Ciudad de Asunción, y declaran prorroga desde ya cualquier otra que pudiera corresponder.
El o los libradores de este documento fija domicilio especial, a los efectos del cumplimiento del mismo el o los que aparece/n más abajo. El plazo de presentación 
al cobro de este pagare a la orden queda ampliado por el librador hasta el..........................................................<br><br>
Se deja constancia de que los firmantes, autoriza para que en caso de un atraso superior a los 90 días en el pago del presente documento o de cualquier otra deuda vigente 
que mantenga con el Acreedor, incluyan mi (nuestro) nombre personal o Razón Social en el Registro General de Morosos de Informconf, o de cualquier otra empresa de actividad
similar que opere en plaza, como así también proporcionar es información a terceros interesados.................................";

	// Creación del objeto de la clase heredada
	$pdf = new PDF('P','mm','A4');
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',9);
	$pdf->WriteHTML(utf8_decode($html));
	//$pdf->Justify(utf8_decode($html),120,4);

	$pdf->Output();
?>