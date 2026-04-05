<?php
namespace Core;

class Router {
    private $routes = [];

    public function add($method, $path, $callback) {
        $path = $this->normalizePath($path);
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback
        ];
    }

    public function dispatch($method, $uri) {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH));
        // Remove /api/v1 from path for routing
        $path = str_replace('/api/v1', '', $path);
        if (empty($path)) $path = '/';

        foreach ($this->routes as $route) {
            $pattern = $this->getPattern($route['path']);
            if ($route['method'] === strtoupper($method) && preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $this->executeCallback($route['callback'], $params);
            }
        }

        $this->jsonResponse(false, 'Endpoint not found in Customer Service', null, 404);
    }

    private function normalizePath($path) {
        return '/' . trim($path, '/');
    }

    private function getPattern($path) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeCallback($callback, $params) {
        if (is_array($callback)) {
            $controllerName = $callback[0];
            $action = $callback[1];
            $controller = new $controllerName();
            return $controller->$action($params);
        }
        return call_user_func($callback, $params);
    }

    private function jsonResponse($success, $message, $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }
}
