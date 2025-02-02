<?php
// Creamos el admin de la web eventos 

// Librerías 
require_once __DIR__ . '/../app/libs/Config.php';
require_once __DIR__ . '/../app/libs/bGeneral.php';
require_once __DIR__ . '/../app/libs/bSeguridad.php';
require_once __DIR__ . '/../app/modelo/classModelo.php';
require_once __DIR__ . '/../app/modelo/classEventos.php';

try {
    // Instancia del modelo Eventos
    $m = new Eventos();
    
    // Comprobamos si ya existe el usuario admin usando el método existente
    $adminUsername = 'admin';
    $admin = $m->consultarUsuario($adminUsername);
    
    if ($admin) {
        echo "El usuario administrador ya existe.";
    } else {
        // Datos para el admin
        $nombre     = 'Admin';
        $apellido   = 'Admin';
        $nombreUser = 'admin';
        $correoUser = 'admin@example.com';
        $contrasenya = encriptar('0000'); 
        $foto = 'img/default_profile.png'; // Foto predeterminada
        
        // Inserta el usuario 
        $resultado = $m->insertarUsuario($nombre, $apellido, $nombreUser, $correoUser, $contrasenya, $foto);
        
        if ($resultado) {
            // Obtenemos la conexión mediante el método getConexion()
            $conexion = $m->getConexion();
            
            // Actualizamos el campo nivel_usuario a 2 para el administrador
            $sqlUpdate = "UPDATE eventos.usuarios SET nivel_usuario = 2 WHERE nombreUsuario = :nombreUsuario";
            $stmt = $conexion->prepare($sqlUpdate);
            $stmt->bindParam(':nombreUsuario', $adminUsername);
            $stmt->execute();
            
            echo "Administrador creado correctamente.";
        } else {
            echo "Error al insertar el usuario administrador.";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
