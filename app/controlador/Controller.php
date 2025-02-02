<?php
// Declaramos los namespaces para PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Controller
{
    /**
     * Función que asigna el nivel de usuario y las webs que se le asocian:
     * menuInvitado: para usuarios no registrados. (para todos = General)
     * menuUser: para usuarios registrados. nivel 1.
     * menuAdmin: para el administrador (por ejemplo, para insertar eventos) nivel 2.
     */
    private function cargaMenu()
    {
        if ($_SESSION['nivel_usuario'] == 0) {
            return 'menuInvitado.php';
        } else if ($_SESSION['nivel_usuario'] == 1) {
            return 'menuUser.php';
        } else if ($_SESSION['nivel_usuario'] == 2) {
            return 'menuAdmin.php';
        }
    }

    public function home()
    {
        $params = array(
            'mensaje' => 'Portal de Eventos Municipales',
            'mensaje2' => 'No te pierdas ningún evento',
            'fecha' => date('d-m-Y')
        );
        $menu = 'menuHome.php';

        if ($_SESSION['nivel_usuario'] > 0) {
            header("location:index.php?ctl=inicio");
            exit;
        }
        require __DIR__ . '/../../web/templates/inicio.php';
    }

    public function inicio()
    {
        $params = array(
            'mensaje' => 'Portal de Eventos Municipales',
            'mensaje2' => 'No te pierdas ningún evento',
            'fecha' => date('d-m-Y')
        );
        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/inicio.php';
    }

    // Función para cerrar la sesión y volver a la página de inicio.
    public function salir()
    {
        session_destroy();
        header("location:index.php?ctl=home");
        exit;
    }

    public function error()
    {
        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/error.php';
    }

    public function iniciarSesion()
    {
        try {
            $params = array(
                'nombreUsuario' => '',
                'contrasenya'   => ''
            );
            $menu = $this->cargaMenu();

            // Si el usuario ya está logueado, redirige a inicio.
            if ($_SESSION['nivel_usuario'] > 0) {
                header("location:index.php?ctl=inicio");
                exit;
            }

            if (isset($_POST['bIniciarSesion'])) {
                $nombreUsuario = recoge('nombreUsuario');
                $contrasenya   = recoge('contrasenya');

                // Crear el modelo y consultar el usuario en la base de datos.
                $m = new Eventos();
                $usuario = $m->consultarUsuario($nombreUsuario);

                if ($usuario) {
                    // Verificar la contraseña 
                    if (comprobarhash($contrasenya, $usuario['contraseña'])) {
                        // Asignar a la sesión los datos del usuario.
                        $_SESSION['idUser'] = $usuario['id_usu'];
                        $_SESSION['nombreUsuario'] = $usuario['nombreUsuario'];
                        $_SESSION['nivel_usuario'] = $usuario['nivel_usuario'];
                        $_SESSION['foto'] = $usuario['foto']; //para que guarde la foto si el usuario ha modificado su foto
                        header('Location: index.php?ctl=inicio');
                        exit;
                    } else {
                        // Contraseña incorrecta.
                        $params['mensaje'] = 'Contraseña incorrecta. Revisa el formulario.';
                    }
                } else {
                    // Usuario no encontrado.
                    $params['mensaje'] = 'Usuario no encontrado. Revisa el formulario.';
                }
            }
        } catch (Exception $e) {
            error_log("Excepción en iniciarSesion: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
            exit;
        } catch (Error $e) {
            error_log("Error en iniciarSesion: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
            exit;
        }
        require __DIR__ . '/../../web/templates/formInicioSesion.php';
    }

    public function registro()
{
    $menu = $this->cargaMenu();
    if ($_SESSION['nivel_usuario'] > 0) {
        header("location:index.php?ctl=inicio");
        exit;
    }

    // Inicializamos los parámetros para el formulario
    $params = array(
        'nombre'        => '',
        'apellido'      => '',
        'nombreUsuario' => '',
        'correoUser'    => '',
        'contrasenya'   => '',
    );
    $errores = array();

    if (isset($_POST['bRegistro'])) {
        // Recoger datos de texto
        $nombre        = recoge('nombre');
        $apellido      = recoge('apellido');
        $nombreUsuario = recoge('nombreUsuario');
        $correoUser    = recoge('correoUser');
        $contrasenya   = recoge('contrasenya');

        // Validamos los datos introducidos
        cTexto($nombre, "nombre", $errores);
        cTexto($apellido, "apellido", $errores);
        cCorreo($correoUser, "correoUser", $errores);
        cUser($contrasenya, "contrasenya", $errores);
        cUser($nombreUsuario, "nombreUsuario", $errores);

        // Procesar la foto:
        // Si no se ha subido ningún archivo o hay error (error code 4 = NO FILE), se asigna la imagen predeterminada.
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] == UPLOAD_ERR_NO_FILE) {
            $foto = 'img/default_profile.png';
        } else {
            // implementar el proceso de subida de imagen (validar tipo, tamaño, etc.)
            $directorioDestino = __DIR__ . '/../../web/img/';
            $nombreArchivo = basename($_FILES['foto']['name']);
            $rutaDestino = $directorioDestino . $nombreArchivo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                $foto = 'img/' . $nombreArchivo;
            } else {
                $foto = 'img/default_profile.png';
            }
        }

        if (empty($errores)) {
            try {
                $m = new Eventos();

                // Comprobamos si ya existe el usuario
                if ($m->consultarUsuario($nombreUsuario)) {
                    $params = array(
                        'nombre'        => $nombre,
                        'apellido'      => $apellido,
                        'nombreUsuario' => $nombreUsuario,
                        'correoUser'    => $correoUser,
                        'contrasenya'   => $contrasenya
                    );
                    $params['mensaje'] = 'El usuario ya existe. Por favor, elige otro nombre de usuario.';
                } else {
                    // Inserción de usuario: incluimos la foto en la consulta
                    if ($m->insertarUsuario($nombre, $apellido, $nombreUsuario, $correoUser, encriptar($contrasenya), $foto)) {
                        header('Location: index.php?ctl=iniciarSesion');
                        exit;
                    } else {
                        $params = array(
                            'nombre'        => $nombre,
                            'apellido'      => $apellido,
                            'nombreUsuario' => $nombreUsuario,
                            'correoUser'    => $correoUser,
                            'contrasenya'   => $contrasenya
                        );
                        $params['mensaje'] = 'No se ha podido insertar el usuario. Revisa el formulario.';
                    }
                }
            } catch (Exception $e) {
                error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
                header('Location: index.php?ctl=error');
                exit;
            } catch (Error $e) {
                error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
                header('Location: index.php?ctl=error');
                exit;
            }
        } else {
            $params = array(
                'nombre'        => $nombre,
                'apellido'      => $apellido,
                'nombreUsuario' => $nombreUsuario,
                'correoUser'    => $correoUser,
                'contrasenya'   => $contrasenya
            );
            $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
        }
    }
    require __DIR__ . '/../../web/templates/formRegistro.php';
}

