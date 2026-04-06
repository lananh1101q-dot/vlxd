CREATE DATABASE IF NOT EXISTS vlxd_manufacturing;
USE vlxd_manufacturing;

CREATE TABLE Lenhsanxuat (
    Malenh VARCHAR(50) PRIMARY KEY,
    Masp VARCHAR(50) NOT NULL,
    Ngaysanxuat DATE NOT NULL,
    Soluongsanxuat DECIMAL(18,2) NOT NULL,
    Trangthai VARCHAR(50) DEFAULT N'dang_xu_ly',
    Ngaybatdau DATE NULL,
    Ngayketthuc DATE NULL,
    Ghichu TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE Chitiet_XuatNVL_Sanxuat (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Malenh VARCHAR(50),
    Manvl VARCHAR(50),
    Soluong DECIMAL(18,2) NOT NULL,
    FOREIGN KEY (Malenh) REFERENCES Lenhsanxuat(Malenh) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Chitiet_Nhapsanpham_Sanxuat (
    Id INT PRIMARY KEY AUTO_INCREMENT,
    Malenh VARCHAR(50),
    Makho VARCHAR(50),
    Masp VARCHAR(50),
    Soluong DECIMAL(18,2) NOT NULL,
    FOREIGN KEY (Malenh) REFERENCES Lenhsanxuat(Malenh) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
