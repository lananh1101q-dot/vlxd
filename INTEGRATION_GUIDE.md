# 🔐 HƯỚNG DẪN PHÂN QUYỀN 3 ROLES - VLXD

## ✅ **Tôi Vừa Tạo Cho Bạn**

| File | Mục Đích | Dùng Cho |
|------|---------|----------|
| **[`role_helper.php`](role_helper.php )** | Functions check role | Import vào mỗi page |
| **[`api_gateway.php`](api_gateway.php )** | API đơn giản + check role | Gọi API từ frontend |
| **[`setup_roles.sql`](setup_roles.sql )** | Sample data (admin/staff/guest) | Chạy trong phpMyAdmin |
| **[`dashboard_role_example.php`](dashboard_role_example.php )** | Example page | Tham khảo cách dùng |
| **[`ROLE_SYSTEM.md`](ROLE_SYSTEM.md )** | Tài liệu chi tiết | Đọc để hiểu rõ |

---

## 🚀 **Quick Start - 3 Bước**

### **Bước 1: Chạy SQL Setup**

1. Mở phpMyAdmin: `http://localhost/phpmyadmin/`
2. Chọn database `vlxd`
3. Click tab **SQL**
4. Dán toàn bộ nội dung file [`setup_roles.sql`](setup_roles.sql )
5. Click **Execute** ✅

**Accounts được tạo:**
```
👨‍💼 Admin:     admin / admin123
👨‍⚙️ Staff:     staff / staff123
👤 Guest:     guest / guest123
```

---

### **Bước 2: Update File Hiện Tại**

#### **2A. Sửa [`dangnhap.php`](dangnhap.php )**

Tìm dòng này (khoảng line 30):
```php
if ($isValid) {
    $_SESSION['user'] = [
        'id' => $user['Manv'],
        'username' => $user['Tendangnhap'],
        'email' => $user['Email'],
        'fullname' => $user['Hovaten'],
        // Thêm dòng này:
        'role' => $user['Vaitro']  // 'admin', 'staff', hoặc 'guest'
    ];
```

#### **2B. Sửa [`trangchu.php`](trangchu.php ) (Dashboard)**

Thêm ở đầu file:
```php
<?php
session_start();
require_once 'role_helper.php';

// Bắt buộc login
requireLogin();

// Nếu muốn chỉ admin
// requireRole('admin');
?>
```

---

### **Bước 3: Test Thử**

1. Mở trình duyệt
2. Truy cập: `http://localhost/vlxd/dangnhap.php`
3. Đăng nhập bằng:
   - **Admin**: admin / admin123
   - **Staff**: staff / staff123
   - **Guest**: guest / guest123
4. Xem trang [`dashboard_role_example.php`](dashboard_role_example.php ) để hiểu

---

## 📚 **Cách Dùng Trong Code**

### **1. Check Nếu Là Admin**

```php
<?php
require_once 'role_helper.php';

if (isAdmin()) {
    echo "Bạn là admin!";
}
?>
```

### **2. Hiện/Ẩn Menu Theo Role**

```php
<!-- Menu chỉ admin thấy -->
<?php showIfRole('admin', '
    <li><a href="users.php">Quản Lý Người Dùng</a></li>
'); ?>

<!-- Menu staff/admin thấy -->
<?php showIfAnyRole(['admin', 'staff'], '
    <li><a href="phieu_nhap.php">Lập Phiếu Nhập</a></li>
'); ?>
```

### **3. Bắt Buộc Role Trước Khi Vào Page**

```php
<?php
session_start();
require_once 'role_helper.php';

// Bắt buộc phải admin, nếu không -> error
requireRole('admin');

// Hoặc: bắt buộc staff hoặc admin
requireAnyRole(['admin', 'staff']);

// Nếu muốn tự xử lý lỗi
if (!isAdmin()) {
    http_response_code(403);
    die('Bạn không có quyền truy cập!');
}
?>
```

