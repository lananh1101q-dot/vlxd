<?php
require_once 'services/user-service/db.php';

echo "Dang tao du lieu mau...\n";

try {
    // 1. Nguoidung
    $users = [
        ['NV01', 'admin', '123', 'Quan Tri Vien', 'admin@vlxd.com', 'admin'],
        ['STF01', 'staff', '123', 'Nhan Vien Kho', 'staff@vlxd.com', 'staff'],
        ['GST01', 'guest', '123', 'Khach Tham Quan', 'guest@vlxd.com', 'guest']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) VALUES (?, ?, ?, ?, ?, ?)");
    foreach($users as $u) $stmt->execute($u);
    echo "- Da tao nguoi dung mau (pass: 123)\n";

    // 2. Danhmucsp
    $dms = [
        [1, 'Gach', 'Cac loai gach xay dung'],
        [2, 'Xi mang', 'Xi mang bao va roi'],
        [3, 'Sat thep', 'Sat phi, sat cay, ton']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Danhmucsp (Madm, Tendm, Mota) VALUES (?, ?, ?)");
    foreach($dms as $d) $stmt->execute($d);

    // 3. Nhacungcap
    $nccs = [
        ['NCC01', 'Cong ty Hoa Phat', '0912345678', 'KCN Dung Quat'],
        ['NCC02', 'Xi mang Ha Tien', '0987654321', 'TP.HCM']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Nhacungcap (Mancc, Tenncc, Sdtncc, Diachincc) VALUES (?, ?, ?, ?)");
    foreach($nccs as $n) $stmt->execute($n);

    // 4. Nguyenvatlieu
    $nvls = [
        ['NVL01', 'Cat xay', 'm3', 250000],
        ['NVL02', 'Da 1x2', 'm3', 350000],
        ['NVL03', 'Sat phi 6', 'kg', 18000]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Nguyenvatlieu (Manvl, Tennvl, Dvt, Giavon) VALUES (?, ?, ?, ?)");
    foreach($nvls as $n) $stmt->execute($n);

    // 5. Sanpham
    $sps = [
        ['SP01', 'Gach Ong 8x18', 1, 'Vien', 1200],
        ['SP02', 'Sat Cay VinaKyoei', 3, 'Cay', 150000]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Sanpham (Masp, Tensp, Madm, Dvt, Giaban) VALUES (?, ?, ?, ?, ?)");
    foreach($sps as $s) $stmt->execute($s);

    // 6. Congthucsanpham (SP01 dung NVL01 và NVL02)
    $cts = [
        ['SP01', 'NVL01', 0.05],
        ['SP01', 'NVL02', 0.02]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Congthucsanpham (Masp, Manvl, Soluong) VALUES (?, ?, ?)");
    foreach($cts as $c) $stmt->execute($c);

    // 7. Kho
    $khos = [
        ['KHO01', 'Kho Chinh A', '123 Duong ABC, Quan 1'],
        ['KHO02', 'Kho Phu B', '456 Duong XYZ, Quan 9']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Kho (Makho, Tenkho, Diachi) VALUES (?, ?, ?)");
    foreach($khos as $k) $stmt->execute($k);

    // 8. Loaikhachhang & Khachhang
    $lkhs = [[1, 'Khach Le', 'Khach mua it'], [2, 'Khach Si', 'Dai ly cap 1']];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Loaikhachhang (Maloaikh, Tenloaikh, Motaloaikh) VALUES (?, ?, ?)");
    foreach($lkhs as $l) $stmt->execute($l);

    $khs = [
        ['KH01', 'Nguyen Van A', '0901112223', 'Quan 3', 1],
        ['KH02', 'Cong ty Xay Dung BDS', '0905556667', 'Binh Duong', 2]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Khachhang (Makh, Tenkh, Sdtkh, Diachikh, Maloaikh) VALUES (?, ?, ?, ?, ?)");
    foreach($khs as $k) $stmt->execute($k);

    // 9. Ton kho ban dau
    $tk_nvls = [
        ['KHO01', 'NVL01', 500],
        ['KHO01', 'NVL02', 300],
        ['KHO01', 'NVL03', 1000]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO Tonkho_nvl (Makho, Manvl, Soluongton) VALUES (?, ?, ?)");
    foreach($tk_nvls as $t) $stmt->execute($t);

    echo "Hoan thanh tao du lieu mau thanh cong!\n";

} catch (Exception $e) {
    echo "Loi: " . $e->getMessage() . "\n";
}
