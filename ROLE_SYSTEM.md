# 🔐 Hệ Thống Phân Quyền 3 Roles - VLXD

## 📋 **3 Roles Định Nghĩa**

| Role | Tiếng Việt | Mô Tả | Quyền |
|------|-----------|-------|-------|
| **admin** | Quản trị viên | Toàn quyền | ✅ CRUD tất cả |
| **staff** | Nhân viên | Nhân viên bình thường | ✅ Xem/Thêm dữ liệu |
| **guest** | Khách (không đăng nhập) | Khách vãng lai | ❌ Chỉ xem |

---

## 📂 **Files Tạo/Sửa**

### **1. [`role_helper.php`](role_helper.php )**
File này cung cấp các helper functions:

```php
// Check role
isAdmin()          // return true/false
isStaff()          // return true/false
getCurrentRole()   // return role string
hasRole('admin')   // check specific role
hasAnyRole(['admin', 'staff'])  // check multiple roles

// Require authentication
requireLogin()           // Redirect to login nếu chưa đăng nhập
requireRole('admin')     // Redirect if not admin
requireAnyRole(['admin', 'staff'])  // Require one of roles

// Display conditional content
showIfRole('admin', $html)
showIfAnyRole(['admin', 'staff'], $html)

// Get role info
getRoleName('admin')    // "Quản trị viên"
getRoleBadge('staff')   // HTML badge
```

---

### **2. [`api_gateway.php`](api_gateway.php )**
API Gateway đơn giản với role checking:

```
GET    /api_gateway.php/users              (Admin only)
GET    /api_gateway.php/users/:id          (Anyone)
POST   /api_gateway.php/users              (Admin only)
DELETE /api_gateway.php/users/:id          (Admin only)

GET    /api_gateway.php/products           (Anyone)
POST   /api_gateway.php/products           (Admin/Staff)

GET    /api_gateway.php/warehouses         (Anyone)
GET    /api_gateway.php/customers          (Anyone)

GET    /api_gateway.php/health             (Anyone)
```

---

## 🛠️ **Cách Sử Dụng**

### **Trong PHP Pages**

```php
<?php
session_start();
require_once 'role_helper.php';

// Check if admin
if (isAdmin()) {
    echo "Bạn là admin";
}

// Require admin access
requireRole('admin');

// Show admin menu
showIfRole('admin', '<a href="admin.php">Admin Panel</a>');

// Check staff or admin
if (hasAnyRole(['admin', 'staff'])) {
    echo "Bạn là admin hoặc staff";
}
?>
```

---

### **Trong HTML**

```html
<?php if (isLoggedIn()): ?>
    <p>Xin chào: <?= getCurrentUser()['fullname'] ?></p>
    <p>Role: <?= getRoleName(getCurrentRole()) ?></p>
<?php endif; ?>

<?php showIfRole('admin', '
    <a href="admin.php">Admin Panel</a>
'); ?>
```

---

### **Trong Login Form**

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy user từ database
    $stmt = $pdo->prepare("SELECT * FROM Nguoidung WHERE Tendangnhap = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['Matkhau'])) {
        // Set session với role
        $_SESSION['user'] = [
            'id' => $user['Manv'],
            'username' => $user['Tendangnhap'],
            'fullname' => $user['Hovaten'],
            'email' => $user['Email'],
            'role' => $user['Vaitro']  // 'admin', 'staff', hoặc 'guest'
        ];
        
        header('Location: trangchu.php');
        exit;
    }
}
?>
```

---

## 🔑 **Quyền Chi Tiết**

### **Admin (admin)**
```
✅ Quản lý người dùng (CRUD)
✅ Quản lý sản phẩm (CRUD)
✅ Quản lý kho (CRUD)
✅ Xem báo cáo
✅ Xem audit log
```

### **Staff (staff)**
```
✅ Xem danh sách người dùng
✅ Thêm sản phẩm
✅ Thêm phiếu nhập/xuất
❌ Xóa người dùng
❌ Xóa sản phẩm
```

### **Guest (guest)**
```
✅ Xem danh sách sản phẩm
✅ Xem giá
❌ Thêm/Sửa/Xóa
❌ Xem kho
```

---

## 📊 **API Response Examples**

### **SUCCESS**
```json
{
  "success": true,
  "message": "Users retrieved",
  "data": {
    "users": [
      {"Manv": "001", "Tendangnhap": "admin", "Vaitro": "admin"}
    ]
  },
  "timestamp": "2026-03-31T22:00:00+07:00"
}
```

### **UNAUTHORIZED**
```json
{
  "success": false,
  "message": "Unauthorized: Admin only",
  "data": null,
  "timestamp": "2026-03-31T22:00:00+07:00"
}
```

---

## 🚀 **Triển Khai Trên XAMPP**

### **Bước 1: Copy Files**
```bash
cp role_helper.php d:\xampp\htdocs\vlxd\
cp api_gateway.php d:\xampp\htdocs\vlxd\
```

### **Bước 2: Update Login (dangnhap.php)**
```php
require_once 'role_helper.php';

if ($isValid) {
    $_SESSION['user'] = [
        'id' => $user['Manv'],
        'username' => $user['Tendangnhap'],
        'email' => $user['Email'],
        'fullname' => $user['Hovaten'],
        'role' => $user['Vaitro']  // Add this line
    ];
}
```

### **Bước 3: Protect Pages**
```php
<?php
session_start();
require_once 'role_helper.php';

// Require admin access
requireRole('admin');

// Rest of your code...
?>
```

### **Bước 4: Update Database**
```sql
-- Add sample data with roles
INSERT INTO Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, Vaitro)
VALUES 
('001', 'admin', PASSWORD('admin123'), 'Admin User', 'admin@vlxd.com', 'admin'),
('002', 'staff', PASSWORD('staff123'), 'Staff User', 'staff@vlxd.com', 'staff'),
('003', 'guest', PASSWORD('guest123'), 'Guest User', 'guest@vlxd.com', 'guest');
```

---

## 📞 **Liên Hệ & Support**

Các hàm helper dễ sử dụng - chỉ cần import `role_helper.php` vào page nào muốn check role!

---

**Happy Coding! 🎉**
