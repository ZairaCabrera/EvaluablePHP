<?php

class Eventos extends Modelo {

    public function consultarUsuario($nombreUsuario) {
        // Se busca en la tabla "usuarios" usando la columna "correo"
        $consulta = "SELECT * FROM eventos.usuarios WHERE nombreUsuario = :nombreUsuario";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombreUsuario', $nombreUsuario);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function insertarUsuario($nombre, $apellido, $correoUser, $nombreUser, $contrasenya) {
        // Se añade el campo nivel_usuario, asignando valor 1 para usuarios nuevos.
        $consulta = "INSERT INTO eventos.usuarios (nombre, apellido, nombreUsuario, correo, contraseña, nivel_usuario) 
                     VALUES (:nombre, :apellido, :nombreUser, :correoUser, :contrasenya, :nivel_usuario)";
        
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->bindParam(':apellido', $apellido);
        $result->bindParam(':correoUser', $correoUser);
        $result->bindParam(':nombreUser', $nombreUser);
        $result->bindParam(':contrasenya', $contrasenya);
        
        // Asigna nivel 1 para un usuario registrado (normal)
        $nivel = 1;
        $result->bindParam(':nivel_usuario', $nivel, PDO::PARAM_INT);
        
        $result->execute();
        return $result;
    }

    public function listarEventos() {
        // Se lista ordenando por nomEvento (columna correcta en listaEventos)
        $consulta = "SELECT * FROM eventos.listaEventos ORDER BY nomEvento ASC";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarEventosNombre($nomEvento) {
        $consulta = "SELECT * FROM eventos.listaEventos WHERE nomEvento = :nomEvento";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarEventosCantante($nomCantante) {
        // Se utiliza la columna "cantante" (no nomCantante)
        $consulta = "SELECT * FROM eventos.listaEventos WHERE cantante = :nomCantante";
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

    public function verEventoE($idEvento) {
        // Se filtra por la columna "id_eventos"
        $consulta = "SELECT * FROM eventos.listaEventos WHERE id_eventos = :idEvento";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':idEvento', $idEvento);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarMisReservas($nombreUsuario) {
        // Se unen las tablas reservas, listaEventos y usuarios con los nombres correctos:
        // Reservas: id_usuario, id_evento
        // ListaEventos: id_eventos, nomEvento, fecha
        // Usuarios: id_usu, nombreUsuario
        $consulta = "SELECT reservas.*, listaEventos.nomEvento, listaEventos.fecha
                     FROM eventos.reservas
                     INNER JOIN eventos.listaEventos ON reservas.id_evento = listaEventos.id_eventos
                     INNER JOIN eventos.usuarios ON reservas.id_usuario = usuarios.id_usu
                     WHERE usuarios.nombreUsuario = :nombreUsuario";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarEvento($nomEvento, $cantante, $fecha, $cartel) {
        // Se inserta en la tabla listaEventos usando la columna "fecha" en lugar de "anyo"
        $consulta = "INSERT INTO eventos.listaEventos (nomEvento, cantante, fecha, cartel) VALUES (:nomEvento, :cantante, :fecha, :cartel)";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->bindParam(':cantante', $cantante);
        $result->bindParam(':fecha', $fecha);
        $result->bindParam(':cartel', $cartel);
        $result->execute();
        return $result;
    }
}
?>
