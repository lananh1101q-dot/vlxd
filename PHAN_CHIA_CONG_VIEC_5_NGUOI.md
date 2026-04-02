# 📋 PHÂN CHIA CÔNG VIỆC MICROSERVICES - 5 NGƯỜI

## 🎯 Tổng Quan

Dựa trên **các chức năng hiện tại** của hệ thống VLXD, phân chia 5 người, mỗi người 3 chức năng để đổi thành Microservices.

---

## 👥 **PHÂN CHIA 5 NGƯỜI**

---

## **NGƯỜI 1: USER SERVICE - Quản Lý Người Dùng & Xác Thực**
**Người phụ trách:** ...  
**Service:** `user-service/`  
**Port:** 3001

### 3 Chức Năng:
1. **✅ Đăng Nhập (Đăng Xuất)**
   - Route: `POST /api/v1/auth/login`
   - Input: username, password
   - Output: JWT token, user info
   - Database: `users` table
   
2. **✅ Quản Lý Tài Khoản Người Dùng**
   - Route: `GET/POST/PUT/DELETE /api/v1/users`
   - CRUD người dùng (thêm, sửa, xóa, xem)
   - Database: `users` table
   
3. **✅ Phân Quyền (Role-based)**
   - Routes: 
     - `GET /api/v1/users/:id` (xem quyền)
     - `PUT /api/v1/users/:id` (cập nhật quyền)
   - Available roles: `admin`, `manager`, `staff`
   - Database: `users.Vaitro` field

### Files Cần Tạo:
- `services/user-service/index.php` (API routes)
- `services/user-service/UserService.php` (business logic)
- `shared/middleware/JWTMiddleware.php` (token handler)

### Testing:
```bash
curl -X POST http://localhost:3001/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

---

## **NGƯỜI 2: PRODUCT SERVICE - Quản Lý Sản Phẩm & Danh Mục**
**Người phụ trách:** ...  
**Service:** `product-service/`  
**Port:** 3002

### 3 Chức Năng:
1. **✅ Quản Lý Sản Phẩm**
   - Routes: `GET/POST/PUT/DELETE /api/v1/products`
   - CRUD sản phẩm (Masp, Tensp, Giaban, Dvt, Mota)
   - Database: `sanpham` table
   - File cũ: `taosanpham.php`, `suasp.php`, `xoasp.php`

2. **✅ Quản Lý Danh Mục Sản Phẩm**
   - Routes: `GET/POST/PUT/DELETE /api/v1/categories`
   - CRUD danh mục (Madm, Tendm, Mota)
   - Database: `danhmucsanpham` table
   - File cũ: `taodmsp.php`, `suadmsp.php`, `xoadmsp.php`

3. **✅ Quản Lý Nhà Cung Cấp**
   - Routes: `GET/POST/PUT/DELETE /api/v1/suppliers`
   - CRUD nhà cung cấp (Mancc, Tenncc, Diachi, Sdt)
   - Database: `nhacungcap` table
   - File cũ: `taoncc.php`, `suancc.php`, `xoancc.php`

### Files Cần Tạo:
- `services/product-service/index.php` (API routes)
- `services/product-service/ProductService.php` (business logic)
- Database: 3 tables (sanpham, danhmucsanpham, nhacungcap)

### Testing:
```bash
# Thêm sản phẩm
curl -X POST http://localhost:3002/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"Masp":"SP001","Tensp":"Vải cotton","Giaban":50000,"Dvt":"mét"}'
```

---

## **NGƯỜI 3: WAREHOUSE SERVICE - Quản Lý Kho & Tồn Kho**
**Người phụ trách:** ...  
**Service:** `warehouse-service/`  
**Port:** 3003

### 3 Chức Năng:
1. **✅ Phiếu Nhập Kho**
   - Routes: `GET/POST/PUT/DELETE /api/v1/import`
   - CRUD phiếu nhập (Maphieu, Makho, Mancc, Ngaynhap, Tongtien)
   - Database: `phieunhap`, `chitietphieunhap` tables
   - File cũ: `phieu_nhap.php`, `chi_tiet_phieu_nhap.php`

2. **✅ Phiếu Xuất Kho**
   - Routes: `GET/POST/PUT/DELETE /api/v1/export`
   - CRUD phiếu xuất (Maphieu, Makho, Makh, Ngayxuat)
   - Database: `phieuxuat`, `chitietphieuxuat` tables
   - File cũ: `phieu_xuat.php`, `chi_tiet_phieu_xuat.php`

3. **✅ Tồn Kho & Điều Chuyển**
   - Routes: 
     - `GET /api/v1/inventory` (xem tồn kho)
     - `POST /api/v1/transfer` (chuyển kho)
   - CRUD điều chuyển (Maphieu, Makho_from, Makho_to, Ngaychuyển)
   - Database: `tonkho`, `phieudieuchuyen` tables
   - File cũ: `tonkho.php`, `phieu_dieuchuyen.php`

### Files Cần Tạo:
- `services/warehouse-service/index.php` (API routes)
- `services/warehouse-service/WarehouseService.php` (business logic)
- Database: 6 tables (phieunhap, chitietphieunhap, phieuxuat, chitietphieuxuat, phieudieuchuyen, tonkho)

### Testing:
```bash
# Lấy danh sách import
curl http://localhost:3003/api/v1/import \
  -H "Authorization: Bearer {token}"
