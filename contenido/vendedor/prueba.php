







			<!-- Lugar de entrega -->	
			<p class="px-2 py-1 my-1 bg-secondary text-white rounded">Lugar de Entrega</p>
			<div class="card card-body">
				<textarea class="form-control" id="lugar_entrega" name="lugar_entrega" rows="3" onchange=""> </textarea>
			</div>	


			<button type="button" class="btn btn-sm btn-primary" onclick="cargar_carro();">ENVIAR</button>
			<br>
			<br>
		</div>	
		



	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="ModalDetalleCarrito" tabindex="-1" role="dialog" aria-labelledby="ModalDetalleCarritoTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalDetalleCarritoTitle">Lista de Productos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="resultado">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

</div>
<div id="contenedor_resultado"></div>
