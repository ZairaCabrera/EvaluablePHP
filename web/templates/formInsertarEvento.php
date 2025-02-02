<?php ob_start(); ?>

<div class="container text-center py-2">
    <div class="col-md-12">
        <?php if(isset($params['mensaje'])) : ?>
            <b><span style="color: rgba(200, 119, 119, 1);">
                <?php echo htmlspecialchars($params['mensaje']); ?>
            </span></b>
        <?php endif; ?>
    </div>
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

<div class="container-fluid text-center">
    <div class="container">
        <!-- creamos un form que inserta un evento -->
        <form ACTION="index.php?ctl=insertarEvento" METHOD="post" enctype="multipart/form-data">
            <p> <input type="text" name="nomEvento" placeholder="Nombre del evento" 
                value="<?php echo isset($params['nomEvento']) ? htmlspecialchars($params['nomEvento']) : ''; ?>"><br></p>
            <p> <input type="text" name="cantante" placeholder="Cantante" 
                value="<?php echo isset($params['cantante']) ? htmlspecialchars($params['cantante']) : ''; ?>"><br></p>
			<p><input type="date" name="fecha" placeholder="Fecha (AAAA-MM-DD)" 
                       value="<?php echo isset($params['fecha']) ? htmlspecialchars($params['fecha']) : ''; ?>"></p>
            <p> <input type="file" name="cartel" accept="image/*" placeholder="Cartel"><br></p>
            <input type="submit" name="bInsertarL" value="Aceptar"><br>
        </form>
    </div>
</div>

<?php 
    $contenido = ob_get_clean();
    include 'layout.php';
?>
