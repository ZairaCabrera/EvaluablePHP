<?php

// Clase Eventos: Extiende de Modelo y se encarga de gestionar las operaciones relacionadas con usuarios, eventos y reservas.
class Eventos extends Modelo {

    // Consulta un usuario a partir de su nombre de usuario.
    public function consultarUsuario($nombreUsuario) {
        $consulta = "SELECT * FROM eventos.usuarios WHERE nombreUsuario = :nombreUsuario";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombreUsuario', $nombreUsuario);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Inserta un nuevo usuario en la base de datos con nivel 1 y una foto de perfil.
    public function insertarUsuario($nombre, $apellido, $nombreUser, $correoUser, $contrasenya, $foto) {
        $consulta = "INSERT INTO eventos.usuarios (nombre, apellido, nombreUsuario, correo, contraseña, nivel_usuario, foto) 
                     VALUES (:nombre, :apellido, :nombreUser, :correoUser, :contrasenya, :nivel_usuario, :foto)";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nombre', $nombre);
        $result->bindParam(':apellido', $apellido);
        $result->bindParam(':nombreUser', $nombreUser);
        $result->bindParam(':correoUser', $correoUser);
        $result->bindParam(':contrasenya', $contrasenya);
        $nivel = 1; // Usuario invitado, le asignamos nivel 1
        $result->bindParam(':nivel_usuario', $nivel, PDO::PARAM_INT);
        $result->bindParam(':foto', $foto);
        $result->execute();
        return $result;
    }
    
    // Devuelve la lista de eventos ordenados alfabéticamente por nombre.
    public function listarEventos() {
        $consulta = "SELECT * FROM eventos.listareventos ORDER BY nomEvento ASC";
        $result = $this->conexion->query($consulta);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca eventos por el nombre del evento.
    public function buscarEventosNombre($nomEvento) {
        $consulta = "SELECT * FROM eventos.listareventos WHERE nomEvento = :nomEvento";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Busca eventos por el nombre del cantante.
    public function buscarEventosCantante($nomCantante) {
        $consulta = "SELECT * FROM eventos.listareventos WHERE cantante = :nomCantante";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomCantante', $nomCantante);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // Devuelve los detalles de un evento específico.
    public function verEventoE($idEvento) {
        $consulta = "SELECT * FROM eventos.listareventos WHERE id_eventos = :idEvento";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':idEvento', $idEvento);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    // Busca las reservas de un usuario, uniendo reservas, eventos y usuarios.
    public function buscarMisReservas($nombreUsuario) {
        $consulta = "SELECT reservas.*, listareventos.nomEvento, listareventos.fecha
                     FROM eventos.reservas
                     INNER JOIN eventos.listareventos ON reservas.id_evento = listareventos.id_evento
                     INNER JOIN eventos.usuarios ON reservas.id_usuario = usuarios.id_usu
                     WHERE usuarios.nombreUsuario = :nombreUsuario";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Inserta una reserva para un usuario en un evento.
    public function insertarReserva($idUsuario, $idEvento) {
        $consulta = "INSERT INTO eventos.reservas (id_usuario, id_evento) VALUES (:id_usuario, :id_evento)";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_evento', $idEvento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Elimina una reserva para un usuario en un evento.
    public function eliminarReserva($idUsuario, $idEvento) {
        $consulta = "DELETE FROM eventos.reservas WHERE id_usuario = :id_usuario AND id_evento = :id_evento";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_evento', $idEvento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Actualiza la foto de perfil de un usuario.
    public function actualizarFotoPerfil($idUser, $foto) {
        $consulta = "UPDATE eventos.usuarios SET foto = :foto WHERE id_usu = :idUser";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Inserta un nuevo evento en la base de datos.
    public function insertarEvento($nomEvento, $cantante, $fecha, $cartel) {
        $consulta = "INSERT INTO eventos.listareventos (nomEvento, cantante, fecha, cartel) VALUES (:nomEvento, :cantante, :fecha, :cartel)";
        $result = $this->conexion->prepare($consulta);
        $result->bindParam(':nomEvento', $nomEvento);
        $result->bindParam(':cantante', $cantante);
        $result->bindParam(':fecha', $fecha);
        $result->bindParam(':cartel', $cartel);
        $result->execute();
        return $result;
    }

    // Elimina un evento de la base de datos (acción exclusiva para administradores).
    public function eliminarEvento($idEvento) {
        $consulta = "DELETE FROM eventos.listareventos WHERE id_evento = :idEvento";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':idEvento', $idEvento, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
?>
