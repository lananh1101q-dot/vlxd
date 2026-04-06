<?php
require_once 'shared/db.php';

function insertData($dbName, $inserts)
{
    echo "--- Dang nap du lieu vao database: $dbName ---\n";
    try {
        $pdo = getDbConnection($dbName);
        foreach ($inserts as $sql => $rows) {
            $stmt = $pdo->prepare($sql);
            foreach ($rows as $r) {
                try {
                    $stmt->execute($r);
                } catch (Exception $e) {
                    echo "    [!] Loi dong: " . $e->getMessage() . "\n";
                }
            }
        }
        echo "    [OK] Nap xong $dbName\n";
    } catch (Exception $e) {
        echo "    [ERR] Khong the ket noi $dbName: " . $e->getMessage() . "\n";
    }
}

// 1. User
insertData('vlxd_user', [
    "INSERT IGNORE INTO nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) VALUES (?, ?, ?, ?, ?, ?)" => [
        ['NV01', 'admin', '123', 'Quan Tri Vien', 'admin@vlxd.com', 'admin'],
        ['STF01', 'staff', '123', 'Nhan Vien Kho', 'staff@vlxd.com', 'staff'],
        ['KHO01', 'kho', '123', 'Nhan Vien Kho', 'staff@vlxd.com', 'kho']
    ]
]);

// 2. Product
insertData('vlxd_product', [
    "INSERT IGNORE INTO danhmucsp (Madm, Tendm, Mota) VALUES (?, ?, ?)" => [
        [1, 'Gach', 'Cac loai gach'],
        [2, 'Xi mang', 'Xi mang bao v.v'],
        [3, 'Sat thep', 'Sat phi']
    ],
    "INSERT IGNORE INTO nhacungcap (Mancc, Tenncc, Sdtncc, Diachincc) VALUES (?, ?, ?, ?)" => [
        ['NCC01', 'Hoa Phat', '0912345678', 'Dung Quat'],
        ['NCC02', 'Ha Tien', '0987654321', 'TPHCM']
    ],
    "INSERT IGNORE INTO nguyenvatlieu (Manvl, Tennvl, Dvt, Giavon) VALUES (?, ?, ?, ?)" => [
        ['NVL01', 'Cat xay', 'm3', 250000],
        ['NVL02', 'Da 1x2', 'm3', 350000]
    ],
    "INSERT IGNORE INTO sanpham (Masp, Tensp, Madm, Dvt, Giaban) VALUES (?, ?, ?, ?, ?)" => [
        ['SP01', 'Gach Ong 8x18', 1, 'Vien', 1200],
        ['SP02', 'Sat Cay VinaKyoei', 3, 'Cay', 150000]
    ],
    "INSERT IGNORE INTO congthucsanpham (Masp, Manvl, Soluong) VALUES (?, ?, ?)" => [
        ['SP01', 'NVL01', 0.05],
        ['SP01', 'NVL02', 0.02]
    ]
]);

// 3. Warehouse
insertData('vlxd_warehouse', [
    "INSERT IGNORE INTO kho (Makho, Tenkho, Diachi) VALUES (?, ?, ?)" => [
        ['KHO01', 'Kho Chinh A', 'Quan 1'],
        ['KHO02', 'Kho Phu B', 'Quan 9']
    ],
    "INSERT IGNORE INTO tonkho_nvl (Makho, Manvl, Soluongton) VALUES (?, ?, ?)" => [
        ['KHO01', 'NVL01', 500],
        ['KHO01', 'NVL02', 300]
    ],
    "INSERT IGNORE INTO tonkho_sp (Makho, Masp, Soluongton) VALUES (?, ?, ?)" => [
        ['KHO01', 'SP01', 1000],
        ['KHO01', 'SP02', 200]
    ],
    // ĐÃ CẬP NHẬT: Thêm cột Trangthai vào câu lệnh INSERT và dữ liệu mẫu
    "INSERT IGNORE INTO phieudieuchuyen (Madieuchuyen, Khoxuat, Khonhap, Ngaydieuchuyen, Ghichu, Trangthai) VALUES (?, ?, ?, ?, ?, ?)" => [
        ['DC01', 'KHO01', 'KHO02', date('Y-m-d'), 'Chuyen gach mau', 'da_thuc_hien'],
        ['DC02', 'KHO01', 'KHO02', date('Y-m-d'), 'Chuyen sat can gap', 'dang_xu_ly']
    ],
    "INSERT IGNORE INTO chitiet_phieudieuchuyen (Madieuchuyen, Masp, Soluong) VALUES (?, ?, ?)" => [
        ['DC01', 'SP01', 100],
        ['DC02', 'SP02', 10]
    ]
]);

// 4. Customer
insertData('vlxd_customer', [
    "INSERT IGNORE INTO loaikhachhang (Maloaikh, Tenloaikh, Motaloaikh) VALUES (?, ?, ?)" => [
        [1, 'Khach Le', 'Khach mua it'],
        [2, 'Khach Si', 'Dai ly']
    ],
    "INSERT IGNORE INTO khachhang (Makh, Tenkh, Sdtkh, Diachikh, Maloaikh) VALUES (?, ?, ?, ?, ?)" => [
        ['KH01', 'Nguyen Van A', '0901112223', 'Quan 3', 1],
        ['KH02', 'Cong ty B', '0905556667', 'Binh Duong', 2]
    ]
]);

// 5. Manufacturing
insertData('vlxd_manufacturing', [
    "INSERT IGNORE INTO lenhsanxuat (Malenh, Masp, Ngaysanxuat, Soluongsanxuat, Trangthai, Ngaybatdau, Ngayketthuc, Ghichu) VALUES (?, ?, ?, ?, ?, ?, ?, ?)" => [
        ['LS01', 'SP01', date('Y-m-d'), 500.00, 'dang_xu_ly', date('Y-m-d'), date('Y-m-d', strtotime('+7 days')), 'SX dot 1']
    ]
]);

echo "=== HOAN TAT NAP DU LIEU MAU VAO 5 DATABASE ===\n";