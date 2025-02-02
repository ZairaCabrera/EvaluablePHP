<?php ob_start(); ?>

<div class="container my-4">
    <h2 class="text-center">Modificar Foto de Perfil</h2>
    
    <?php if (isset($params['mensaje']) && !empty($params['mensaje'])): ?>
        <div class="alert alert-info text-center">
            <?php echo htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Formulario para subir nueva foto de perfil -->
            <form action="index.php?ctl=ModificarPerfil" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="foto" class="form-label">Selecciona una nueva foto de perfil:</label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                </div>
                <input type="submit" name="bModificarPerfil" value="Actualizar Perfil" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>

<?php 
    $contenido = ob_get_clean();
    include 'layout.php';
?>
