<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as ResponseClass;
use Slim\Routing\RouteCollectorProxy;

// controllers
use App\Controllers\ProductoController;
use App\Controllers\VentaController;
use App\Controllers\UsuarioController;
use App\Controllers\LoginController;

// models

// middlewares
use App\Middlewares\AuthProductos;
use App\Middlewares\AuthVenta;
use App\Middlewares\AuthUsuario;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\ConsultasMW;


// TIENDA
$app->group('/tienda', function (RouteCollectorProxy $group) {
    $group->post('/alta', ProductoController::class . ':agregarProducto')->add(AuthProductos::class . ':validarCampos')->add(new AuthMiddleware(['admin']));
    $group->post('/consultar', ProductoController::class . ':consultarProducto')->add(new AuthMiddleware(['admin']));;
    $group->get('/listarProductos', ProductoController::class . ':listarProductos');
});

// VENTA
$app->group('/ventas', function (RouteCollectorProxy $group) {
    $group->post('/alta', VentaController::class . ':registrarVenta')->add(AuthVenta::class . ':validarCamposVenta')->add(new AuthMiddleware(['admin', 'empleado']));
    $group->put('/modificar', VentaController::class . ':modificarVenta')->add(new AuthMiddleware(['admin']));
    //$group->get('/listarProductos', ProductoController::class . ':listarProductos');
    $group->get('/consultar/productos/vendidos', VentaController::class . ':obtenerProductosVendidos');
    $group->get('/consultar/ventas/porUsuario', VentaController::class . ':obtenerVentasPorUsuario')->add(new AuthMiddleware(['admin', 'empleado']));
    $group->get('/consultar/ventas/porProducto', VentaController::class . ':obtenerVentasPorTipoProducto')->add(new AuthMiddleware(['admin', 'empleado']));;
    $group->get('/consultar/productos/entreValores', ProductoController::class . ':obtenerProductosEntreValores');//->add(ConsultasMW::class . ':validarCamposVenta')->add(new AuthMiddleware(['admin', 'empleado']));
    $group->get('/consultar/ventas/ingresos', VentaController::class . ':obtenerIngresosVentas')->add(new AuthMiddleware(['admin']));
    $group->get('/consultar/productos/masVendido', VentaController::class . ':mostrarProductoMasVendido')->add(new AuthMiddleware(['admin', 'empleado']));
});

// REGISTRO
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->post('/registro', UsuarioController::class . ':agregarUsuario');//->add(AuthUsuario::class . ':validarCampos');
    $group->post('/login', LoginController::class . ':login');
    //$group->post('/consultar', ProductoController::class . ':consultarProducto');
    //$group->get('/listarProductos', ProductoController::class . ':listarProductos');
});