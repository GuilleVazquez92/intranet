<?php
$total_paginas = ceil($total_items/50);

if($total_paginas>0){
	?>
	<nav aria-label="Page navigation example">
		<ul class="pagination pagination-sm justify-content-center">
			<?php 
			if($pagina>1){
				?>
				<li class="page-item">
					<a class="page-link" onclick="pagina(<?= $pagina-1;?>)" tabindex="-1">Anterior</a>
				</li>
				<?php
			}else{
				?>
				<li class="page-item disabled"></li>
				<?php
			}

			$grupo 	= ceil($pagina/5);
			$max 	= $grupo*5;
			$min  	= ($max-5)+1;

			for ($i=$min; $i <= $max ; $i++) {
				if($pagina==$i){
					?>
					<li class="page-item active">
						<span class="page-link"><?= $i;?>
						<span class="sr-only">(current)</span>
					</span>
				</li>
				<?php
			}else{
				?>
				<li class="page-item"><a class="page-link" onclick="pagina(<?= $i;?>)"><?= $i;?></a></li>
				<?php
			}
			if($i==$total_paginas){
				break;
			}
		}
		if($total_paginas>$pagina){
			?>
			<li class="page-item">
				<a class="page-link" onclick="pagina(<?= $pagina+1;?>)" tabindex="-1">Siguiente</a>
			</li>
			<?php
		}else{
			?>
			<li class="page-item disabled"></li>
			<?php
		} 
		?>
	</ul>
</nav>
<?php
}
?>