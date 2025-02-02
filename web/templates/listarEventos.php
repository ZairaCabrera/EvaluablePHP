<?php ob_start(); ?>

<div class="container">
    <h2 class="text-center my-4">Listado de Eventos</h2>
    
    <!-- Mostrar mensaje si existe -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-warning text-center">
            <?php echo htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($params['eventos']) && count($params['eventos']) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr align="center">
                        <th>Nombre del Evento</th>
                        <th>Cantante</th>
                        <th>Fecha</th>
                        <th>Cartel</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($params['eventos'] as $evento): ?>
                        <tr align="center">
                            <td>
                                <a href="index.php?ctl=verEvento&idEvento=<?php echo $evento['id_evento']; ?>">
                                    <?php echo htmlspecialchars($evento['nomEvento']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($evento['cantante']); ?></td>
                            <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($evento['cartel']); ?>" alt="Cartel del evento" style="max-width:100px; height:auto;">
                            </td>
                            <td>
                                <!-- Si el usuario es de nivel 1 (usuario normal), mostramos el botón de reservar -->
                                <?php if (isset($_SESSION['nivel_usuario']) && $_SESSION['nivel_usuario'] == 1): ?>
                                    <a href="index.php?ctl=Reservar&idEvento=<?php echo $evento['id_evento']; ?>" class="btn btn-success">
                                        Reservar
                                    </a>
                                <?php endif; ?>
                                <!-- Si el usuario es administrador (nivel 2), mostramos el botón de eliminar -->
                                <?php if (isset($_SESSION['nivel_usuario']) && $_SESSION['nivel_usuario'] == 2): ?>
                                    <a href="index.php?ctl=eliminarEvento&idEvento=<?php echo $evento['id_evento']; ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este evento?');">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center">No hay eventos que mostrar.</p>
    <?php endif; ?>
</div>

<?php 
    $contenido = ob_get_clean();
    include 'layout.php';
?>
