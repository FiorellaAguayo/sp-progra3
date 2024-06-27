<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Usuario;
use \PDOException;
use \PDO;

class UsuarioService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function crearUsuario($mail, $usuario, $contraseña, $perfil, $foto, $fechaDeAlta, $fechaDeBaja) {
        $sql = "INSERT INTO usuarios (mail, usuario, contrasena, perfil, foto, fecha_de_alta, fecha_de_baja) VALUES (:mail, :usuario, :contrasena, :perfil, :foto, :fechaDeAlta, :fechaDeBaja)";
        $stmt = $this->db->prepare($sql);
    
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contraseña);
        $stmt->bindParam(':perfil', $perfil);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':fechaDeAlta', $fechaDeAlta);
        $stmt->bindParam(':fechaDeBaja', $fechaDeBaja);
        $stmt->execute();
        $id_usuario = $this->db->lastInsertId();
    
        return new Usuario($id_usuario, $mail, $usuario, $contraseña, $perfil, $foto, $fechaDeAlta, $fechaDeBaja);
    }

    public function loginUsuario($mail, $contraseña)
    {
        $sql = "SELECT * FROM usuarios WHERE mail = :mail";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && $contraseña === $usuario['contrasena']) {
            return $usuario;
        } else {
            return null;
        }
    }
}