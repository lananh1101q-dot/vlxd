-- warehouse_db.sql
CREATE DATABASE IF NOT EXISTS vlxd_warehouse;
USE vlxd_warehouse;

CREATE TABLE Kho (
    Makho VARCHAR(50) PRIMARY KEY,
    Tenkho VARCHAR(100) NOT NULL,
    Diachi TEXT
) ENGINE=InnoDB;

CREATE TABLE Tonkho_nvl (
    Makho VARCHAR(50),
    Manvl VARCHAR(50),
    Soluongton DECIMAL(18,2) DEFAULT 0,
    PRIMARY KEY (Makho, Manvl),
    FOREIGN KEY (Makho) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Tonkho_sp (
    Makho VARCHAR(50),
    Masp VARCHAR(50),
    Soluongton DECIMAL(18,2) DEFAULT 0,
    PRIMARY KEY (Makho, Masp),
    FOREIGN KEY (Makho) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Phieunhap (
    Manhaphang VARCHAR(50) PRIMARY KEY,
    Mancc VARCHAR(50),
    Makho VARCHAR(50),
    Ngaynhaphang DATE NOT NULL,
    Tongtiennhap DECIMAL(18, 2) DEFAULT 0,
    Ghichu TEXT,
    FOREIGN KEY (Makho) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Chitiet_Phieunhap (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Manhaphang VARCHAR(50),
    Manvl VARCHAR(50),
    Soluong DECIMAL(18,2) NOT NULL,
    Dongianhap DECIMAL(18, 2) NOT NULL,
    Thanhtien DECIMAL(18, 2) AS (Soluong * Dongianhap) STORED,
    FOREIGN KEY (Manhaphang) REFERENCES Phieunhap(Manhaphang) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- ĐÃ CẬP NHẬT: Thêm cột Trangthai vào bảng Phieudieuchuyen
CREATE TABLE Phieudieuchuyen (
    Madieuchuyen VARCHAR(50) PRIMARY KEY,
    Khoxuat VARCHAR(50) NOT NULL,
    Khonhap VARCHAR(50) NOT NULL,
    Ngaydieuchuyen DATE NOT NULL,
    Ghichu TEXT,
    Trangthai VARCHAR(50) DEFAULT 'dang_xu_ly',
    FOREIGN KEY (Khoxuat) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (Khonhap) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE Chitiet_Phieudieuchuyen (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Madieuchuyen VARCHAR(50),
    Masp VARCHAR(50),
    Soluong DECIMAL(18,2) NOT NULL,
    FOREIGN KEY (Madieuchuyen) REFERENCES Phieudieuchuyen(Madieuchuyen) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Phieuxuat (
    Maxuathang VARCHAR(50) PRIMARY KEY,
    Makh VARCHAR(50),
    Makho VARCHAR(50),
    Ngayxuat DATE NOT NULL,
    Tongtienxuat DECIMAL(18, 2) DEFAULT 0,
    Ghichu TEXT,
    FOREIGN KEY (Makho) REFERENCES Kho(Makho) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Chitiet_Phieuxuat (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Maxuathang VARCHAR(50),
    Masp VARCHAR(50),
    Soluong DECIMAL(18,2) NOT NULL,
    Dongiaxuat DECIMAL(18, 2) NOT NULL,
    Thanhtien DECIMAL(18, 2) AS (Soluong * Dongiaxuat) STORED,
    FOREIGN KEY (Maxuathang) REFERENCES Phieuxuat(Maxuathang) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;