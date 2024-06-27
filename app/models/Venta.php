<?php
namespace App\Models;

class Venta
{
    private $_email;
    private $_marca;
    private $_tipo;
    private $_fecha;
    private $_cantidad;
    private $_modelo;
    private $_precio;

    public function __construct($email, $marca, $tipo, $cantidad, $fecha, $modelo)
    {
        $this->_email = $email;
        $this->_marca = $marca;
        $this->_tipo = $tipo;
        $this->_cantidad = $cantidad;
        $this->_modelo = $modelo;
        $this->_fecha = $fecha;
    }

    public function getMarca()
    {
        return $this->_marca;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function getTipo()
    {
        return $this->_tipo;
    }

    public function getCantidad()
    {
        return $this->_cantidad;
    }

    public function getModelo()
    {
        return $this->_modelo;
    }
    public function getPrecio()
    {
        return $this->_precio;
    }

    public function getFecha()
    {
        return $this->_fecha;
    }

    public function toArray() {
        return [
            'email' => $this->email,
            'marca' => $this->marca,
            'tipo' => $this->tipo,
            'modelo' => $this->modelo,
            'precio' => $this->precio,
            'cantidad' => $this->cantidad,
            'fecha' => $this->fecha,
            'numeroDePedido' => $this->numeroDePedido
        ];
    }
}