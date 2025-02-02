<?php ob_start() ?>
	
	<div class="container text-center p-4">
		<div class="col-md-12" id="cabecera">
			<h1 class="h1Inicio">REGÍSTRARSE</h1>
		</div>
	</div>
	
	<div class="container text-center py-2">
		<div class="col-md-12">
			<?php if(isset($params['mensaje'])) :?>
				<b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
			<?php endif; ?>
		</div>
		<div class="col-md-12">
			<?php foreach ($errores as $error) {?>
				<b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error."<br>"; ?></span></b>
			<?php } ?>
		</div>
	</div>
	
	<div class="container text-center p-1">
		<form ACTION="index.php?ctl=registro" METHOD="post" NAME="formRegistro">
			<p> <input TYPE="text" NAME="nombre" VALUE="<?php echo $params['nombre'] ?>" PLACEHOLDER="Nombre"> <br></p>
			<p> <input TYPE="text" NAME="apellido" VALUE="<?php echo $params['apellido'] ?>" PLACEHOLDER="Apellido"><br></p>
			<p> <input TYPE="text" NAME="nombreUsuario" VALUE="<?php echo $params['nombreUsuario'] ?>" PLACEHOLDER="Nombre de usuario"><br></p>
			<p> <input TYPE="text" NAME="correoUser" VALUE="<?php echo $params['correoUser'] ?>" PLACEHOLDER="Correo"><br></p>
			<p> <input TYPE="password" NAME="contrasenya" VALUE="<?php echo $params['contrasenya'] ?>" PLACEHOLDER="Contraseña"><br></p>
			<input TYPE="submit" NAME="bRegistro" VALUE="Aceptar"><br>
		</form>
	</div>
		
	<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>