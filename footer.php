
<!--
<button type="button" class="btn btn-primary" id="liveToastBtn">Show live toast</button>

<div class="position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
	<div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2500">

		<div class="toast-header">
			<svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" role="img" aria-label=" :  " preserveAspectRatio="xMidYMid slice" focusable="false"><title> </title><rect width="100%" height="100%" fill="#28a745"></rect><text x="50%" y="50%" fill="#dee2e6" dy=".3em"> </text></svg>

			<strong class="mr-auto">Mensaje</strong>
			<small>Ahora</small>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
		</div>
		<div class="toast-body">
			Tiene un carrito nuevo desde la web.
		</div>
	</div>
</div>-->


<script src="<?= JS.'jquery.js?ver=1.7.2';?>"></script>
<script type="text/javascript" src="<?= JS.'bootstrap.min.js?ver='.date('dms');?>" ></script>
<script type="text/javascript" src="<?= JS.'propios.js?ver='.date('dms');?>" ></script>
<script>
	jQuery(document).ready(function () {
		jQuery('[data-toggle="tooltip"]').tooltip();
	});

	$('#liveToastBtn').click(function(){
		$('.toast').toast('show');
	});

</script>
</body>
</html>


