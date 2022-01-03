<?php

function encriptar($usuario,$pass)
{

	//--------
	// Letra
	//--------

	$WVLet[1] = 'A';
	$WVLet[2] = 'B';
	$WVLet[3] = 'C';
	$WVLet[4] = 'D';
	$WVLet[5] = 'E';
	$WVLet[6] = 'F';
	$WVLet[7] = 'G';
	$WVLet[8] = 'H';
	$WVLet[9] = 'I';
	$WVLet[10] = 'J';
	$WVLet[11] = 'K';
	$WVLet[12] = 'L';
	$WVLet[13] = 'M';
	$WVLet[14] = 'N';
	$WVLet[15] = 'O';
	$WVLet[16] = 'P';
	$WVLet[17] = 'Q';
	$WVLet[18] = 'R';
	$WVLet[19] = 'S';
	$WVLet[20] = 'T';
	$WVLet[21] = 'U';
	$WVLet[22] = 'V';
	$WVLet[23] = 'W';
	$WVLet[24] = 'X';
	$WVLet[25] = 'Y';
	$WVLet[26] = 'Z';
	//------------//
	$WVLet[27] = '0';
	$WVLet[28] = '1';
	$WVLet[29] = '2';
	$WVLet[30] = '3';
	$WVLet[31] = '4';
	$WVLet[32] = '5';
	$WVLet[33] = '6';
	$WVLet[34] = '7';
	$WVLet[35] = '8';
	$WVLet[36] = '9';
	//------------//
	$WVLet[37] = ' ';

	//--------
	// Usuario
	//--------

	$WVUsu[1] = 92;
	$WVUsu[2] = 66;
	$WVUsu[3] = 51;
	$WVUsu[4] = 70;
	$WVUsu[5] = 45;
	$WVUsu[6] = 76;
	$WVUsu[7] = 37;
	$WVUsu[8] = 64;
	$WVUsu[9] = 83;
	$WVUsu[10] = 33;
	$WVUsu[11] = 86;
	$WVUsu[12] = 97;
	$WVUsu[13] = 67;
	$WVUsu[14] = 71;
	$WVUsu[15] = 89;
	$WVUsu[16] = 61;
	$WVUsu[17] = 57;
	$WVUsu[18] = 25;
	$WVUsu[19] = 94;
	$WVUsu[20] = 62;
	$WVUsu[21] = 99;
	$WVUsu[22] = 35;
	$WVUsu[23] = 87;
	$WVUsu[24] = 53;
	$WVUsu[25] = 74;
	$WVUsu[26] = 88;
	//-----------//
	$WVUsu[27] = 41;
	$WVUsu[28] = 38;
	$WVUsu[29] = 90;
	$WVUsu[30] = 93;
	$WVUsu[31] = 95;
	$WVUsu[32] = 98;
	$WVUsu[33] = 81;
	$WVUsu[34] = 84;
	$WVUsu[35] = 85;
	$WVUsu[36] = 72;
	//-----------//
	$WVUsu[37] = 75;

	//-----------
	// Contraseña
	//-----------

	$WVCon[1] = 54;
	$WVCon[2] = 85;
	$WVCon[3] = 92;
	$WVCon[4] = 78;
	$WVCon[5] = 97;
	$WVCon[6] = 47;
	$WVCon[7] = 91;
	$WVCon[8] = 68;
	$WVCon[9] = 44;
	$WVCon[10] = 82;
	$WVCon[11] = 58;
	$WVCon[12] = 99;
	$WVCon[13] = 83;
	$WVCon[14] = 74;
	$WVCon[15] = 73;
	$WVCon[16] = 79;
	$WVCon[17] = 65;
	$WVCon[18] = 95;
	$WVCon[19] = 12;
	$WVCon[20] = 51;
	$WVCon[21] = 42;
	$WVCon[22] = 77;
	$WVCon[23] = 39;
	$WVCon[24] = 86;
	$WVCon[25] = 75;
	$WVCon[26] = 59;
	$WVCon[27] = 93;
	$WVCon[28] = 98;
	$WVCon[29] = 80;
	$WVCon[30] = 8;
	$WVCon[31] = 7;
	$WVCon[32] = 5;
	$WVCon[33] = 23;
	$WVCon[34] = 21;
	$WVCon[35] = 31;
	$WVCon[36] = 45;
	//-----------//
	$WVCon[37] = 43;

/****/

	$contrasenha = $espacios_pass = $espacios_user = '';
	$loop 		 = 10;

	$usuario = preg_replace("/[^A-Za-z0-9 ]/", "",$usuario);
	for($x = 0; $x<($loop-(strlen($usuario))); $x++)
		{
			$espacios_user = $espacios_user.' ';
		}

	$pass = preg_replace("/[^A-Za-z0-9 ]/", "",$pass);
	for($x = 0; $x<($loop-(strlen($pass))); $x++)
		{
			$espacios_pass = $espacios_pass.' ';
		}

	$WUsu = strtoupper($usuario.$espacios_user);
	$WCon = strtoupper($pass.$espacios_pass);

/****************************************************/
	//-----------------
	// Enumerar usuario
	$WCan = 0;

	while ($WCan < $loop) 
	{
		$WPos = 1;
		$WUni = substr($WUsu , $WCan , 1);
		$WCan++;
				
		while($WPos <=37 )
		{
			if($WUni == $WVLet[$WPos])
			{
				$WNUsu[$WCan] = $WVUsu[$WPos];
			}
			$WPos++;
		}
	}
	
	//--------------------
	// Enumerar contraseña
	$WCan = 0;

	while($WCan < $loop) 
	{
		$WPos = 1;
		$WUni = substr($WCon , $WCan , 1);
		$WCan++;
					
		while($WPos <=37 )
		{
			if($WUni == $WVLet[$WPos])
			{
				$WNCon[$WCan] = $WVCon[$WPos];
			}
			$WPos++;
		}

		// Contabilizar usuario + contraseña
			$WNRes[$WCan] = $WNUsu[$WCan] + $WNCon[$WCan];

		// Concatenar la contraseña
			$contrasenha = $contrasenha.str_pad($WNRes[$WCan],3," ", STR_PAD_LEFT);	
	}

	return $contrasenha;
}	
?>

