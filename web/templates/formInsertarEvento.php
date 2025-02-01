<?php ob_start(); ?>

<div class="container text-center py-2">
	<div class="col-md-12">
			<?php if(isset($params['mensaje'])) :?>
				<b><span style="color: rgba(200, 119, 119, 1);"><?php echo $params['mensaje'] ?></span></b>
			<?php endif; ?>
	</div>
</div>

<div class="col-md-12">
			<?php foreach ($errores as $error) {?>
				<b><span style="color: rgba(200, 119, 119, 1);"><?php echo $error."<br>"; ?></span></b>
			<?php } ?>
</div>

<div class="container-fluid text-center">
	<div class="container">
		<form ACTION="index.php?ctl=insertarL" METHOD="post">
			<p>* <input TYPE="text" NAME="titulo" PLACEHOLDER="Título"><br></p>
			<p>* <input TYPE="text" NAME="autor" PLACEHOLDER="Autor"><br></p>
			<p>* <input TYPE="text" NAME="editorial" PLACEHOLDER="Editorial"><br></p>
			<p>   <input TYPE="text" NAME="anyo" PLACEHOLDER="Año"><br></p>	
			<input TYPE="submit" name="bInsertarL" VALUE="Aceptar" PLACEHOLDER="Nombre de usuario"><br>
		</form>
	</div>
</div>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>
