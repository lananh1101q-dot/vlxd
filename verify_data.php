<?php
require_once 'services/user-service/db.php';
$tables = ['Nguoidung', 'Sanpham', 'Nguyenvatlieu', 'Nhacungcap', 'Danhmucsp', 'Kho', 'Khachhang', 'Loaikhachhang', 'Tonkho_nvl'];
echo "Ket qua nap du lieu mau:\n";
foreach($tables as $t){
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo " - [ " . str_pad($t, 15) . " ]: " . $count . " ban ghi\n";
    } catch(Exception $e) { echo " - [ " . str_pad($t, 15) . " ]: LOI - " . $e->getMessage() . "\n"; }
}
