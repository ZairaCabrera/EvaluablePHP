<?php ob_start(); ?>

<div class="container text-center p-4">
    <div class="col-md-12" id="cabecera">
        <h1 class="h1Inicio">REGÍSTRARSE</h1>
    </div>
</div>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if(isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);">
                <?php echo htmlspecialchars($params['mensaje']); ?>
            </span></b>
        <?php endif; ?>
    </div>
    <div class="col-md-12">
        <?php 
        if(isset($errores)) {
            foreach ($errores as $error) {
                echo '<b><span style="color: rgba(200, 119, 119, 1);">' . $error . "<br>" . '</span></b>';
            }
        }
        ?>
    </div>
</div>

<div class="container text-center p-1">
    <!-- Formulario para registarse nuevo usuario -->
    <form action="index.php?ctl=registro" method="post" name="formRegistro" enctype="multipart/form-data">
        <p>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($params['nombre']); ?>" placeholder="Nombre">
        </p>
        <p>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($params['apellido']); ?>" placeholder="Apellido">
        </p>
        <p>
            <input type="text" name="nombreUsuario" value="<?php echo htmlspecialchars($params['nombreUsuario']); ?>" placeholder="Nombre de usuario">
        </p>
        <p>
            <input type="text" name="correoUser" value="<?php echo htmlspecialchars($params['correoUser']); ?>" placeholder="Correo">
        </p>
        <p>
            <input type="password" name="contrasenya" value="<?php echo htmlspecialchars($params['contrasenya']); ?>" placeholder="Contraseña">
        </p>
        <!-- Campo para subir la foto -->
        <p>
            <input type="file" name="foto" accept="image/*">
        </p>
        <input type="submit" name="bRegistro" value="Aceptar">
    </form>
</div>

<?php 
    $contenido = ob_get_clean();
    include 'layout.php';
?>
