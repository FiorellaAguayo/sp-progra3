<?php

namespace App\Services;
use App\Data\Database;
use App\Models\Producto;
use \PDOException;
use \PDO;

class ProductoService {
    private $db;

    public function __construct(){
        $this->db = Database::getInstance();
    }

    public function crearProducto($marca, $precio, $tipo, $modelo, $color, $stock) {
        $sql = "INSERT INTO productos (marca, precio, tipo, modelo, color, stock) VALUES (:marca, :precio, :tipo, :modelo, :color, :stock)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':color', $color);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();
        $id_producto = $this->db->lastInsertId();

        return new Producto($id_producto, $marca, $precio, $tipo, $modelo, $color, $stock);
    }

    public function modificarProducto($id, $marca, $precio, $tipo, $modelo, $color, $stock) {
        $sql = "UPDATE productos SET ";
        $fields = [];
        if (!is_null($marca)) $fields[] = "marca = :marca";
        if (!is_null($precio)) $fields[] = "precio = :precio";
        if (!is_null($tipo)) $fields[] = "tipo = :tipo";
        if (!is_null($modelo)) $fields[] = "modelo = :modelo";
        if (!is_null($color)) $fields[] = "color = :color";
        if (!is_null($stock)) $fields[] = "stock = :stock";
        $sql .= implode(", ", $fields);
        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        if (!is_null($marca)) $stmt->bindParam(':marca', $marca);
        if (!is_null($precio)) $stmt->bindParam(':precio', $precio);
        if (!is_null($tipo)) $stmt->bindParam(':tipo', $tipo);
        if (!is_null($modelo)) $stmt->bindParam(':modelo', $modelo);
        if (!is_null($color)) $stmt->bindParam(':color', $color);
        if (!is_null($stock)) $stmt->bindParam(':stock', $stock);
        $stmt->execute();

        return new Producto($id, $marca, $precio, $tipo, $modelo, $color, $stock);
    }

    public function eliminarProducto($id) {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function actualizarProducto($marca, $precio, $tipo, $modelo, $color, $stockExistente, $stockAIncrementar) {
        try {
            $sql = "UPDATE productos SET precio = :precio, modelo = :modelo, color = :color, stock = :nuevoStock WHERE marca = :marca AND tipo = :tipo";
            $nuevoStock = $stockExistente + $stockAIncrementar;
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':modelo', $modelo);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':nuevoStock', $nuevoStock);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            return $stmt->rowCount(); // Retorna el número de filas afectadas
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function obtenerListaProductos() {
        try {
            $sql = "SELECT * FROM productos";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $listaRetorno = [];
            foreach ($productos as $producto) {
                $productoInstance = new Producto($producto['id'], $producto['marca'], $producto['precio'], $producto['tipo'], $producto['modelo'], $producto['color'], $producto['stock']);
                array_push($listaRetorno, $productoInstance);
            }
            return $listaRetorno;
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function buscarProductoPorMarcaTipoColor($marca, $tipo, $color) {
        try {
            $sql = "SELECT * FROM productos WHERE marca = :marca AND tipo = :tipo AND color = :color";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':color', $color);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Retorna null si no encuentra el producto
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function buscarProductoPorMarcaTipo($marca, $tipo) {
        try {
            $sql = "SELECT * FROM productos WHERE marca = :marca AND tipo = :tipo";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Retorna null si no encuentra el producto
        } catch(PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }
    

    public function obtenerProductoPorMarca($marca) {
        $sql = "SELECT * FROM productos WHERE marca = :marca";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':marca', $marca);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retorna vacío si no hay productos
    }
    
    public function obtenerProductoPorTipo($tipo) {
        $sql = "SELECT * FROM productos WHERE tipo = :tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retorna vacío si no hay productos
    }

    public function obtenerProductosEntreValores($valor1, $valor2) {
        $minPrecio = min($valor1, $valor2);
        $maxPrecio = max($valor1, $valor2);
    
        $sql = "SELECT id, marca, precio, tipo, modelo, color, stock FROM productos WHERE precio BETWEEN :minPrecio AND :maxPrecio";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':minPrecio', $minPrecio);
        $stmt->bindParam(':maxPrecio', $maxPrecio);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'productos' => $productos
        ];
    }      
}