# 🎯 **QUICK REFERENCE - PHÂN QUYỀN 3 ROLES**

## 🔐 **3 Roles & Accounts Test**

```
👨‍💼 ADMIN   | user: admin   | pass: admin123
👨‍⚙️ STAFF   | user: staff   | pass: staff123
👤 GUEST   | user: guest   | pass: guest123
```

---

## 📝 **Setup - 3 Bước (5 Phút)**

```bash
# 1. Chạy SQL (phpMyAdmin)
setup_roles.sql

# 2. Thêm vào dangnhap.php (line ~40)
'role' => $user['Vaitro']

# 3. Thêm vào top mỗi protected page
<?php require_once 'role_helper.php'; ?>
```

---

## 💻 **Code Snippets**

### **Protect Page (Bắt Buộc Login)**
```php
<?php
require_once 'role_helper.php';
requireLogin();  // Redirect to login if not logged in
?>
```

### **Protect Page (Admin Only)**
```php
<?php
require_once 'role_helper.php';
requireRole('admin');  // Redirect if not admin
?>
```

### **Check Role in HTML**
```php
<?php if (isAdmin()): ?>
    <p>Admin-only content</p>
<?php endif; ?>
```

### **Show/Hide Menu**
```php
<?php showIfRole('admin', '<li>Admin Menu</li>'); ?>
<?php showIfAnyRole(['admin', 'staff'], '<li>Staff Menu</li>'); ?>
```

### **Get User Info**
```php
<?php
$user = getCurrentUser();      // Array
$role = getCurrentRole();      // 'admin', 'staff', 'guest'
$name = getRoleName($role);    // "Quản trị viên"
$badge = getRoleBadge($role);  // HTML badge
?>
```

---

## 🔌 **API Calls**

### **List Users (Admin Only)**
```javascript
fetch('api_gateway.php/users')
  .then(r => r.json())
  .then(d => console.log(d.data.users));
```

### **List Products (Anyone)**
```javascript
fetch('api_gateway.php/products')
  .then(r => r.json())
  .then(d => console.log(d.data.products));
```

### **Create Product (Staff/Admin)**
```javascript
fetch('api_gateway.php/products', {
  method: 'POST',
  headers: {'Content-Type': 'application/json'},
  body: JSON.stringify({
    Masp: 'SP001',
    Tensp: 'Product Name',
    Dvt: 'mét',
    Giaban: 100000
  })
})
.then(r => r.json())
.then(d => console.log(d));
```

---

## 📂 **Files Reference**

| File | Chứa Gì |
|------|---------|
| `role_helper.php` | ALL helper functions |
| `api_gateway.php` | API + role check |
| `setup_roles.sql` | Sample data |
| `ROLE_SYSTEM.md` | Full docs |
| `INTEGRATION_GUIDE.md` | How to integrate |
| `dashboard_role_example.php` | Example page |

---

## 🧠 **Remember**

1. ✅ Import `role_helper.php` vào **mỗi** page cần check role
2. ✅ Set `'role'` trong `$_SESSION['user']` khi login
3. ✅ Dùng `requireRole()` để protect pages
4. ✅ Dùng `showIfRole()` để show/hide content
5. ✅ Test với 3 accounts khác nhau

---

## 🐛 **Common Errors & Fix**

| Lỗi | Sửa |
|-----|-----|
| "Undefined function isAdmin()" | Add: `require_once 'role_helper.php';` |
| Role không set | Kiểm tra login đã set `'role' => $user['Vaitro']` chưa |
| 403 Forbidden | Không phải admin/staff truy cập admin-only page |
| Redirect loop | Kiểm tra lại `requireLogin()` position |

---

## 🚀 **Start Here**

1. Run `setup_roles.sql` → Database ready ✅
2. Update `dangnhap.php` → Login ready ✅
3. Add `require_once 'role_helper.php';` to pages → Protection ready ✅
4. Test with admin/staff/guest accounts ✅
5. Copy examples từ `dashboard_role_example.php` ✅

---

**Let's Go! 🎉**
