<?php ob_start(); ?>

<div class="container my-4">
    <h2 class="text-center">Mis Reservas</h2>
    
    <!-- Mostrar mensaje si existe -->
    <?php if (isset($params['mensaje'])): ?>
        <div class="alert alert-info text-center">
            <?php echo htmlspecialchars($params['mensaje']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($params['eventos']) && count($params['eventos']) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr align="center">
                        <th>Nombre del Evento</th>
                        <th>Fecha</th>
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
                            <td><?php echo htmlspecialchars($evento['fecha']); ?></td>
                            <td>
                                <!-- Botón para ver el evento -->
                                <a href="index.php?ctl=verEvento&idEvento=<?php echo $evento['id_evento']; ?>" class="btn btn-info">
                                    Ver Evento
                                </a>
                                <!-- Botón para eliminar la reserva -->
                                <a href="index.php?ctl=eliminarReserva&idEvento=<?php echo $evento['id_evento']; ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta reserva?');">
                                    Eliminar Reserva
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
    <?php endif; ?>
</div>

<?php 
    $contenido = ob_get_clean();
    include 'layout.php';
?>
