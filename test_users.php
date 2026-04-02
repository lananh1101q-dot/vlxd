<?php
require_once 'services/user-service/db.php';
$stmt = $pdo->query("SELECT Manv, Tendangnhap, Vaitro FROM Nguoidung LIMIT 5");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT);
