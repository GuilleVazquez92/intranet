<?php 

	$salt 	 = '34a@$#aA98gsdf45mk23$';
	$pass 	 = 'f4c'.date('Y').'L4n'.date('d').'d14';
	$api_key = hash('sha256', $salt . $pass);
	define('API_KEY', $api_key); 

?>