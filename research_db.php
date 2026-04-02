<?php
require_once 'services/user-service/db.php';
$tables = ['Nguoidung', 'Sanpham', 'Nguyenvatlieu', 'Nhacungcap', 'Danhmucsp', 'Kho', 'Khachhang', 'Loaikhachhang', 'Lenhsanxuat', 'Phieunhap', 'Chitiet_Phieunhap', 'Phieuxuat', 'Chitiet_Phieuxuat', 'Congthucsanpham', 'Tonkho_sp', 'Tonkho_nvl'];
foreach($tables as $t){
    echo "--- Table: $t ---\n";
    try {
        $res = $pdo->query("DESCRIBE $t");
        while($c = $res->fetch(PDO::FETCH_ASSOC)) {
            echo $c['Field'] . " (" . $c['Type'] . ")\n";
        }
    } catch(Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }
}
