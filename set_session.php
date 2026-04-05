<?php
session_start();

if (isset($_POST['user'])) {
    $user = json_decode($_POST['user'], true);

    $_SESSION['user'] = [
        'username' => $user['Tendangnhap'],
        'fullname' => $user['Hoten'] ?? $user['Tendangnhap'],
        'role' => strtolower($user['Vaitro'])
    ];
}