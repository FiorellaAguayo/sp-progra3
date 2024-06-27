<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class AuthVenta
{
    public function validarCamposVenta(Request $request, RequestHandler $requestHandler) {
        $params = $request->getParsedBody();

        if (!isset($params['email'], $params['marca'], $params['tipo'], $params['modelo'], $params['cantidad']) ||
            empty($params['email']) || empty($params['marca']) || empty($params['tipo']) || empty($params['modelo']) || empty($params['cantidad'])) {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Parametros incorrectos o incompletos para la venta"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        return $requestHandler->handle($request);
    }
}