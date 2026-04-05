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
use Controllers\ProductController;

$router = new Router();

// Products
$router->add('GET',    '/products',      [ProductController::class, 'getProducts']);
$router->add('GET',    '/products/{id}', [ProductController::class, 'getProduct']);
$router->add('POST',   '/products',      [ProductController::class, 'createProduct']);
$router->add('PUT',    '/products/{id}', [ProductController::class, 'updateProduct']);
$router->add('DELETE', '/products/{id}', [ProductController::class, 'deleteProduct']);

// Categories
$router->add('GET',    '/categories',      [ProductController::class, 'getCategories']);
$router->add('GET',    '/categories/{id}', [ProductController::class, 'getCategory']);
$router->add('POST',   '/categories',      [ProductController::class, 'createCategory']);
$router->add('PUT',    '/categories/{id}', [ProductController::class, 'updateCategory']);
$router->add('DELETE', '/categories/{id}', [ProductController::class, 'deleteCategory']);

// Materials
$router->add('GET',    '/materials',      [ProductController::class, 'getMaterials']);
$router->add('GET',    '/materials/{id}', [ProductController::class, 'getMaterial']);
$router->add('POST',   '/materials',      [ProductController::class, 'createMaterial']);
$router->add('PUT',    '/materials/{id}', [ProductController::class, 'updateMaterial']);
$router->add('DELETE', '/materials/{id}', [ProductController::class, 'deleteMaterial']);

// Suppliers
$router->add('GET',    '/suppliers',      [ProductController::class, 'getSuppliers']);
$router->add('GET',    '/suppliers/{id}', [ProductController::class, 'getSupplier']);
$router->add('POST',   '/suppliers',      [ProductController::class, 'createSupplier']);
$router->add('PUT',    '/suppliers/{id}', [ProductController::class, 'updateSupplier']);
$router->add('DELETE', '/suppliers/{id}', [ProductController::class, 'deleteSupplier']);

// Formulas
$router->add('GET',    '/formulas',      [ProductController::class, 'getFormulas']);
$router->add('POST',   '/formulas',      [ProductController::class, 'createFormula']);
$router->add('DELETE', '/formulas/{id}', [ProductController::class, 'deleteFormula']);

// Default route
$router->add('GET', '/', function() {
    echo json_encode(['success' => true, 'message' => 'Product Service MVC is running', 'version' => '2.0']);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
