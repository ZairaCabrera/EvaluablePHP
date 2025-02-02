<?php
//declaramos aquí los namespace para controladores

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Controller
{
    /**
     * Función que asigna el nivel de usuario y las webs que se le asocian:
     * menuInvitado: para usuarios no registrados.
     * menuUser: para usuarios registrados.
     * menuAdmin: para el administrador (por ejemplo, para insertar eventos).
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
                'contrasenya' => ''
            );
            $menu = $this->cargaMenu();

            if ($_SESSION['nivel_usuario'] > 0) {
                header("location:index.php?ctl=inicio");
            }
            if (isset($_POST['bIniciarSesion'])) { 
                $nombreUsuario = recoge('nombreUsuario');
                $contrasenya = recoge('contrasenya');

                // Validación del campo nombreUsuario
                if (cUser($nombreUsuario, "nombreUsuario", $errores)) {
                    // Si no hay problema, creo el modelo y consulto el usuario.
                    $m = new Eventos();
                    if ($usuario = $m->consultarUsuario($nombreUsuario)) {
                        
                        // Compruebo si el password es correcto.
                        if (comprobarhash($contrasenya, $usuario['contraseña'])) {

                            // Asigno a la sesión los datos del usuario.
                            $_SESSION['idUser'] = $usuario['id_usu'];
                            $_SESSION['nombreUsuario'] = $usuario['nombreUsuario'];
                            $_SESSION['nivel_usuario'] = $usuario['nivel_usuario']; 

                            header('Location: index.php?ctl=inicio');
                        }
                    } else {
                        $params = array(
                            'correoUser' => $correoUser,
                            'contrasenya' => $contrasenya
                        );
                        $params['mensaje'] = 'No se ha podido iniciar sesión. Revisa el formulario.';
                    }
                } else {
                    $params = array(
                        'correoUser' => $correoUser,
                        'contrasenya' => $contrasenya
                    );
                    $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }
        require __DIR__ . '/../../web/templates/formInicioSesion.php';
    }

    public function registro()
{
    $menu = $this->cargaMenu();
    if ($_SESSION['nivel_usuario'] > 0) {
        header("location:index.php?ctl=inicio");
    }

    $params = array(
        'nombre' => '',
        'apellido' => '',
        'nombreUsuario' => '',
        'correoUser' => '',
        'contrasenya' => '',
    );

    $errores = array();

    if (isset($_POST['bRegistro'])) {
        // Sanitizamos los datos introducidos desde el formulario
        $nombre = recoge('nombre');
        $apellido = recoge('apellido');
        $nombreUsuario = recoge('nombreUsuario');
        $correoUser = recoge('correoUser');
        $contrasenya = recoge('contrasenya');

        // Validamos los datos introducidos
        cTexto($nombre, "nombre", $errores);
        cTexto($apellido, "apellido", $errores);
        cCorreo($correoUser, "correoUser", $errores);
        cUser($contrasenya, "contrasenya", $errores);
        cUser($nombreUsuario, "nombreUsuario", $errores);

        if (empty($errores)) {
            try {
                $m = new Eventos();
                // Inserción de usuario (el orden de los parámetros es: nombre, apellido, nombreUsuario, correoUser, contraseña)
                if ($m->insertarUsuario($nombre, $apellido, $nombreUsuario, $correoUser, encriptar($contrasenya))) {

                    // Obtener el ID del usuario insertado (asegúrate de que 'conexion' sea accesible o implementa un método en el modelo)
                    $idUsuario = $m->conexion->lastInsertId();

                    //insertamos la libreria PHPMailer
                    require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
                    require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
                    require_once __DIR__ . '/../libs/PHPMailer/Exception.php';

                    $mail = new PHPMailer(true);
                    try {
                        // Configuración del servidor SMTP para Gmail
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'eventosmunicipalesvalencia@gmail.com';// cuenta de Gmail
                        $mail->Password   = 'hpyw xtoq bkbe aykt'; // Contraseña 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Puedes usar ENCRYPTION_STARTTLS y puerto 587 si lo prefieres
                        $mail->Port       = 465;                              // Cambia a 587 si usas TLS

                        // Configurar remitente y destinatario
                        $mail->setFrom('eventosmunicipalesvalencia@gmail.com', 'Eventos Municipales');
                        $mail->addAddress($correoUser, $nombreUsuario);

                        // Contenido del correo
                        $mail->isHTML(true);
                        $mail->Subject = 'Confirma tu registro en Eventos Municipales';
                        
                        // Generamos un enlace de confirmación; deberás implementar la acción 'confirmar'
                        $linkConfirmacion = 'http://localhost/dwes/EvaluableZai/web/index.php?ctl=confirmar&idUser=' . $idUsuario;
                        $mail->Body    = 'Hola ' . $nombreUsuario . ', por favor confirma tu cuenta haciendo clic en el siguiente enlace: <a href="' . $linkConfirmacion . '">Confirmar Cuenta</a>';
                        $mail->AltBody = 'Hola ' . $nombreUsuario . ', por favor confirma tu cuenta visitando: ' . $linkConfirmacion;

                        $mail->send();
                    } catch (Exception $e) {
                        // Registra el error pero continúa el proceso
                        error_log("Error enviando correo: " . $mail->ErrorInfo);
                    }

                    header('Location: index.php?ctl=iniciarSesion');
                    exit;
                } else {
                    $params = array(
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'nombreUsuario' => $nombreUsuario,
                        'correoUser' => $correoUser,
                        'contrasenya' => $contrasenya
                    );
                    $params['mensaje'] = 'No se ha podido insertar el usuario. Revisa el formulario.';
                }
            } catch (Exception $e) {
                error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
                header('Location: index.php?ctl=error');
            } catch (Error $e) {
                error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
                header('Location: index.php?ctl=error');
            }
        } else {
            $params = array(
                'nombre' => $nombre,
                'apellido' => $apellido,
                'nombreUsuario' => $nombreUsuario,
                'correoUser' => $correoUser,
                'contrasenya' => $contrasenya
            );
            $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario.';
        }
    }
    require __DIR__ . '/../../web/templates/formRegistro.php';
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/mostrarEventos.php';
    }

    public function buscarPorEvento()
    {
        try {
            $params = array(
                'nomEvento' => '',
                'resultado' => array(),
                'eventos' => array()
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/buscarPorTitulo.php';
    }

    public function buscarPorCantante()
    {
        try {
            $params = array(
                'nomCantante' => '',
                'resultado' => array(),
                'eventos' => array()
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/buscarPorTitulo.php';
    }

    public function eventosSemana()
    {
        try {
            $m = new Eventos();
            $params = array(
                'eventos' => $m->buscarEventosSemana(),
            );
            if (!$params['eventos'])
                $params['mensaje'] = "No hay eventos en esta semana.";
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/buscarPorEditorial.php';
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        // He cambiado el template de verLibro.php a verEvento.php para que se muestre correctamente el detalle del evento.
        require __DIR__ . '/../../web/templates/verEvento.php';
    }

    public function misReservas($nombreUsuario)
    {
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
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        // He cambiado el template de buscarPorEditorial.php a misReservas.php para que se muestre correctamente la información de reservas.
        require __DIR__ . '/../../web/templates/misReservas.php';
    }

    public function insertarEvento()
    {
        try {
            $params = array(
                'nomEvento' => '',
                'cantante' => '',
                'fecha' => '',
                'cartel' => ''
            );
            $errores = array();
            if (isset($_POST['bInsertarL'])) {
                $nomEvento = recoge('nomEvento');
                $cantante = recoge('cantante');
                $fecha = recoge('fecha');
                // Se podría recoger la URL del cartel si se envía desde el formulario, por ejemplo:
                $cartel = recoge('cartel');
                // Validación de los campos del formulario
                cTexto($nomEvento, "nomEvento", $errores);
                cTexto($cantante, "cantante", $errores);
                cTexto($fecha, "fecha", $errores);

                if (empty($errores)) {
                    $m = new Eventos();
                    if ($m->insertarEvento($nomEvento, $cantante, $fecha, $cartel)) {
                        header('Location: index.php?ctl=listarEventos');
                    } else {
                        $params = array(
                            'nomEvento' => $nomEvento,
                            'cantante' => $cantante,
                            'anyo' => $anyo,
                            'cartel' => $cartel
                        );
                        $params['mensaje'] = 'No se ha podido insertar el evento. Revisa el formulario.';
                    }
                } else {
                    $params = array(
                        'nomEvento' => $nomEvento,
                        'cantante' => $cantante,
                        'anyo' => $anyo,
                        'cartel' => $cartel
                    );
                    $params['mensaje'] = 'Hay datos que no son correctos. Revisa el formulario';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();
        require __DIR__ . '/../../web/templates/formInsertarL.php';
    }
}
