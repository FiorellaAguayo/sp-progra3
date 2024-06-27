<?php
namespace App\Models;

class Usuario
{
    private $_id;
    private $_mail;
    private $_usuario;
    private $_contraseña;
    private $_perfil;
    private $_foto;
    private $_fechaDeAlta;
    private $_fechaDeBaja;

    public function __construct($id, $mail, $usuario, $contraseña, $perfil, $foto, $fechaDeAlta, $fechaDeBaja)
    {
        $this->_id = $id;
        $this->_mail = $mail;
        $this->_usuario = $usuario;
        $this->_contraseña = $contraseña;
        $this->_perfil = $perfil;
        $this->_foto = $foto;
        $this->_fechaDeAlta = $fechaDeAlta;
        $this->_fechaDeBaja = $fechaDeBaja;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getMail()
    {
        return $this->_mail;
    }

    public function getUsuario()
    {
        return $this->_usuario;
    }

    public function getContraseña()
    {
        return $this->_contraseña;
    }

    public function getPerfil()
    {
        return $this->_perfil;
    }

    public function getFoto()
    {
        return $this->_foto;
    }

    public function getFechaDeAlta()
    {
        return $this->_fechaDeAlta;
    }

    public function getFechaDeBaja()
    {
        return $this->_fechaDeBaja;
    }

    public function setFechaDeBaja($fechaDeBaja)
    {
        $this->_fechaDeBaja = $fechaDeBaja;
    }
}
