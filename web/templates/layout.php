<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Portal de Eventos Municipales</title>
    <link rel="stylesheet" type="text/css" href="<?php echo 'css/' . Config::$mvc_vis_css; ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Estilos para el bloque del perfil */
        .user-profile {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            align-items: center;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="container">
            <div class="row position-relative">
                <div class="col-md-11">
                    <h1 class="text-center"><b>No te pierdas ningún evento. ¡Regístrate y reserva!</b></h1>
                </div>
                <div class="col-md-1">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bloque de perfil de usuario  -->
    <?php if (isset($_SESSION['nombreUsuario']) && $_SESSION['nivel_usuario'] > 0): ?>
    <div class="user-profile">
        <?php
            $fotoPerfil = isset($_SESSION['foto']) && !empty($_SESSION['foto'])
                          ? $_SESSION['foto']
                          : 'img/default_profile.png';
        ?>
        <a href="index.php?ctl=ModificarPerfil" class="btn btn-secondary">
            Modificar Foto
        </a>
        <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de perfil">
        <span>Hola, <?php echo htmlspecialchars($_SESSION['nombreUsuario']); ?>!</span>
    </div>
    <?php endif; ?>
    
    <!-- Menú dinámico según nivel  -->
    <?php
        if (!isset($menu)) {
            $menu = 'menuInvitado.php';
        }
        include $menu;
    ?>
    
    <!-- Contenido principal -->
    <div class="container-fluid">
        <div class="container">
            <div id="contenido">
                <?php echo $contenido; ?>
            </div>
        </div>
    </div>
    
    <!-- Pie de página -->
    <div class="container-fluid pie p-2 my-5">
        <div class="footer">
            <div class="container text-center">
                <p>
                    Contacto: 
                    <a href="mailto:info@eventosmunicipales.com">info@eventosmunicipales.com</a> | Tel: 123-456-7890
                </p>
                <p>Dirección: Calle Falsa 123, Ciudad Ejemplo, País</p>
                <p>Síguenos en:
                    <a href="#" class="social-link">Facebook</a>,
                    <a href="#" class="social-link">Twitter</a>,
                    <a href="#" class="social-link">Instagram</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