### **4. Lấy Thông Tin User**

```php
<?php
$user = getCurrentUser();  // Array ['id', 'username', 'fullname', 'email', 'role']
$role = getCurrentRole();  // String 'admin', 'staff', 'guest'
$name = getRoleName($role);  // "Quản trị viên", "Nhân viên", "Khách"
?>
```

---

## 🔌 **API Gateway Usage**

### **Gọi API từ JavaScript**

```javascript
// Xem danh sách users (Admin only)
fetch('http://localhost/vlxd/api_gateway.php/users')
    .then(r => r.json())
    .then(data => console.log(data));

// Tạo sản phẩm (Staff/Admin)
fetch('http://localhost/vlxd/api_gateway.php/products', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        Masp: 'SP001',
        Tensp: 'Sản phẩm test',
        Dvt: 'mét',
        Giaban: 100000
    })
})
.then(r => r.json())
.then(data => console.log(data));
```

---

## 🎯 **Phân Chia Công Việc 5 Người**

| Người | Chắp Nhậm | Chức Năng |
|-------|---------|---------|
| **1** | User Service | Login, Users CRUD, Check roles |
| **2** | Product Service | Sản phẩm, Danh mục, NCC |
| **3** | Warehouse Service | Phiếu nhập/xuất, Tồn kho |
| **4** | Customer Service | Khách hàng, Loại KH, Giao hàng |
| **5** | Manufacturing | Lệnh SX, Dashboard, Báo cáo |

**Mỗi người:**
- ✅ Dùng `role_helper.php` để check role
- ✅ Dùng `api_gateway.php` để test API
- ✅ Protect pages bằng `requireRole()` hoặc `requireAnyRole()`

---

## 📊 **Quy Tắc Có Quyền (Authorization)**

### **Admin (Toàn Quyền)**
- ✅ Create, Read, Update, Delete - TẤT CẢ
- ✅ Xem logs
- ✅ Quản lý users

### **Staff (Nhân Viên)**
- ✅ Create, Read, Update - Dữ liệu
- ✅ Read - Users (chỉ xem, không sửa)
- ❌ Delete - Tất cả
- ❌ Quản lý users

### **Guest (Chưa Đăng Nhập)**
- ✅ Read - Products, Warehouses
- ❌ Create, Update, Delete
- ❌ Xem sensitive data

---

## 🔐 **Database Schema - Vaitro Column**

```sql
-- Trong bảng Nguoidung, cột Vaitro có thể có giá trị:
-- 'admin'  - Quản trị viên
-- 'staff'  - Nhân viên
-- 'guest'  - Khách (hiếm khi dùng, thường là session chưa login)

SELECT * FROM Nguoidung WHERE Vaitro IN ('admin', 'staff', 'guest');
```

---

## 🧪 **Testing Checklist**

- [ ] Login với admin -> Thấy All Menu
- [ ] Login với staff -> Thấy Staff Menu
- [ ] Không login -> Redirect to login
- [ ] Thử vào `/api_gateway.php/users` mà không admin -> 403 Error
- [ ] Thử `/api_gateway.php/products` không login -> Vẫn xem được

---

## ❓ **Troubleshooting**

### ❌ "Undefined function isAdmin()"
→ Quên `require_once 'role_helper.php'` ở đầu file

### ❌ "Undefined index: role"
→ Session user không có 'role' key. Kiểm tra login có set `$_SESSION['user']['role']` không

### ❌ "Access Denied" không có message
→ Sửa thành:
```php
if (!hasRole('admin')) {
    http_response_code(403);
    die('Bạn không có quyền!');
}
```

---

## 📞 **Support**

Để thêm/sửa roles, edit trong [`role_helper.php`](role_helper.php ) hoặc [`ROLE_SYSTEM.md`](ROLE_SYSTEM.md )

---

**Happy Coding! 🎉**
