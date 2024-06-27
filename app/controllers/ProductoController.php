<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ProductoService;
use App\Models\Producto;

class ProductoController {
    private $productoService;

    public function __construct(){
        $this->productoService = new ProductoService();
    }

    public function agregarProducto(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $marca = $data['marca'];
        $precio = $data['precio'];
        $tipo = $data['tipo'];
        $modelo = $data['modelo'];
        $color = $data['color'];
        $stock = $data['stock'];

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
            $filename = $this->moveUploadedFile($directory, $uploadedFile, $marca, $tipo);

            $productoExistente = $this->productoService->buscarProductoPorMarcaTipo($marca, $tipo);
            if ($productoExistente) {
                $this->productoService->actualizarProducto($marca, $precio, $tipo, $modelo, $color, $productoExistente['stock'], $stock);
                $payload = json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
            } else {
                $this->productoService->crearProducto($marca, $precio, $tipo, $modelo, $color, $stock);
                $payload = json_encode(['success' => true, 'message' => 'Producto creado correctamente']);
            }

        } else {
            $payload = json_encode(['error' => true, 'message' => 'Error al subir la imagen.']);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function moveUploadedFile($directory, $uploadedFile, $marca, $tipo)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = strtolower($marca . '_' . $tipo) . '.' . $extension;
    
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    
        return $filename;
    }
}