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
                        <th>Reserva</th>
                    </tr>
                    <?php foreach ($params['eventos'] as $evento) : ?>
                        <tr align="center">
                            <td>
                                <a href="index.php?ctl=verEvento&idEvento=<?php echo $evento['id_evento']; ?>">
                                    <?php echo $evento['nomEvento']; ?>
                                </a>
                            </td>
                            <td><?php echo $evento['cantante']; ?></td>
                            <td><?php echo $evento['fecha']; ?></td>
                            <td>
                                <!-- la ruta se pasara en 'cartel' -->
                                <img src="<?php echo $evento['cartel']; ?>" alt="Cartel del evento" style="max-width:100px; height:auto;">
                            </td>
                            <td>
                            <?php if (isset($_SESSION['nivel_usuario']) && $_SESSION['nivel_usuario'] > 0): ?>
                                    <!-- Usuario registrado: botón para reservar -->
                                    <a href="index.php?ctl=Reservar&idEvento=<?php echo $evento['id_evento']; ?>" class="btn btn-success">
                                        Reservar
                                    </a>
                                <?php else: ?>
                                    <!-- Usuario no registrado: redirige al login -->
                                    <a href="index.php?ctl=iniciarSesion" class="btn btn-primary">
                                        Inicia sesión para reservar
                                    </a>
                                <?php endif; ?>
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
<?php else : ?>
    <p>No se encontraron eventos.</p>
<?php endif; ?>


<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>