public function ModificarPerfil() {
    // Solo los usuarios autenticados pueden modificar su perfil.
    if (!isset($_SESSION['idUser'])) {
        header("Location: index.php?ctl=iniciarSesion");
        exit;
    }
    
    $params = array('mensaje' => '');
    
    // Si se envía el formulario para modificar el perfil
    if (isset($_POST['bModificarPerfil'])) {
        $errores = array();
        $extensionesValidas = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 500 * 1024; // 500 KB
        $directorio = __DIR__ . '/../../web/img';
        
        //utilizamos cFile() para procesar la subida de la foto
        $rutaArchivo = cFile("foto", $errores, $extensionesValidas, $directorio, $max_file_size, false);
        
        if ($rutaArchivo === false) {
            // Si no se ha subido ninguna imagen o hubo error, se asigna la imagen predeterminada
            $foto = 'img/default_profile.png';
        } else {
            // Obtenemos la ruta relativa: usamos basename para extraer el nombre del archivo
            $foto = 'img/' . basename($rutaArchivo);
        }
        
        // Actualizamos la foto de perfil en la base de datos y en la sesión
        $m = new Eventos();
        if ($m->actualizarFotoPerfil($_SESSION['idUser'], $foto)) {
            $_SESSION['foto'] = $foto;
            $params['mensaje'] = "Foto de perfil actualizada correctamente.";
        } else {
            $params['mensaje'] = "Error al actualizar la foto de perfil.";
        }
    }
    
    $menu = $this->cargaMenu();
    require __DIR__ . '/../../web/templates/modificarPerfil.php';
}


    public function listarEventos()
    {
        try {
            $m = new Eventos();
            $params = array(
                'eventos' => $m->listarEventos(),
            );
            if (!$params['eventos'])
                $params['mensaje'] = "No hay eventos que mostrar.";
        } catch (Exception $e) {
            error_log("Excepción en listarEventos: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log("Error en listarEventos: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/listarEventos.php';
    }

    public function buscarPorEvento()
    {
        try {
            $params = array(
                'nomEvento' => '',
                'resultado' => array(),
                'eventos'   => array()
            );
            $m = new Eventos();
            if (isset($_POST['buscarPorEvento'])) {
                $nomEvento = recoge("nomEvento");
                $params['nomEvento'] = $nomEvento;
                $params['eventos'] = $m->buscarEventosNombre($nomEvento);
                if (!$params['eventos']) {
                    $params['mensaje'] = 'No hay eventos con este nombre: ' . $nomEvento . '.';
                }
            }
        } catch (Exception $e) {
            error_log("Excepción en buscarPorEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log("Error en buscarPorEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/buscarPorEvento.php';
    }

    public function buscarPorCantante()
    {
        try {
            $params = array(
                'nomCantante' => '',
                'resultado'   => array(),
                'eventos'     => array()
            );
            $m = new Eventos();
            if (isset($_POST['buscarPorCantante'])) {
                $nomCantante = recoge("nomCantante");
                $params['nomCantante'] = $nomCantante;
                $params['eventos'] = $m->buscarEventosCantante($nomCantante);
                if (!$params['eventos']) {
                    $params['mensaje'] = 'No hay eventos de ' . $nomCantante . ' en este momento.';
                }
            }
        } catch (Exception $e) {
            error_log("Excepción en buscarPorCantante: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log("Error en buscarPorCantante: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/buscarPorCantante.php';
    }

    public function verEvento()
    {
        try {
            if (!isset($_GET['idEvento'])) {
                $params['mensaje'] = 'No hay eventos que mostrar.';
            }
            $idEvento = recoge('idEvento');
            $m = new Eventos();
            $params['eventos'] = $m->verEventoE($idEvento);
            if (!$params['eventos']) {
                $params['mensaje'] = 'No hay eventos que mostrar.';
            }
        } catch (Exception $e) {
            error_log("Excepción en verEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log("Error en verEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/verEvento.php';
    }

    public function misReservas($nombreUsuario = null)
{

    if ($nombreUsuario === null && isset($_SESSION['nombreUsuario'])) {
        $nombreUsuario = $_SESSION['nombreUsuario'];
    }
    
    try {
        $m = new Eventos();
        $params = array(
            'eventos' => $m->buscarMisReservas($nombreUsuario),
        );
        if (!$params['eventos'])
            $params['mensaje'] = 'No hay eventos reservados.';
    } catch (Exception $e) {
        error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
        header('Location: index.php?ctl=error');
        exit;
    } catch (Error $e) {
        error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
        header('Location: index.php?ctl=error');
        exit;
    }

    $menu = $this->cargaMenu();
    require __DIR__ . '/../../web/templates/misReservas.php';
}

public function eliminarReserva() {
    if (!isset($_GET['idEvento'])) {
        header("Location: index.php?ctl=misReservas");
        exit;
    }
    $idEvento = recoge('idEvento'); // Sanitizamos el parámetro

    // Verificar que el usuario esté logueado
    if (!isset($_SESSION['idUser'])) {
        header("Location: index.php?ctl=iniciarSesion");
        exit;
    }
    $idUsuario = $_SESSION['idUser'];

    try {
        $m = new Eventos();
        if ($m->eliminarReserva($idUsuario, $idEvento)) {
            header("Location: index.php?ctl=MisReservas&nombreUsuario=" . urlencode($_SESSION['nombreUsuario']));
            exit;
        } else {
            header("Location: index.php?ctl=error");
            exit;
        }
    } catch (Exception $e) {
        error_log("Excepción en eliminarReserva: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
        header("Location: index.php?ctl=error");
        exit;
    } catch (Error $e) {
        error_log("Error en eliminarReserva: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
        header("Location: index.php?ctl=error");
        exit;
    }
}


public function Reservar() {
    if (!isset($_GET['idEvento'])) {
        header("Location: index.php?ctl=ListarEventos");
        exit;
    }
    $idEvento = recoge('idEvento'); // Sanitizamos el parámetro.

    // Verificamos que el usuario esté logueado.
    if (!isset($_SESSION['idUser'])) {
        header("Location: index.php?ctl=iniciarSesion");
        exit;
    }
    $idUsuario = $_SESSION['idUser'];

    try {
        $m = new Eventos();
        if ($m->insertarReserva($idUsuario, $idEvento)) {
            header("Location: index.php?ctl=MisReservas&nombreUsuario=" . urlencode($_SESSION['nombreUsuario']));
            exit;
        } else {
            header("Location: index.php?ctl=error");
            exit;
        }
    } catch (Exception $e) {
        error_log("Excepción en Reservar: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
        header("Location: index.php?ctl=error");
        exit;
    } catch (Error $e) {
        error_log("Error en Reservar: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
        header("Location: index.php?ctl=error");
        exit;
    }
}


public function insertarEvento()
    {
        try {
            $params = array(
                'nomEvento' => '',
                'cantante'  => '',
                'fecha'     => '',
                'cartel'    => ''
            );
            $errores = array();
            if (isset($_POST['bInsertarL'])) {
                // Recoger y sanitizar datos de texto
                $nomEvento = recoge('nomEvento');
                $cantante  = recoge('cantante');
                $fecha     = recoge('fecha');
                
                // Procesar la subida del archivo para el cartel
                if (isset($_FILES['cartel']) && $_FILES['cartel']['error'] == UPLOAD_ERR_OK) {
                    $tempName = $_FILES['cartel']['tmp_name'];
                    $originalName = basename($_FILES['cartel']['name']);
                    $destination = __DIR__ . '/../../web/img/' . $originalName;
                    if (move_uploaded_file($tempName, $destination)) {
                        // Guardar la ruta relativa (desde la carpeta web)
                        $cartel = 'img/' . $originalName;
                    } else {
                        $cartel = 'img/default_profile.png';
                    }
                } else {
                    $cartel = 'img/default_profile.png';
                }
                
                // Validación de los campos de texto para nomEvento y cantante
                cTexto($nomEvento, "nomEvento", $errores);
                cTexto($cantante, "cantante", $errores);
                
                // Validación de la fecha usando unixFechaAAAAMMDD()
                $unixFecha = unixFechaAAAAMMDD($fecha, 'fecha', $errores);
                if ($unixFecha !== false) {
                    // Convertir el timestamp a formato AAAA-MM-DD para insertar en la base de datos
                    $fecha = date("Y-m-d", $unixFecha);
                }
                
                if (empty($errores)) {
                    $m = new Eventos();
                    if ($m->insertarEvento($nomEvento, $cantante, $fecha, $cartel)) {
                        header('Location: index.php?ctl=ListarEventos');
                        exit;
                    } else {
                        $params = array(
                            'nomEvento' => $nomEvento,
                            'cantante'  => $cantante,
                            'fecha'     => $fecha,
                            'cartel'    => $cartel
                        );
                        $params['mensaje'] = 'No se ha podido insertar el evento. Revisa el formulario.';
                    }
                } else {
                    $params = array(
                        'nomEvento' => $nomEvento,
                        'cantante'  => $cantante,
                        'fecha'     => $fecha,
                        'cartel'    => $cartel
                    );
                    $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
            exit;
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
            exit;
        }
        
        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/formInsertarEvento.php';
    }

    public function eliminarEvento() {
        if (!isset($_GET['idEvento'])) {
            header("Location: index.php?ctl=ListarEventos");
            exit;
        }
        $idEvento = recoge('idEvento'); // Sanitizamos el parámetro
    
        // Verificar que el usuario esté logueado y sea administrador (nivel 2)
        if (!isset($_SESSION['idUser']) || $_SESSION['nivel_usuario'] != 2) {
            header("Location: index.php?ctl=iniciarSesion");
            exit;
        }
        
        try {
            $m = new Eventos();
            if ($m->eliminarEvento($idEvento)) {
                header("Location: index.php?ctl=ListarEventos");
                exit;
            } else {
                header("Location: index.php?ctl=error");
                exit;
            }
        } catch (Exception $e) {
            error_log("Excepción en eliminarEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header("Location: index.php?ctl=error");
            exit;
        } catch (Error $e) {
            error_log("Error en eliminarEvento: " . $e->getMessage() . " - " . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header("Location: index.php?ctl=error");
            exit;
        }
    }
    

}
