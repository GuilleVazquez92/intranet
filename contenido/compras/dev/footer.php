	</body>

	<script
  			src="https://code.jquery.com/jquery-3.4.1.min.js"
  			integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  			crossorigin="anonymous">
    </script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
			integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
			crossorigin="anonymous">
	</script>
	<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" 
			integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" 
			crossorigin="anonymous">
	</script>

<!--	<script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
	<script src="https://code.jquery.com/ui/1.8.21/jquery-ui.min.js"></script>  -->
<!--	

		<script src="../../js/jquery.ui.touch-punch.min.js"></script> 
		Comentado recientemente.


-->   
 	<script>

		$('#widget').sortable({
			update : function(event,ui){
			$(this).children().each(function(index){
				if($(this).attr('data-position') != (index+1)){
					$(this).attr('data-position',(index+1)).addClass('updated');
				}
			});
				save_position();
			}
		});

	function save_position(){
		var positions = [];
		$('.updated').each(function(){
			positions.push([$(this).attr('data-index'),$(this).attr('data-position')]);
			$(this).removeClass('updated');
		});

		$.ajax({
			url: 'solicitud_pl.php',
			method: 'POST',
			dataType: 'text',
			data:{
				update:1,
				positions: positions
			}, success: function(response){
				console.log(response);
			}
		});
	}

	</script>
</html>
