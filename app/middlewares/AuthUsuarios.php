<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthUsuario
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();
        if (isset($params['mail'], $params['usuario'], $params['contraseña'], $params['perfil']) &&
            !empty($params['mail']) && !empty($params['usuario']) && !empty($params['contraseña']) && !empty($params['perfil'])) {
            
                $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Parámetros incorrectos o incompletos para el usuario"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $response;
    }
}