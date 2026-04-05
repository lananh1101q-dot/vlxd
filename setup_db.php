<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

echo "<!DOCTYPE html><html lang='vi'><head><meta charset='utf-8'><title>Setup Database Microservices</title><style>body{font-family:monospace; background:#1e1e1e; color:#00ff00; padding:20px;} .err{color:#ff5555;}</style></head><body><h2>Tiến trình Thiết lập 5 Cơ sở dữ liệu...</h2><pre>";

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
    ]);

    $services = [
        'vlxd_user' => __DIR__ . '/services/user-service/user_db.sql',
        'vlxd_product' => __DIR__ . '/services/product-service/product_db.sql',
        'vlxd_warehouse' => __DIR__ . '/services/warehouse-service/warehouse_db.sql',
        'vlxd_customer' => __DIR__ . '/services/customer-service/customer_db.sql',
        'vlxd_manufacturing' => __DIR__ . '/services/manufacturing-service/manufacturing_db.sql',
    ];

    foreach ($services as $db => $file) {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
        echo "[OK] Đã tạo / kiểm tra Database: <b>$db</b>\n";
        
        if (file_exists($file)) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
            echo "    -> Đã Import Schema thành công từ file .sql.\n";
        } else {
            echo "    <span class='err'>-> LỖI: Không tìm thấy SQL FILE: $file</span>\n";
        }
        echo "---------------------------------------------------\n";
    }
    
    // Seed admin
    $pdo->exec("USE `vlxd_user`;");
    $chk = $pdo->query("SELECT * FROM Nguoidung WHERE Tendangnhap = 'admin'")->fetch();
    if (!$chk) {
        $passHash = password_hash('123456', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Vaitro) VALUES ('AD01', 'admin', '$passHash', 'Administrator', 'admin')");
        echo "\n[SEED] Đã tạo tài khoản quản trị mặc định trong User Service.\n";
        echo "       Tài khoản: admin \n       Mật khẩu:  123456 \n";
    } else {
        echo "\n[SEED] Tài khoản admin đã tồn tại. Bỏ qua.\n";
    }

    echo "\n<h3 style='color:#00e5ff'>=== HOÀN TẤT THIẾT LẬP DATABASE MICROSERVICES ===</h3>\n";
    echo "Bây giờ bạn hãy chạy file <b>./start_services.sh</b> ở thư mục gốc để khởi động các cổng mạng.\n";
    echo "Sau đó truy cập vào trang <a href='http://localhost:8888' style='color:orange'>API Gateway</a> để kiểm tra luồng API Gateway.";
} catch(PDOException $e) {
    echo "<span class='err'>LỖI DATABASE: " . $e->getMessage() . "</span>";
}
echo "</pre></body></html>";
