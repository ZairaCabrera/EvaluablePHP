
<h3 class="text">Bienvenido <?php echo $_SESSION['nombreUsuario']?></h3>
<div class="container-fluid menu text-center p-3 my-4">
	<div class="container">
		<div class="row">
			<div class="col-md-12 ">
				<a href="index.php?ctl=home" class="p-4">INICIO</a>
				<a href="index.php?ctl=listarLibros" class="p-4">LIBROS</a>
				<a href="index.php?ctl=buscarPorTitulo" class="p-4">BUSCAR POR TÍTULO</a>
				<a href="index.php?ctl=buscarPorAutor" class="p-4">BUSCAR POR AUTOR</a>
				<a href="index.php?ctl=buscarPorEditorial" class="p-4">BUSCAR POR EDITORIAL</a>
				
				<a HREF="index.php?ctl=salir"><button TYPE="button" class="btn btn-secondary mt-3" style="width: 150px;">CERRAR SESIÓN</button></a>

			</div>
		</div>
	</div>
</div>