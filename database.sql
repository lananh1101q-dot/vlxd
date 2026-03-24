CREATE DATABASE QuanLyKho;
USE QuanLyKho;


CREATE TABLE Danhmucsp (
    Madm INT PRIMARY KEY AUTO_INCREMENT,
    Tendm VARCHAR(100) NOT NULL UNIQUE,
    Mota VARCHAR(100)
);


CREATE TABLE Sanpham (
    Masp VARCHAR(50) PRIMARY KEY,
    Tensp VARCHAR(255) NOT NULL,
    Madm INT,
    Dvt VARCHAR(50) NOT NULL,
    Giaban DECIMAL(18, 2) DEFAULT 0,
    FOREIGN KEY (Madm) REFERENCES Danhmucsp(Madm)
);


CREATE TABLE Nhacungcap (
    Mancc VARCHAR(50) PRIMARY KEY,
    Tenncc VARCHAR(255) NOT NULL,
    Sdtncc VARCHAR(15),
    Diachincc VARCHAR(255)
);

CREATE TABLE Loaikhachhang (
    Maloaikh INT PRIMARY KEY AUTO_INCREMENT,
    Tenloaikh VARCHAR(100) NOT NULL,
    Motaloaikh TEXT
);


CREATE TABLE Khachhang (
    Makh VARCHAR(50) PRIMARY KEY,
    Tenkh VARCHAR(255) NOT NULL,
    Sdtkh VARCHAR(15),
    Diachikh VARCHAR(255),
    Maloaikh INT,
    FOREIGN KEY (Maloaikh) REFERENCES Loaikhachhang(Maloaikh)
);


CREATE TABLE Phieunhap (
    Manhaphang VARCHAR(50) PRIMARY KEY,
    Mancc VARCHAR(50),
    Makho VARCHAR(50),
    Ngaynhaphang DATE NOT NULL,
    Tongtiennhap DECIMAL(18, 2) DEFAULT 0,
    Ghichu TEXT,
    FOREIGN KEY (Mancc) REFERENCES Nhacungcap(Mancc),
    FOREIGN KEY (Makho) REFERENCES Kho(Makho)
);


CREATE TABLE Chitiet_Phieunhap (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Manhaphang VARCHAR(50),
    Masp VARCHAR(50),
    Soluong INT NOT NULL,
    Dongianhap DECIMAL(18, 2) NOT NULL,
    Thanhtien DECIMAL(18, 2) AS (Soluong * Dongianhap) STORED, -- Trường tính toán
    FOREIGN KEY (Manhaphang) REFERENCES Phieunhap(Manhaphang),
    FOREIGN KEY (Masp) REFERENCES Sanpham(Masp)
);


CREATE TABLE Phieuxuat (
    Maxuathang VARCHAR(50) PRIMARY KEY,
    Makh VARCHAR(50),
    Ngayxuat DATE NOT NULL,
    Tongtienxuat DECIMAL(18, 2) DEFAULT 0,
    Ghichu TEXT,
    FOREIGN KEY (Makh) REFERENCES Khachhang(Makh)
);


CREATE TABLE Chitiet_Phieuxuat (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Maxuathang VARCHAR(50),
    Masp VARCHAR(50),
    Soluong INT NOT NULL,
    Dongiaxuat DECIMAL(18, 2) NOT NULL,
    Thanhtien DECIMAL(18, 2) AS (Soluong * Dongiaxuat) STORED,
    FOREIGN KEY (Maxuathang) REFERENCES Phieuxuat(Maxuathang),
    FOREIGN KEY (Masp) REFERENCES Sanpham(Masp)
);

CREATE TABLE Kho (
    Makho VARCHAR(50) PRIMARY KEY,
    Tenkho VARCHAR(100) NOT NULL,
    Diachi TEXT
);


CREATE TABLE Tonkho (
    Makho VARCHAR(50),
    Masp VARCHAR(50),
    Soluongton INT DEFAULT 0,
    PRIMARY KEY (Makho, Masp), 
    FOREIGN KEY (Makho) REFERENCES Kho(Makho),
    FOREIGN KEY (Masp) REFERENCES Sanpham(Masp)
);
CREATE TABLE Nguoidung (
    Manv VARCHAR(50) PRIMARY KEY,
    Tendangnhap VARCHAR(100) NOT NULL UNIQUE,
    Matkhau VARCHAR(255) NOT NULL, 
    Hovaten VARCHAR(255),
    Email VARCHAR(100),
    Vaitro VARCHAR(50) NOT NULL
);
CREATE TABLE Thanhtoan (
    Matt INT AUTO_INCREMENT PRIMARY KEY,
    Maxuathang VARCHAR(50),
    Ngaythanhtoan DATE NOT NULL,
    Sotienthanhtoan DECIMAL(18,2) NOT NULL,
    Hinhthuc VARCHAR(50),
    Ghichu TEXT,
    FOREIGN KEY (Maxuathang) REFERENCES Phieuxuat(Maxuathang)
);