```

---

## **NGƯỜI 4: CUSTOMER SERVICE - Quản Lý Khách Hàng & Đơn Hàng**
**Người phụ trách:** ...  
**Service:** `customer-service/`  
**Port:** 3004

### 3 Chức Năng:
1. **✅ Quản Lý Khách Hàng**
   - Routes: `GET/POST/PUT/DELETE /api/v1/customers`
   - CRUD khách hàng (Makh, Tenkh, Diachi, Sdt, Madkh)
   - Database: `khachhang` table
   - File cũ: `khachhang.php`, `them_khachhang.php`, `sua_khachhang.php`

2. **✅ Quản Lý Loại Khách Hàng**
   - Routes: `GET/POST/PUT/DELETE /api/v1/customer-types`
   - CRUD loại khách hàng (Madkh, Tendkh, Mota)
   - Database: `danhmuckh` table
   - File cũ: `loaikhachhang.php`, `them_loaikhachhang.php`

3. **✅ Quản Lý Giao Hàng (Lệnh)**
   - Routes: `GET/POST/PUT/DELETE /api/v1/shipments`
   - CRUD lệnh giao hàng (Malenh, Makh, Makho, Ngaygiao, Trangthai)
   - Database: `lenh_giaohang` table (nếu có) hoặc `phieuxuat`
   - File cũ: (có thể mở rộng từ phieuxuat)

### Files Cần Tạo:
- `services/customer-service/index.php` (API routes)
- `services/customer-service/CustomerService.php` (business logic)
- Database: 3 tables (khachhang, danhmuckh, lenh_giaohang - tạo mới)

### Testing:
```bash
# Lấy danh sách khách hàng
curl http://localhost:3004/api/v1/customers \
  -H "Authorization: Bearer {token}"
```

---

## **NGƯỜI 5: MANUFACTURING & ANALYTICS - Sản Xuất & Báo Cáo**
**Người phụ trách:** ...  
**Services:** `manufacturing-service/` + `analytics-service/`  
**Port:** 3005 (manufacturing) + 3006 (analytics)

### 3 Chức Năng:

1. **✅ Quản Lý Lệnh Sản Xuất**
   - Service: manufacturing-service (Port 3005)
   - Routes: `GET/POST/PUT/DELETE /api/v1/production`
   - CRUD lệnh sản xuất (Malenh, Masp, Soluong, Ngaybd, Ngaykt, Trangthai)
   - Database: `lengsanxuat`, `chitiet_lengsanxuat` tables
   - File cũ: `lenh_san_xuat.php`, `danh_sach_lenh_san_xuat.php`, `hoan_thanh_san_xuat.php`

2. **✅ Quản Lý Chi Tiết Sản Xuất (Steps)**
   - Routes: `GET/POST/PUT/DELETE /api/v1/production/:id/steps`
   - CRUD các bước sản xuất (Mabh, Malenh, Nobh, Tenbuoc, Soluong, Thoigian, Trangthai)
   - Database: `chitiet_lengsanxuat` table

3. **✅ Báo Cáo & Thống Kê Dashboard**
   - Service: analytics-service (Port 3006)
   - Routes: 
     - `GET /api/v1/reports/dashboard` (dashboard metrics)
     - `GET /api/v1/reports/inventory` (báo cáo tồn kho)
     - `GET /api/v1/reports/production` (báo cáo sản xuất)
   - Features: Tổng hợp dữ liệu, metrics, biểu đồ
   - Database: `tonkho_nvl`, `tonkho_sp`, `lengsanxuat` (đọc từ các services khác)

### Files Cần Tạo:
- `services/manufacturing-service/index.php` (API routes)
- `services/manufacturing-service/ManufacturingService.php` (business logic)
- `services/analytics-service/index.php` (API routes)
- `services/analytics-service/AnalyticsService.php` (business logic)
- Database: 3 tables (lengsanxuat, chitiet_lengsanxuat, tonkho_nvl, tonkho_sp)

### Testing:
```bash
# Lấy lệnh sản xuất
curl http://localhost:3005/api/v1/production \
  -H "Authorization: Bearer {token}"

