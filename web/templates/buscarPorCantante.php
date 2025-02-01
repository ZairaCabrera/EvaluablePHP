<?php ob_start() ?>

<div class="container">
	<form name="formBusquedaTitulo" action="index.php?ctl=buscarPorTitulo" method="POST">
		<table>
			<tr>
				<td>Título del libro:</td>
				<td><input TYPE="text" NAME="titulo" VALUE="<?php echo $params['titulo'] ?>"></td>
				<td><input TYPE="submit" NAME="buscarPorTitulo" VALUE="Buscar"></td>
			</tr>
		</table>
	</form>
</div>

<?php if (isset($params['mensaje'])) {
	echo $params['mensaje'];
}
if (count($params['libros']) > 0) : ?>

	<div class="container">
		<div class="row">

			<div class="col-md-4">
				<p></p>
			</div>

			<div class="col-md-4">
				<table border="1" cellpadding="10">
					<tr align="center">
						<th>Título</th>
						<th>Autor</th>
						<th>Editorial</th>
					</tr>
					<?php foreach ($params['libros'] as $libro) : ?>
						<tr align="center">
							<td><a href="index.php?ctl=verLibro&idLibro=<?php echo $libro['idLibro'] ?>"> <?php echo $libro['titulo']; ?></a></td>
							<td><?php echo $libro['autor'] ?></td>
							<td><?php echo $libro['editorial'] ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div class="col-md-4">
				<p></p>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>