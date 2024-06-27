<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseClass;

class ConsultasMW
{
    public function validarCamposVenta(Request $request, RequestHandler $requestHandler) {
        $params = $request->getParsedBody();

        if (!isset($params['valor1'], $params['valor2']) || empty($params['valor1']) || empty($params['valor2'])) {
            $response = new ResponseClass();
            $response->getBody()->write(json_encode(["error" => "Parametros incorrectos o incompletos para la venta"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        return $requestHandler->handle($request);
    }
}