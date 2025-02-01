<?php
//incluimos las librerias
require_once __DIR__ . '/../app/libs/Config.php';
require_once __DIR__ . '/../app/libs/bGeneral.php';
require_once __DIR__ . '/../app/libs/bSeguridad.php';
require_once __DIR__ . '/../app/modelo/classModelo.php';
require_once __DIR__ . '/../app/modelo/classBiblioteca.php';
require_once __DIR__ . '/../app/controlador/Controller.php';
//iniciamos sesión
session_start(); 

//comprobamos si el usuario está logueado, si no lo está le asignamos el 0.
if (!isset($_SESSION['nivel_usuario'])) {
    $_SESSION['nivel_usuario'] = 0;
}

/**
 * Acciones que ejecuta la página junto con el nivel de usuario
 */

$map = array(
    'home' => array('controller' => 'Controller', 'action' => 'home', 'nivel_usuario' => 0),
    'inicio' => array('controller' => 'Controller', 'action' => 'inicio', 'nivel_usuario' => 0),
    'salir' => array('controller' => 'Controller', 'action' => 'salir', 'nivel_usuario' => 1),
    'error' => array('controller' => 'Controller', 'action' => 'error', 'nivel_usuario' => 0),
    'iniciarSesion' => array('controller' => 'Controller', 'action' => 'iniciarSesion', 'nivel_usuario' => 0),
    'registro' => array('controller' => 'Controller', 'action' => 'registro', 'nivel_usuario' => 0),
    'ListarEventos' => array('controller' => 'Controller', 'action' => 'ListarEventos', 'nivel_usuario' => 0),
    'EventosFavoritos' => array('controller' => 'Controller', 'action' => 'EventosFavoritos', 'nivel_usuario' => 1),
    'BuscarPorCantante' => array('controller' => 'Controller', 'action' => 'BuscarPorCantante', 'nivel_usuario' => 0),
    'ModificarPerfil' => array('controller' => 'Controller', 'action' => 'ModificarPerfil', 'nivel_usuario' => 1),
    'MisReservas' => array('controller' => 'Controller', 'action' => 'MisReservas', 'nivel_usuario' => 1),
    'EventosSemana' => array('controller' => 'Controller', 'action' => 'EventosSemana', 'nivel_usuario' => 0),
    'Reservar' => array('controller' => 'Controller', 'action' => 'Reservar', 'nivel_usuario' => 1),
    'insertarEvento' => array('controller' => 'Controller', 'action' => 'insertarEvento', 'nivel_usuario' => 2)
);



// Comprobamos que la ruta es correcta
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        header('Status: 404 Not Found');
        echo '<html><body><h1>Error 404: No existe la ruta <i>' .
            $_GET['ctl'] . '</p></body></html>';
        exit;
    }
} else {
    $ruta = 'home';
}


$controlador = $map[$ruta];

//Comprobamos si el metodo y la acción relacionada con el valor de ctl existe
if (method_exists($controlador['controller'], $controlador['action'])) {
    if ($controlador['nivel_usuario'] <= $_SESSION['nivel_usuario']) {
        call_user_func(array(
            new $controlador['controller'],
            $controlador['action']
        ));
    }else{
        call_user_func(array(
            new $controlador['controller'],
            'inicio'
        )); 
    }
} else {
    header('Status: 404 Not Found');
    echo '<html><body><h1>Error 404: El controlador <i>' .
        $controlador['controller'] .
        '->' .
        $controlador['action'] .
        '</i> no existe</h1></body></html>';
    console_log("entrarErrorInicio");
}





















?>