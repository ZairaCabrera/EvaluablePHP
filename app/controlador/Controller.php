<?php

class Controller
{

    /**
     * Función que asigna el nivel de usuaruo y las webs que se le asocian
     * en menú invitado lo verán aquellas personas que no estén registradas
     * menuUser es la página que verán las personas que están registradas
     * menuAdmin es la página que solo la verá el administrador (insertar eventos).
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

    //Funcion que sale del login y vuelve a la página de inicio
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
                'correo' => '',
                'contrasenya' => ''
            );
            $menu = $this->cargaMenu();

            if ($_SESSION['nivel_usuario'] > 0) {
                header("location:index.php?ctl=inicio");
            }
            if (isset($_POST['bIniciarSesion'])) { // Nombre del boton del formulario
                $correoUser = recoge('correoUser');
                $contrasenya = recoge('contrasenya');

                // Comprobar campos formulario. Aqui va la validación con las funciones de bGeneral   
                if (cCorreo($correoUser, "correoUser", $params)) {
                    // Si no ha habido problema creo modelo y hago consulta                    
                    $m = new Eventos();
                    if ($usuario = $m->consultarUsuario($correoUser)) {
                        // Compruebo si el password es correcto
                        if (comprobarhash($contrasenya, $usuario['contrasenya'])) {
                            // Obtenemos el resto de datos

                            $_SESSION['idUser'] = $usuario['idUser'];
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
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
            // Sanitizamos los datos introducidos desde formulario
            $nombre = recoge('nombre');
            $apellido = recoge('apellido');
            $nombreUsuario = recoge('nombreUsuario');
            $correoUser = recoge('correoUser');
            $contrasenya = recoge('contrasenya');

            // Validamos los datos introducimos desde formulario         
            cTexto($nombre, "nombre", $errores);
            cTexto($apellido, "apellido", $errores);
            cCorreo($correoUser, "correoUser", $errores);
            cUser($contrasenya, "contrasenya", $errores);
            cUser($nombreUsuario, "nombreUsuario", $errores);

            if (empty($errores)) {
                // Si no ha habido problema creo modelo y hago inserción     
                try {

                    $m = new Eventos();
                    if ($m->insertarUsuario($nombre, $apellido, $correoUser, $nombreUsuario, encriptar($contrasenya))) {

                        header('Location: index.php?ctl=iniciarSesion');
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
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
                    header('Location: index.php?ctl=error');
                } catch (Error $e) {
                    error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/mostrarEventos.php';
    }


    public function buscarPorEvento(){
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
                    $params['mensaje'] = 'No hay eventos con este nombre: '.$nomEvento.'.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
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
                    $params['mensaje'] = 'No hay eventos de '.$nomCantante.' en este momento.';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
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
                $params['mensaje'] = "No hay eventos con ese nombre.";
        } catch (Exception $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/buscarPorEditorial.php';
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/buscarPorEditorial.php';
    }



    public function insertarEvento()
    {
        try {
            $params = array(
                'nomEvento' => '',
                'cantante' => '',
                'anyo' => '',
                'cartel' => ''
            );
            $errores = array();
            if (isset($_POST['bInsertarL'])) {
                $nomEvento = recoge('nomEvento');
                $cantante = recoge('cantante');
                $anyo = recoge('anyo');
                /**
                 * ¿Cómo se recogeria una url?
                 */
                // Comprobar campos formulario. Aqui va la validación con las funciones de bGeneral
                cTexto($nomEvento, "nomEvento", $errores);
                cTexto($cantante, "cantante", $errores);
                cTexto($anyo, "anyo", $errores);

                if (empty($errores)) {
                    // Si no ha habido problema creo modelo y hago inserción
                    $m = new Eventos();
                    if ($m->insertarEvento($nomEvento, $cantante, $anyo, $cartel)) {
                        header('Location: index.php?ctl=listarEventos');
                    } else {
                        $params = array(
                            'nomEvento' => $nomEvento,
                            'cantante' => $cantante,
                            'anyo' => $anyo,
                            'cartel' => $cartel
                        );
                        $params['mensaje'] = 'No se ha podido insertar el alimento. Revisa el formulario.';
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
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logExceptio.txt");
            header('Location: index.php?ctl=error');
        } catch (Error $e) {
            error_log($e->getMessage() . microtime() . PHP_EOL, 3, "../app/log/logError.txt");
            header('Location: index.php?ctl=error');
        }

        $menu = $this->cargaMenu();

        require __DIR__ . '/../../web/templates/formInsertarL.php';
    }
}