# Dashboard
curl http://localhost:3006/api/v1/reports/dashboard \
  -H "Authorization: Bearer {token}"
```

---

## 🔗 **API GATEWAY - MỌI NGƯỜI DÙNG CHUNG**
**File:** `api-gateway/index.php` (1 file duy nhất)
**Port:** 8000

**Chức năng:**
- Nhận tất cả requests từ frontend
- Route đến services tương ứng
- Validate JWT token
- Return responses đúng format

**Routes chính:**
```
POST   /api/v1/auth/login            → user-service
GET    /api/v1/products              → product-service
POST   /api/v1/import                → warehouse-service
GET    /api/v1/customers             → customer-service
POST   /api/v1/production            → manufacturing-service
GET    /api/v1/reports/dashboard     → analytics-service
```

---

## 📊 **LỊCH TRÌNH 2-3 TUẦN**

| Tuần | Công Việc |
|------|-----------|
| **Tuần 1 - Ngày 1-2** | Setup database, tạo base structure |
| **Tuần 1 - Ngày 3-5** | Mỗi người code API routes + business logic |
| **Tuần 2 - Ngày 1-3** | Tạo API Gateway, test từng service |
| **Tuần 2 - Ngày 4-5** | Integration testing, fix bugs |
| **Tuần 3** | Documentation, deployment guide |

---

## 📁 **CẤU TRÚC FOLDER**

```
vlxd/
├── api-gateway/
│   └── index.php              (API Gateway - MỌI NGƯỜI DÙNG)
├── services/
│   ├── user-service/          (NGƯỜI 1)
│   │   ├── index.php
│   │   └── UserService.php
│   ├── product-service/       (NGƯỜI 2)
│   │   ├── index.php
│   │   └── ProductService.php
│   ├── warehouse-service/     (NGƯỜI 3)
│   │   ├── index.php
│   │   └── WarehouseService.php
│   ├── customer-service/      (NGƯỜI 4)
│   │   ├── index.php
│   │   └── CustomerService.php
│   ├── manufacturing-service/ (NGƯỜI 5)
│   │   ├── index.php
│   │   └── ManufacturingService.php
│   └── analytics-service/     (NGƯỜI 5)
│       ├── index.php
│       └── AnalyticsService.php
├── shared/
│   ├── config/
│   │   └── database.php
│   └── middleware/
│       └── JWTMiddleware.php
├── database.sql               (Database schema cho tất cả)
├── db.php                     (Database connection)
└── (tất cả file cũ giữ nguyên)
```

---

## 🧪 **TESTING CHECKLIST**

### Bước 1: Setup Database
```bash
# Import database schema
mysql -u root < database.sql
```

### Bước 2: Mỗi Người Test Service Riêng
```bash
# Người 1 test user-service
curl http://localhost:3001/api/v1/users

# Người 2 test product-service
curl http://localhost:3002/api/v1/products

# ... v.v
```

### Bước 3: Test API Gateway
```bash
curl http://localhost:8000/api/v1/products
```

### Bước 4: Test Frontend Integration
```
http://localhost/vlxd/login.php (or index.php)
```

---

## 📞 **QUYÊN HẠN & TRÁCH NHIỆM**

| Người | Service | Quyền Hạn | Trách Nhiệm |
|-------|---------|-----------|------------|
| 1 | user-service | auth/* | Login, users CRUD, roles |
| 2 | product-service | products/*, categories/*, suppliers/* | Products, categories, suppliers |
| 3 | warehouse-service | import/*, export/*, inventory/*, transfer/* | Import, export, inventory |
| 4 | customer-service | customers/*, customer-types/*, shipments/* | Customers, types, shipments |
| 5 | manufacturing-service + analytics-service | production/*, reports/* | Production, analytics |
| All | api-gateway | (routing) | Integration, deployment |

---

## ✅ **TIÊU CHÍ THÀNH CÔNG**

- ✅ Mỗi service hoàn thiện 3 chức năng
- ✅ Tất cả API endpoints working
- ✅ JWT token authentication working
- ✅ Database sync không lỗi
- ✅ API Gateway routing đúng
- ✅ Frontend kết nối được API
- ✅ Tất cả CRUD operations working
- ✅ Documentation hoàn chỉnh

---

**Ready? Ai là người 1, 2, 3, 4, 5? 👥**
