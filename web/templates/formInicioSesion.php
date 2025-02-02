<?php ob_start() ?>
	
	<div class="container text-center p-4">
		<div class="col-md-12" id="cabecera">
			<h1 class="h1Inicio">Los mejores eventos están aquí</h1>
		</div>
	</div>

	<div class="container text-center py-2">
		<div class="col-md-12">
			<?php if(isset($params['mensaje'])) :?>
				<b><span style="color: rgba(200, 119, 119, 1);">
					<?php echo $params['mensaje'] ?>
				</span></b>
			<?php endif; ?>
		</div>
	</div>
	
	<div class="container text-center p-4">
		<form action="index.php?ctl=iniciarSesion" method="post" name="formIniciarSesion">
			<h5><b>Iniciar sesión</b></h5>
			<p>
				<input type="text" name="nombreUsuario" placeholder="Nombre de usuario">
			</p>
			<p>
				<input type="password" name="contrasenya" placeholder="Contraseña">
			</p>	
			<input type="submit" name="bIniciarSesion" value="Aceptar"><br>
			
			<!-- Enlace para registrarse si no se tiene cuenta -->
			<p>
				¿No tienes cuenta? <a href="index.php?ctl=registro">Regístrate aquí</a>
			</p>
		</form>	
	</div>
	
<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>
