<?php
namespace Controllers;

class BaseController {
    protected function jsonResponse($success, $message, $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }

    protected function getBody() {
        return json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }
}
