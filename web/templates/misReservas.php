<?php
ob_start();
if (isset($params['mensaje'])) { 
	echo $params['mensaje'] ;
}else {
    ?>
	 
<b><span style="color: rgba(200, 119, 119, 1);"></span></b>

	<div class"container-fluid">
			<div class="container">
				<div class="row">				
					<div class="col-md-3">
						<p></p>
					</div>
					<div class="col-md-6">
						<h1 class="p-2"><?php echo $params['libros']['titulo']?></h1>
						<table border="1" cellpadding="10">
							<tr align="center">
								<th>Título</th>
								<td><?php echo $params['libros']['titulo']; ?></td>
							</tr>
							<tr align="center">
								<th>Autor</th>
								<td><?php echo $params['libros']['autor']; ?></td>
							</tr>
							<tr align="center">
								<th>Editorial</th>
								<td><?php echo $params['libros']['editorial']; ?></td>
							</tr>
							<tr align="center">
								<th>Fecha de publicación</th>
								<td><?php echo $params['libros']['anyo']; ?></td>
							</tr>
						</table>
					</div>

					<div class="col-md-3">	            
              		
										
				</div>
			</div>
	</div>


<?php } 

$contenido = ob_get_clean();

include 'layout.php' ?>