
<?php

if (isset($_COOKIE['bienvenida'])) {
    echo "<p>" . $_COOKIE['bienvenida'] . "</p>";
}

?>
<div class="container-fluid menu text-center p-3 my-4">
	<div class="container">
		<div class="row">
			<div class="col-md-12 ">
				<a href="index.php?ctl=home" class="p-4">Home</a>
				<a href="index.php?ctl=ListarEventos" class="p-3">Eventos</a>
				<a href="index.php?ctl=buscarPorEvento" class="p-3">Buscar por evento</a>
				<a href="index.php?ctl=BuscarPorCantante" class="p-3">Buscar por cantante</a>
				<a href="index.php?ctl=iniciarSesion" class="p-3">Inicia Sesión</a>
			</div>
		</div>
	</div>
</div>