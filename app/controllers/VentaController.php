<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\VentaService;

class VentaController {
    private $ventaService;

    public function __construct() {
        $this->ventaService = new VentaService();
    }

    public function registrarVenta(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $marca = $data['marca'];
        $tipo = $data['tipo'];
        $modelo = $data['modelo'];
        $cantidad = $data['cantidad'];
        $fecha = $data['fecha'];

        $directory = __DIR__ . '/../../ImagenesDeProductos/2024';
        $uploadedFiles = $request->getUploadedFiles();;
        $imagen = $uploadedFiles['imagen'];

        if (empty($imagen)) {
            $payload = json_encode(['error' => true, 'message' => 'No se encontró el archivo de imagen.']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        $uploadedFile = $uploadedFiles['imagen'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile, $marca, $tipo, $modelo, $email, $fecha);

            $resultadoVenta = $this->ventaService->registrarVenta($email, $marca, $tipo, $modelo, $cantidad, $fecha);
        
            if (is_array($resultadoVenta) && isset($resultadoVenta['error'])) {
                $payload = json_encode(['success' => false, 'message' => $resultadoVenta['error']]);
            } else {
                $payload = json_encode(['success' => true, 'message' => 'Venta registrada correctamente']);
            }

        } else {
            $payload = json_encode(['error' => true, 'message' => 'Error al subir la imagen.']);
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerProductosVendidos(Request $request, Response $response) {
        $data = $request->getQueryParams();
        $fecha = $data['fecha'] ?? null;
        $ventas = $this->ventaService->obtenerVentasPorFecha($fecha);

        $payload = json_encode([
            'success' => true,
            'fecha' => $fecha ?? (new \DateTime('yesterday'))->format('Y-m-d'),
            'total_vendidos' => $ventas['total_vendidos'],
            'productos' => $ventas['productos']
        ]);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function modificarVenta(Request $request, Response $response) {
        $data = $request->getParsedBody(); // Esto ahora funciona con JSON gracias al middleware
        $numeroDePedido = $data['numeroDePedido'] ?? null;
        $email = $data['email'] ?? null;
        $marca = $data['marca'] ?? null;
        $tipo = $data['tipo'] ?? null;
        $modelo = $data['modelo'] ?? null;
        $cantidad = $data['cantidad'] ?? null;
    
        if (!$numeroDePedido || !$email || !$marca || !$tipo || !$modelo || !$cantidad) {
            $payload = json_encode([
                'success' => false,
                'message' => 'Datos incompletos para la modificación de la venta.'
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $resultado = $this->ventaService->modificarVenta($numeroDePedido, $email, $marca, $tipo, $modelo, $cantidad);
    
        if ($resultado) {
            $payload = json_encode(['success' => true, 'message' => 'Venta modificada exitosamente']);
        } else {
            $payload = json_encode(['success' => false, 'message' => 'No existe ese número de pedido']);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function obtenerVentasPorUsuario(Request $request, Response $response) {
        $data = $request->getQueryParams();
        $email = $data['email'] ?? null;
        $ventas = $this->ventaService->obtenerVentasPorEmail($email);

        $payload = json_encode([
            'success' => true,
            'productos' => $ventas['productos']
        ]);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerVentasPorTipoProducto(Request $request, Response $response) {
        $data = $request->getQueryParams();
        $tipo = $data['tipo'] ?? null;
        $ventas = $this->ventaService->obtenerVentasPorTipoProducto($tipo);

        $payload = json_encode([
            'success' => true,
            'productos' => $ventas['productos']
        ]);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function obtenerIngresosVentas(Request $request, Response $response) {
        $data = $request->getQueryParams();
        $fecha = $data['fecha'] ?? null;
    
        if ($fecha === null) {
            $payload = json_encode([
                'success' => false,
                'message' => 'Fecha no especificada'
            ]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $ingresos = $this->ventaService->obtenerIngresosVentasPorFecha($fecha);
    
        $payload = json_encode([
            'success' => true,
            'ingresos' => $ingresos['ingresos']
        ]);
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function mostrarProductoMasVendido(Request $request, Response $response) {
        $producto = $this->ventaService->obtenerProductoMasVendido();
    
        if ($producto) {
            $payload = json_encode([
                'success' => true,
                'producto_mas_vendido' => $producto
            ]);
        } else {
            $payload = json_encode([
                'success' => false,
                'message' => 'No se encontró ningún producto más vendido.'
            ]);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function moveUploadedFile($directory, $uploadedFile, $marca, $tipo, $modelo, $email, $fecha)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $emailUser = strstr($email, '@', true);
        
        $fechaFormatted = date('Ymd', strtotime($fecha));
        $filename = strtolower($marca . '_' . $tipo . '_' . $modelo . '_' . $emailUser . '_' . $fechaFormatted) . '.' . $extension;
    
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    
        return $filename;
    }
}