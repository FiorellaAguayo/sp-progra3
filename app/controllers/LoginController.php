<?php

namespace App\Controllers;
//require_once './Model/Empleado.php';
//require_once './Utilities/AutentificadorJWT.php';
use App\Services\UsuarioService;
use App\Services\TokenService;
use App\Models\Usuario;

class LoginController {
    private $usuarioService;
    private $tokenService;

    public function __construct() {
        $this->usuarioService = new UsuarioService();
        $this->tokenService = new TokenService();
    }

    public function login($request, $response, $args) {
        $parametros = $request->getParsedBody();
        $mail = $parametros['mail'];
        $contraseña = $parametros['contrasena'];

        $usuario = $this->usuarioService->loginUsuario($mail, $contraseña);
        
        if ($usuario) {
            $datos = array('mail' => $usuario['mail'], 'perfil' => $usuario['perfil'], 'contrasena' => $usuario['contrasena']);
            $token = $this->tokenService->crearToken($datos);
            $payload = json_encode(array('jwt' => $token));
        } else {
            $payload = json_encode(array('error' => 'Usuario o contraseña incorrectos'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}