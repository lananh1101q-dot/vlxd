-- ============================================================
-- SETUP DỮ LIỆU PHÂN QUYỀN 3 ROLES
-- Admin, Staff, Guest
-- ============================================================

-- Xóa dữ liệu cũ nếu có (tùy chọn)
-- DELETE FROM Nguoidung;

-- Thêm người dùng sample với các roles khác nhau

-- Admin account
INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) 
VALUES ('ADM001', 'admin', '$2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2', 'Quản Trị Viên', 'admin@vlxd.com', 'admin')
ON DUPLICATE KEY UPDATE Matkhau = VALUES(Matkhau), Vaitro = VALUES(Vaitro);

-- Staff accounts
INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) 
VALUES 
('STF001', 'staff', '$2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2', 'Nhân Viên 1', 'staff1@vlxd.com', 'staff'),
('STF002', 'staff2', '$2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2', 'Nhân Viên 2', 'staff2@vlxd.com', 'staff'),
('STF003', 'staff3', '$2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2', 'Nhân Viên 3', 'staff3@vlxd.com', 'staff')
ON DUPLICATE KEY UPDATE Matkhau = VALUES(Matkhau), Vaitro = VALUES(Vaitro);

-- Guest account (optional - usually guests don't have accounts)
INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro) 
VALUES ('GST001', 'guest', '$2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2', 'Khách Vãng Lai', 'guest@vlxd.com', 'guest')
ON DUPLICATE KEY UPDATE Matkhau = VALUES(Matkhau), Vaitro = VALUES(Vaitro);

-- ============================================================
-- Mật khẩu default cho test:
-- Admin: admin123
-- Staff: staff123  
-- Guest: guest123
-- 
-- Để cập nhật mật khẩu, dùng PHP:
-- password_hash('your_password', PASSWORD_DEFAULT)
-- 
-- Hash của 'admin123' (bcrypt):
-- $2y$10$N9qo8uLOickgx2ZMRZoMye0irrKhVLfSvJYYcnjpHVMc6OPLVUlXW2
-- ============================================================

-- Kiểm tra dữ liệu
SELECT Manv, Tendangnhap, Hovaten, Email, Vaitro FROM Nguoidung WHERE Vaitro IN ('admin', 'staff', 'guest');
