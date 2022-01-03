<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<div id="contenedor"></div>
	<div id="api_key">d9455091fb3a837ba8e5dba80efd3a36265748122cc56f34b12c6cdc15a624e8</div>
</body>
</html>

<?php

?>

<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
	$(document).ready(function(){

		var api_key = $('#api_key').html();

		$.ajax({
			type:'GET',
			url:"https://30e74d898071.ngrok.io/api/v1/clientes",
			headers: {Authorization: api_key,
				'Content-Type': 'application/x-www-form-urlencoded'},
				success:function(resp){
					console.log(resp);
					var myJSON = JSON.stringify(resp);
					$("#contenedor").html(myJSON);					
				},
				error:function(resp){
					alert("error");
					console.log(resp);
				}
			});	
	})
	
</script>		