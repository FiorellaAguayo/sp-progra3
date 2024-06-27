<?php
require __DIR__ . '/../vendor/autoload.php';
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../app/routes.php';

$app->addBodyParsingMiddleware();

$app->add(function ($request, $handler) {
    $contentType = $request->getHeaderLine('Content-Type');
    if (strstr($contentType, 'application/json')) {
        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $request = $request->withParsedBody($contents);
        }
    }
    return $handler->handle($request);
});

// Run app
$app->run();