CREATE DATABASE IF NOT EXISTS vlxd_customer;
USE vlxd_customer;

CREATE TABLE Loaikhachhang (
    Maloaikh INT PRIMARY KEY AUTO_INCREMENT,
    Tenloaikh VARCHAR(100) NOT NULL,
    Motaloaikh TEXT
) ENGINE=InnoDB;

CREATE TABLE Khachhang (
    Makh VARCHAR(50) PRIMARY KEY,
    Tenkh VARCHAR(255) NOT NULL,
    Sdtkh VARCHAR(15),
    Diachikh VARCHAR(255),
    Maloaikh INT,
    FOREIGN KEY (Maloaikh) REFERENCES Loaikhachhang(Maloaikh) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE Thanhtoan (
    Matt INT AUTO_INCREMENT PRIMARY KEY,
    Maxuathang VARCHAR(50),
    Ngaythanhtoan DATE NOT NULL,
    Sotienthanhtoan DECIMAL(18,2) NOT NULL,
    Hinhthuc VARCHAR(50),
    Ghichu TEXT
) ENGINE=InnoDB;
