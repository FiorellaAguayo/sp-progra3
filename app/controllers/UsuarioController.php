<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UsuarioService;
use App\Models\Usuario;

class UsuarioController {
    private $usuarioService;

    public function __construct(){
        $this->usuarioService = new UsuarioService();
    }

    public function agregarUsuario(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $mail = $data['mail'];
        $usuario = $data['usuario'];
        $contraseña = $data['contraseña'];
        $perfil = $data['perfil'];
    
        $directory = __DIR__ . '/../../ImagenesDeProductos/2024';
        $uploadedFiles = $request->getUploadedFiles();
        $imagen = $uploadedFiles['foto'];
    
        if (empty($imagen)) {
            $payload = json_encode(['error' => true, 'message' => 'No se encontró el archivo de imagen.']);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    
        $uploadedFile = $uploadedFiles['foto'];
    
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            
            $fechaDeAlta = date('Y-m-d');
            $fechaDeBaja = null;
            $filename = $this->moveUploadedFile($directory, $uploadedFile, $usuario, $perfil);
            $resultadoVenta = $this->usuarioService->crearUsuario($mail, $usuario, $contraseña, $perfil, $filename, $fechaDeAlta, $fechaDeBaja);
        
            if (is_array($resultadoVenta) && isset($resultadoVenta['error'])) {
                $payload = json_encode(['success' => false, 'message' => $resultadoVenta['error']]);
            } else {
                $payload = json_encode(['success' => true, 'message' => 'Usuario Creado correctamente']);
            }
        } else {
            $payload = json_encode(['error' => true, 'message' => 'Error al subir la imagen.']);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }    

/*
    public function altaUsuario(Request $request, Response $response) {
        try {
            $data = $request->getParsedBody();
            $mail = $data['mail'];
            $usuario = $data['usuario'];
            $contraseña = $data['contraseña'];
            $perfil = $data['perfil'];
    
            if ($mail !== null && $usuario !== null && $contraseña !== null && $perfil !== null) {
                $usuarioExistente = $this->usuarioService->obtenerUsuarioPorEmail($mail);
    
                if ($usuarioExistente) {
                    $payload = json_encode(["message" => "El usuario ya existe"]);
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(409); // Conflict status
                } else {
                    $nuevoUsuario = $this->usuarioService->crearUsuario($mail, $usuario, $contraseña, $perfil);
                    $payload = json_encode([
                        'success' => true,
                        'message' => 'Usuario creado exitosamente',
                        'data' => [
                            'id' => $nuevoUsuario->getId(),
                            'mail' => $nuevoUsuario->getMail(),
                            'usuario' => $nuevoUsuario->getUsuario(),
                            'contraseña' => $nuevoUsuario->getContraseña(),
                            'perfil' => $nuevoUsuario->getPerfil(),
                        ],
                    ]);
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                }
    
            } else {
                $payload = json_encode(['success' => false, 'message' => 'Datos incompletos']);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Bad request status
            }
    
        } catch(\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // Internal server error
        }
    }    */

    private function moveUploadedFile($directory, $uploadedFile, $usuario, $perfil)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = strtolower($usuario . '_' . $perfil) . '.' . $extension;
    
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    
        return $filename;
    }

}