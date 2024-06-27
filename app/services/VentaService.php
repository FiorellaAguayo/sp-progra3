<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Venta;
use \PDOException;
use \PDO;

class VentaService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function registrarVenta($email, $marca, $tipo, $modelo, $cantidad, $fecha) {
        $sql = "SELECT id, stock, precio FROM productos WHERE marca = :marca AND tipo = :tipo AND modelo = :modelo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->execute();
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$producto) {
            return ["error" => "Producto no encontrado con los criterios especificados"];
        }
        
        if ($producto['stock'] < $cantidad) {
            return ["error" => "Stock insuficiente"];
        }
    
        // Calcular el nuevo stock
        $nuevoStock = $producto['stock'] - $cantidad;
    
        // Actualizar el stock del producto
        $sql = "UPDATE productos SET stock = :stock WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':stock', $nuevoStock);
        $stmt->bindParam(':id', $producto['id']);
        $stmt->execute();
    
        // Crear nueva venta
        $numeroDePedido = uniqid();
        $sql = "INSERT INTO ventas (email, marca, tipo, modelo, precio, cantidad, fecha, numeroDePedido) VALUES (:email, :marca, :tipo, :modelo, :precio, :cantidad, :fecha, :numeroDePedido)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':precio', $producto['precio']);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':numeroDePedido', $numeroDePedido);
        $stmt->execute();
    
        return new Venta($email, $marca, $tipo, $modelo, $producto['precio'], $cantidad, $fecha, $numeroDePedido);
    }

    public function modificarVenta($numeroDePedido, $email, $marca, $tipo, $modelo, $cantidad) {
        $sql = "SELECT id FROM ventas WHERE numeroDePedido = :numeroDePedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':numeroDePedido', $numeroDePedido);
        $stmt->execute();
        $ventaExistente = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$ventaExistente) {
            return false;  // No existe la venta
        }
    
        $sqlUpdate = "UPDATE ventas SET email = :email, marca = :marca, tipo = :tipo, modelo = :modelo, cantidad = :cantidad WHERE numeroDePedido = :numeroDePedido";
        $stmtUpdate = $this->db->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':email', $email);
        $stmtUpdate->bindParam(':marca', $marca);
        $stmtUpdate->bindParam(':tipo', $tipo);
        $stmtUpdate->bindParam(':modelo', $modelo);
        $stmtUpdate->bindParam(':cantidad', $cantidad);
        $stmtUpdate->bindParam(':numeroDePedido', $numeroDePedido);
        $stmtUpdate->execute();
    
        return true;
    }
    

    public function obtenerVentasPorFecha($fecha = null) {
        if (!$fecha) {
            $fecha = (new \DateTime('yesterday'))->format('Y-m-d');
        }
        
        $sql = "SELECT SUM(cantidad) AS total_vendidos FROM ventas WHERE fecha = :fecha";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $totalVendidos = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT email, marca, tipo, modelo, cantidad, precio FROM ventas WHERE fecha = :fecha";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total_vendidos' => $totalVendidos['total_vendidos'] ?? 0,
            'productos' => $productos
        ];
    }

    public function obtenerVentasPorEmail($email) {
        $sql = "SELECT email, marca, tipo, modelo, cantidad, precio FROM ventas WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'productos' => $productos
        ];
    }

    public function obtenerVentasPorTipoProducto($tipo) {
        $sql = "SELECT email, marca, tipo, modelo, cantidad, precio FROM ventas WHERE tipo = :tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'productos' => $productos
        ];
    }

    public function obtenerIngresosVentasPorFecha($fecha = null) {
        if ($fecha) {
            $sql = "SELECT fecha, SUM(precio * cantidad) AS ingresos FROM ventas WHERE fecha = :fecha GROUP BY fecha";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fecha', $fecha);
        } else {
            $sql = "SELECT fecha, SUM(precio * cantidad) AS ingresos FROM ventas GROUP BY fecha";
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return [
            'ingresos' => $ingresos
        ];
    }

    public function obtenerProductoMasVendido() {
        $sql = "SELECT marca, tipo, modelo, SUM(cantidad) AS total_vendido FROM ventas GROUP BY marca, tipo, modelo ORDER BY total_vendido DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado;
    }
}