<?php

namespace App\Middlewares;
use Slim\Psr7\Response as ResponseClass;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthProductos
{
    public function validarCampos(Request $request, RequestHandler $requestHandler)
    {
        $params = $request->getParsedBody();

        if (isset($params['marca'], $params['precio'], $params['tipo'], $params['modelo'], $params['color'], $params['stock']) &&
            !empty($params['marca']) && !empty($params['precio']) && !empty($params['tipo']) && !empty($params['modelo']) && !empty($params['color']) && !empty($params['stock'])) {
            
                $response = $requestHandler->handle($request);
        } else {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Parámetros incorrectos o incompletos para el producto"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        return $response;
    }
}
