<?php ob_start() ?>

<div class="container">
	<form name="formBusquedaEvento" action="index.php?ctl=buscarPorEvento" method="POST">
		<table>
			<tr>
				<td>Nombre del Evento:</td>
				<td>
                    <input type="text" name="nomEvento" value="<?php echo htmlspecialchars($params['nomEvento']); ?>">
                </td>
				<td>
                    <input type="submit" name="buscarPorEvento" value="Buscar">
                </td>
			</tr>
		</table>
	</form>
</div>

<?php 
if (isset($params['mensaje'])) {
	echo $params['mensaje'];
}
if (count($params['eventos']) > 0) : ?>

	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<p></p>
			</div>

			<div class="col-md-4">
				<table border="1" cellpadding="10">
					<tr align="center">
						<th>Nombre del Evento</th>
						<th>Cantante</th>
						<th>Fecha</th>
						<th>Cartel</th>
					</tr>
					<?php foreach ($params['eventos'] as $evento) : ?>
						<tr align="center">
							<td>
                                <a href="index.php?ctl=verEvento&idEvento=<?php echo $evento['id_eventos']; ?>">
                                    <?php echo $evento['nomEvento']; ?>
                                </a>
                            </td>
							<td><?php echo $evento['cantante']; ?></td>
							<td><?php echo $evento['fecha']; ?></td>
							<td>
                                <img src="<?php echo $evento['cartel']; ?>" alt="Cartel del evento" style="max-width:100px; height:auto;">
                            </td>
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
