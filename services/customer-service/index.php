<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Custom Autoloader
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Core\Router;
use Controllers\CustomerController;

$router = new Router();

// Customer Types
$router->add('GET',    '/customer-types',      [CustomerController::class, 'getCustomerTypes']);
$router->add('GET',    '/customer-types/{id}', [CustomerController::class, 'getCustomerType']);
$router->add('POST',   '/customer-types',      [CustomerController::class, 'createCustomerType']);
$router->add('PUT',    '/customer-types/{id}', [CustomerController::class, 'updateCustomerType']);
$router->add('DELETE', '/customer-types/{id}', [CustomerController::class, 'deleteCustomerType']);

// Customers
$router->add('GET',    '/customers',      [CustomerController::class, 'getCustomers']);
$router->add('GET',    '/customers/{id}', [CustomerController::class, 'getCustomer']);
$router->add('POST',   '/customers',      [CustomerController::class, 'createCustomer']);
$router->add('PUT',    '/customers/{id}', [CustomerController::class, 'updateCustomer']);
$router->add('DELETE', '/customers/{id}', [CustomerController::class, 'deleteCustomer']);

// Default route
$router->add('GET', '/', function() {
    echo json_encode(['success' => true, 'message' => 'Customer Service MVC is running', 'version' => '2.0']);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
