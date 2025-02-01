<?php

class Biblioteca extends Modelo {


    public function consultarUsuario($correoUser) {
        $consulta = "SELECT * FROM eventos.usuarios WHERE correoUser=:correoUser ";

        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':correoUser', $correoUser);
        $result->execute();

        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarUsuario($nombre, $apellido, $correoUser, $nombreUser, $contrasenya) {
        $consulta = "INSERT INTO eventos.usuarios (nombre, apellido, correoUser, nombreUser, contrasenya) VALUES (:nombre, :apellido, :correoUser, :nombreUser, :contrasenya)";
        
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->bindParam(':apellido', $apellido);
        $result->bindParam(':correoUser', $correoUser);
        $result->bindParam(':nombreUser', $nombreUser);
        $result->bindParam(':contrasenya', $contrasenya);
        $result->execute();

        return $result;
    }

    public function listarEventos() {
        $consulta = "SELECT * FROM eventos.listaEventos ORDER BY nomEvento ASC";
        $result = $this->conexion->query($consulta);

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarEventosNombre($nomEvento) {
        $consulta = "SELECT * FROM eventos.listaEventos WHERE nomEvento=:nomEvento";
        
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->execute();

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarEventosCantante($nomCantante) {
        $consulta = "SELECT * FROM eventos.listaEventos WHERE nomCantante=:nomCantante";
        
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomCantante', $nomCantante);
        $result->execute();
        
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarEventosSemana() {
        $consulta = "SELECT * FROM eventos.listaEventos 
                            WHERE fecha >= CURDATE() 
                            AND fecha < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                            ORDER BY fecha ASC";
        
        $result = $this->conexion->query($consulta);

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarMisReservas($nombreUsuario) {
        $consulta = "SELECT reservas.*, listaEventos.titulo, listaEventos.fecha
                            FROM reservas
                            INNER JOIN listaEventos ON reservas.event_id = listaEventos.id
                            INNER JOIN usuarios ON reservas.usuario_id = usuarios.id
                            WHERE usuarios.nombreUsuario = :nombreUsuario";
        
        $result = $this->conexion->query($consulta);

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }


    public function insertarEvento($nomEvento, $cantante, $anyo, $cartel) {
        $consulta = "INSERT INTO eventos.listaEventos (nomEvento, cantante, anyo, cartel) VALUES (:nomEvento, :cantante, :anyo, :cartel)";
        
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->bindParam(':cantante', $cantante);
        $result->bindParam(':anyo', $anyo);
        $result->bindParam(':cartel', $cartel);
        $result->execute();        
        
        return $result;
    }











}



?>