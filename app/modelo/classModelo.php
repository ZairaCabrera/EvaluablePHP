<?php

class Modelo extends PDO {

    protected $conexion;

    public function __construct() {
        $this->conexion = new PDO('mysql:host=' . Config::$mvc_bd_hostname . ';dbname=' . Config::$mvc_bd_nombre . '', Config::$mvc_bd_usuario, Config::$mvc_bd_clave);
        
        $this->conexion->exec("set names utf8");
        
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    //he creado esta función para poder utilizarla cuando creo el administrador 
    public function getConexion() {
        return $this->conexion;
    }

}


?>