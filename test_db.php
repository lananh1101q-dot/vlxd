<?php
require_once 'services/user-service/db.php';
$stmt = $pdo->query("DESCRIBE Nguoidung");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($cols, JSON_PRETTY_PRINT);