-- 1. Bảng Người dùng (Quản trị, Nhân viên, Khách hàng)
CREATE TABLE NguoiDung (
    ma_nguoi_dung INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro VARCHAR(20) COMMENT 'ADMIN, NHANVIEN, KHACHHANG',
    ho_ten VARCHAR(100),
    email VARCHAR(100),
    so_dien_thoai VARCHAR(15)
) ENGINE=InnoDB;

-- 2. Bảng Nhà cung cấp nguyên liệu
CREATE TABLE NhaCungCap (
    ma_ncc INT AUTO_INCREMENT PRIMARY KEY,
    ten_ncc VARCHAR(150) NOT NULL,
    dia_chi TEXT,
    so_dien_thoai VARCHAR(15),
    email VARCHAR(100)
) ENGINE=InnoDB;

-- 3. Bảng Nguyên vật liệu (Cát, đá, thép cuộn...)
CREATE TABLE NguyenVatLieu (
    ma_nvl INT AUTO_INCREMENT PRIMARY KEY,
    ma_ncc INT,
    ten_nvl VARCHAR(100) NOT NULL,
    don_vi_tinh VARCHAR(20),
    so_luong_ton INT DEFAULT 0,
    FOREIGN KEY (ma_ncc) REFERENCES NhaCungCap(ma_ncc)
) ENGINE=InnoDB;

-- 4. Bảng Sản phẩm hoàn thiện (Xi măng bao, Thép cây, Gạch...)
CREATE TABLE SanPham (
    ma_sp INT AUTO_INCREMENT PRIMARY KEY,
    ten_sp VARCHAR(150) NOT NULL,
    loai_sp VARCHAR(50),
    gia_ban DECIMAL(15, 2),
    don_vi_tinh VARCHAR(20),
    so_luong_ton_kho INT DEFAULT 0
) ENGINE=InnoDB;

-- 5. Bảng Lệnh sản xuất (Khi kho thiếu hàng sẽ tạo lệnh này)
CREATE TABLE LenhSanXuat (
    ma_lenh INT AUTO_INCREMENT PRIMARY KEY,
    ma_sp INT,
    so_luong_yc INT,
    ngay_bat_dau DATE,
    trang_thai VARCHAR(50) DEFAULT 'Dang cho',
    FOREIGN KEY (ma_sp) REFERENCES SanPham(ma_sp)
) ENGINE=InnoDB;

-- 6. Bảng Đơn hàng
CREATE TABLE DonHang (
    ma_don_hang INT AUTO_INCREMENT PRIMARY KEY,
    ma_khach_hang INT,
    ngay_dat DATETIME DEFAULT CURRENT_TIMESTAMP,
    tong_tien DECIMAL(15, 2),
    trang_thai_thanh_toan VARCHAR(50),
    FOREIGN KEY (ma_khach_hang) REFERENCES NguoiDung(ma_nguoi_dung)
) ENGINE=InnoDB;

-- 7. Bảng Chi tiết đơn hàng
CREATE TABLE ChiTietDonHang (
    ma_chi_tiet INT AUTO_INCREMENT PRIMARY KEY,
    ma_don_hang INT,
    ma_sp INT,
    so_luong INT,
    gia_don_vi DECIMAL(15, 2),
    FOREIGN KEY (ma_don_hang) REFERENCES DonHang(ma_don_hang) ON DELETE CASCADE,
    FOREIGN KEY (ma_sp) REFERENCES SanPham(ma_sp)
) ENGINE=InnoDB;

-- 8. Bảng Vận chuyển
CREATE TABLE VanChuyen (
    ma_van_chuyen INT AUTO_INCREMENT PRIMARY KEY,
    ma_don_hang INT UNIQUE,
    ngay_giao DATE,
    ma_van_don VARCHAR(50),
    don_vi_van_chuyen VARCHAR(100),
    trang_thai_giao VARCHAR(50),
    FOREIGN KEY (ma_don_hang) REFERENCES DonHang(ma_don_hang)
) ENGINE=InnoDB;