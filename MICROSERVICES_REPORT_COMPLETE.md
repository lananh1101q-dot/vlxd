# BÁO CÁO THIẾT KẾ KIẾN TRÚC MICROSERVICES
## Hệ Thống Quản Lý Kho VLXD (Vận Lý Xưởng Dệt)

**Người lập báo cáo**: Nhóm phát triển  
**Ngày lập**: 31/03/2026  
**Phiên bản**: 2.0  
**Trạng thái**: Hoàn chỉnh

---

## MỤC LỤC

1. [Giới thiệu](#giới-thiệu)
2. [Phân chia công việc](#phân-chia-công-việc)
3. [Biểu đồ gói (Package Diagram)](#biểu-đồ-gói)
4. [Biểu đồ thành phần (Component Diagram)](#biểu-đồ-thành-phần)
5. [Biểu đồ triển khai (Deployment Diagram)](#biểu-đồ-triển-khai)
6. [Lịch trình thực hiện](#lịch-trình-thực-hiện)
7. [Công nghệ sử dụng](#công-nghệ-sử-dụng)
8. [Rủi ro và giải pháp](#rủi-ro-và-giải-pháp)
9. [Tiêu chí thành công](#tiêu-chí-thành-công)
10. [Kết luận](#kết-luận)

---

## GIỚI THIỆU

### Bối cảnh dự án
Dự án này chuyển đổi hệ thống Quản lý Kho VLXD từ kiến trúc monolithic sang **Microservices đơn giản**. Tập trung vào **core functionalities**:
- ✓ Chia thành 5 services độc lập
- ✓ Mỗi service có 1 database riêng
- ✓ Giao tiếp qua API REST
- ✓ **Không cần Kubernetes**, chạy bình thường trên PHP/XAMPP
- ✓ Dễ bảo trì, easy scaling

### Mục tiêu
1. ✓ Tách hệ thống thành **5 microservices** đơn giản
2. ✓ Mỗi service có **database riêng** (MySQL)
3. ✓ Các services giao tiếp qua **API REST đơn giản**
4. ✓ Triển khai trên **PHP native** (không framework phức tạp)
5. ✓ Hoàn thành trong **2-3 tuần**

### Phạm vi
- **5 thành viên nhóm**
- **Mỗi thành viên**: 3 chức năng đơn giản
- **Thời gian**: 2-3 tuần (easy to manage)
- **Quy mô**: ~400 giờ công

---

## PHÂN CHIA CÔNG VIỆC

### Tổng quan công việc

| STT | Thành viên | Services Chính | Chức năng | Giờ công | Tuần |
|-----|-----------|----------------|----------|---------|------|
| 1 | **Thành viên 1** | User Service | Login, User CRUD, Profile | 60 | 2-3 |
| 2 | **Thành viên 2** | Product Service | Product, Category, Material | 60 | 2-3 |
| 3 | **Thành viên 3** | Warehouse Service | Import, Export, Inventory | 65 | 2-3 |
| 4 | **Thành viên 4** | Customer Service | Customer, Customer Type, Delivery | 60 | 2-3 |
| 5 | **Thành viên 5** | Manufacturing + Analytics | Production Order, Details, Reports | 65 | 2-3 |
| Chung | **Tất cả** | API Gateway, Testing, Documentation | Công việc chung | 90 | 1-2 |

**Tổng cộng**: ~400 giờ công ≈ 2-3 tuần (hoặc 2 người/tuần nếu làm song song)

---

### CHI TIẾT CÔNG VIỆC CỦA TỪNG THÀNH VIÊN

#### **THÀNH VIÊN 1: USER SERVICE (Đơn Giản)**

**Mô tả**: Xây dựng hệ thống quản lý người dùng và xác thực cơ bản.

**3 Chức năng**:
1. **Đăng nhập & Đăng ký**: Login, Register (không RBAC)
2. **Quản lý Nhân Viên**: CRUD nhân viên, thông tin cơ bản
3. **Quản lý Profile**: Cập nhật profile, đổi mật khẩu

**Bảng dữ liệu**:
```
- Nguoidung (Manv, Tendangnhap, Matkhau, Hovaten, Email, NgayTao, TrangThai)
```

**10 API Endpoints**:
- POST /api/v1/auth/login - Đăng nhập
- POST /api/v1/auth/register - Đăng ký
- GET /api/v1/users - Danh sách nhân viên
- POST /api/v1/users - Thêm nhân viên
- GET /api/v1/users/:id - Chi tiết nhân viên
- PUT /api/v1/users/:id - Sửa nhân viên
- DELETE /api/v1/users/:id - Xóa nhân viên
- PUT /api/v1/profile - Cập nhật profile
- PUT /api/v1/change-password - Đổi mật khẩu

**Công việc chi tiết**:
- Design database schema: 2h
- Implement login/register: 12h
- Implement user CRUD: 15h
- Implement profile management: 10h
- Unit tests: 15h
- API documentation: 5h

**Công cụ**:
- PHP native (không framework)
- MySQL 8.0
- JWT (simple token)
- Port: 3001

**Deliverables**:
✓ User Service chạy
✓ API documentation
✓ Unit tests

---

#### **THÀNH VIÊN 2: PRODUCT SERVICE (Đơn Giản)**

**Mô tả**: Xây dựng hệ thống quản lý sản phẩm, danh mục, nguyên vật liệu.

**3 Chức năng**:
1. **Quản lý sản phẩm**: CRUD sản phẩm, giá bán, đơn vị tính
2. **Quản lý danh mục**: CRUD danh mục sản phẩm
3. **Quản lý nguyên vật liệu**: CRUD NVL, nhà cung cấp, giá vốn

**Bảng dữ liệu**:
```
- Sanpham (Masp, Tensp, Madm, Dvt, Giaban, Mota, NgayTao, TrangThai)
- Danhmucsp (Madm, Tendm, Mota)
- Nguyenvatlieu (Manvl, Tennvl, Dvt, Giavon, Mancc)
- Nhacungcap (Mancc, Tenncc, Sdtncc, Email)
```

**12 API Endpoints**:
- GET /api/v1/products - Danh sách sản phẩm
- POST /api/v1/products - Thêm sản phẩm
- PUT /api/v1/products/:id - Sửa sản phẩm
- DELETE /api/v1/products/:id - Xóa sản phẩm
- GET /api/v1/categories - Danh sách danh mục
- POST /api/v1/categories - Thêm danh mục
- PUT /api/v1/categories/:id - Sửa danh mục
- GET /api/v1/materials - Danh sách NVL
- POST /api/v1/materials - Thêm NVL
- PUT /api/v1/materials/:id - Sửa NVL
- GET /api/v1/suppliers - Danh sách NCC
- POST /api/v1/suppliers - Thêm NCC

**Công việc chi tiết**:
- Design database: 2h
- Implement product CRUD: 15h
- Implement category CRUD: 10h
- Implement material CRUD: 12h
- Unit tests: 15h
- API documentation: 5h

**Công cụ**:
- PHP native
- MySQL 8.0
- Port: 3002

---

#### **THÀNH VIÊN 3: WAREHOUSE SERVICE (Đơn Giản)**

**Mô tả**: Xây dựng hệ thống quản lý phiếu nhập, xuất hàng và tồn kho.

**3 Chức năng**:
1. **Quản lý phiếu nhập**: CRUD phiếu nhập, chi tiết phiếu
2. **Quản lý phiếu xuất**: CRUD phiếu xuất, chi tiết phiếu
3. **Quản lý tồn kho**: Cập nhật tồn, xem tồn kho

**Bảng dữ liệu**:
```
- Phieunhap (Manhaphang, Mancc, Makho, Ngaynhaphang, Tongtiennhap, Trangthai)
- Chitiet_Phieunhap (Id, Manhaphang, Manvl, Soluong, Dongianhap)
- Phieuxuat (Maxuathang, Makh, Makho, Ngayxuat, Tongtienxuat, Trangthai)
- Chitiet_Phieuxuat (Id, Maxuathang, Masp, Soluong, Dongiaxuat)
- Tonkho_sp (Makho, Masp, Soluongton)
- Tonkho_nvl (Makho, Manvl, Soluongton)
```

**12 API Endpoints**:
- GET /api/v1/import-receipts - Danh sách phiếu nhập
- POST /api/v1/import-receipts - Thêm phiếu nhập
- GET /api/v1/export-receipts - Danh sách phiếu xuất
- POST /api/v1/export-receipts - Thêm phiếu xuất
- GET /api/v1/inventory - Tồn kho
- PUT /api/v1/inventory/:id - Cập nhật tồn
- GET /api/v1/inventory/product/:id - Tồn theo SP
- GET /api/v1/inventory/material/:id - Tồn theo NVL

**Công việc chi tiết**:
- Design database: 3h
- Implement import CRUD: 12h
- Implement export CRUD: 12h
- Implement inventory: 15h
- Unit tests: 15h
- API documentation: 5h

**Công cụ**:
- PHP native
- MySQL 8.0
- Port: 3003

---

#### **THÀNH VIÊN 4: CUSTOMER SERVICE (Đơn Giản)**

**Mô tả**: Xây dựng hệ thống quản lý khách hàng và loại khách hàng.

**3 Chức năng**:
1. **Quản lý khách hàng**: CRUD khách hàng, thông tin liên hệ
2. **Quản lý loại khách hàng**: CRUD loại KH, mô tả
3. **Quản lý lệnh giao hàng**: CRUD lệnh pickup/delivery

**Bảng dữ liệu**:
```
- Khachhang (Makh, Tenkh, Sdtkh, Diachikh, Maloaikh, Email, NgayDangKy, TrangThai)
- Loaikhachhang (Maloaikh, Tenloaikh, Mota)
- Lenhgiaohang (Malenh, Makh, Ngaygiao, Diachigiao, Trangthai)
```

**12 API Endpoints**:
- GET /api/v1/customers - Danh sách KH
- POST /api/v1/customers - Thêm KH
- PUT /api/v1/customers/:id - Sửa KH
- DELETE /api/v1/customers/:id - Xóa KH
- GET /api/v1/customer-types - Danh sách loại KH
- POST /api/v1/customer-types - Thêm loại KH
- GET /api/v1/delivery-orders - Danh sách lệnh
- POST /api/v1/delivery-orders - Thêm lệnh
- PUT /api/v1/delivery-orders/:id - Sửa lệnh
- DELETE /api/v1/delivery-orders/:id - Xóa lệnh

**Công việc chi tiết**:
- Design database: 2h
- Implement customer CRUD: 15h
- Implement customer type CRUD: 10h
- Implement delivery order CRUD: 12h
- Unit tests: 15h
- API documentation: 5h

**Công cụ**:
- PHP native
- MySQL 8.0
- Port: 3005

---

#### **THÀNH VIÊN 5: MANUFACTURING & REPORTING SERVICE**

**A. MANUFACTURING SERVICE (Chức năng 1)**

**Mô tả**: Quản lý lệnh sản xuất, tiếANALYTICS SERVICE (Đơn Giản)**

**Mô tả**: Quản lý lệnh sản xuất và biểu báo thống kê.

**3 Chức năng**:
1. **Quản lý lệnh sản xuất**: CRUD lệnh sản xuất, cập nhật trạng thái
2. **Quản lý toa lệnh sản xuất**: Chi tiết SX, xuất NVL, nhập SP hoàn thành
3. **Báo cáo & Dashboard**: Thống kê bán hàng, tồn kho, hiệu suất SX

**Bảng dữ liệu**:
```
- Lenhsanxuat (Malenh, Masp, Ngaysanxuat, Soluongsanxuat, Trangthai, Ngaybatdau, Ngayketthuc)
- Chitiet_XuatNVL_Sanxuat (Id, Malenh, Manvl, Soluong, Ngayxuat)
- Chitiet_Nhapsanpham_Sanxuat (Id, Malenh, Makho, Masp, Soluong, Ngaynhap)
- DashboardMetrics (MaChi, Loai, GiaTri, NgayCapNhat)
```

**12 API Endpoints**:
- GET /api/v1/production-orders - Danh sách lệnh SX
- POST /api/v1/production-orders - Thêm lệnh SX
- PUT /api/v1/production-orders/:id - Sửa lệnh SX
- DELETE /api/v1/production-orders/:id - Xóa lệnh SX
- PUT /api/v1/production-orders/:id/status - Cập nhật trạng thái
- POST /api/v1/production-details - Chi tiết SX
- GET /api/v1/dashboard/sales - Dashboard bán hàng
- GET /api/v1/dashboard/inventory - Dashboard tồn kho
- GET /api/v1/dashboard/production - Dashboard SX
- GET /api/v1/reports/sales - Báo cáo bán hàng
- GET /api/v1/reports/inventory - Báo cáo tồn kho
- GET /api/v1/reports/production - Báo cáo SX

**Công việc chi tiết**:
- Design database: 2h
- Implement production CRUD: 15h
- Implement production details: 12h
- Implement dashboard: 12h
- Implement reports: 15h
- Unit tests: 15h
- API documentation: 5h

**Công cụ**:
- PHP native
- MySQL 8.0
- Port: 3004 (Manufacturing), 3006 (Analytics)

---

### CÔNG VIỆC CHUNG (Tất cả thành viên)

**1. API Gateway** (20 giờ):
- 1 file PHP đơn giản
- Routing đến các services
- JWT token validation
- CORS handling

**2. Setup & Configuration** (15 giờ):
- Database schema cho 5 services
- Environment config (.env)
- Shared helper functions
- Database migration scripts

**3. Testing & QA** (30 giờ):
- Unit testing cơ bản
- Integration testing
- Manual QA
- Bug fixing

**4. Documentation** (20 giờ):
- API documentation
- Setup guide
- Developer guide
- Deployment guide

**5. Integration & Deployment** (5 giờ):
- Verify services communicate
- Create startup guide
- Deploy on XAMPP

---

## BIỂU ĐỒ GÓI

```
┌──────────────────────────────────────────────────────┐
│              CLIENT APPLICATION                      │
│         (Web Browser / Desktop App)                  │
└──────────────┬───────────────────────────────────────┘
               │
               ▼
┌──────────────────────────────────────────────────────┐
│         API GATEWAY (Port 8000)                      │
│  (Routing, JWT Auth, CORS)                          │
└──────────────┬───────────────────────────────────────┘
               │
    ┌──────────┼──────────┬─────────────┬─────────────┐
    │          │          │             │             │
    ▼          ▼          ▼             ▼             ▼
┌────────┐┌────────┐┌──────────┐┌──────────┐┌──────────┐
│User    ││Product ││Warehouse ││Customer  ││Manufact. │
│Service ││Service ││Service   ││Service   ││& Report  │
│3001    ││3002    ││3003      ││3005      ││Service   │
└────┬───┘└────┬───┘└────┬─────┘└────┬─────┘└────┬─────┘
     │         │         │           │           │
     ▼         ▼         ▼           ▼           ▼
┌────────┐┌────────┐┌──────────┐┌──────────┐┌──────────┐
│User DB ││Product ││Warehouse ││Customer  ││Analytics │
│        ││DB      ││DB        ││DB        ││DB        │
└────────┘└────────┘└──────────┘└──────────┘└──────────┘
```

---

## BIỂU ĐỒ THÀNH PHẦN

### Kiến trúc Component của Hệ Thống

```
CLIENT LAYER
┌──────────────────────────────────────────────────────────┐
│  Web Browser │ Desktop App │ Mobile App (AJAX)          │
└──────────────────────────────────────────────────────────┘

API GATEWAY LAYER
┌──────────────────────────────────────────────────────────┐
│                  API Gateway (1 File)                     │
│  ┌────────────┐ ┌─────────────┐ ┌──────────────────┐    │
│  │ Router     │ │ JWT Auth    │ │ CORS Handler     │    │
│  └────────────┘ └─────────────┘ └──────────────────┘    │
└──────────────────────────────────────────────────────────┘

MICROSERVICES LAYER
┌──────────────────────────────────────────────────────────┐
│                                                            │
│ USER SERVICE    PRODUCT SERVICE   WAREHOUSE SERVICE      │
│ ┌──────────┐    ┌──────────┐       ┌──────────┐         │
│ │Login/Reg │    │Products  │       │Import    │         │
│ ├──────────┤    ├──────────┤       ├──────────┤         │
│ │User CRUD │    │Categories│       │Export    │         │
│ ├──────────┤    ├──────────┤       ├──────────┤         │
│ │Profile   │    │Materials │       │Inventory │         │
│ └──────────┘    └──────────┘       └──────────┘         │
│                                                            │
│ CUSTOMER SERVICE             MANUFACTURING & ANALYTICS   │
│ ┌──────────┐                ┌──────────┐                │
│ │Customers │                │Production│                │
│ ├──────────┤                ├──────────┤                │
│ │Cust Types│                │Details   │                │
│ ├──────────┤                ├──────────┤                │
│ │Delivery  │                │Dashboard │                │
│ │Orders    │                │Reports   │                │
│ └──────────┘                └──────────┘                │
│                                                            │
└──────────────────────────────────────────────────────────┘

DATABASE LAYER
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ User DB  │ │Product DB│ │Warehouse │ │Customer  │
│          │ │          │ │   DB     │ │  DB      │
└──────────┘ └──────────┘ └──────────┘ └──────────┘

┌──────────┐
│Analytics │
│   DB     │
└──────────┘
```

---

## BIỂU ĐỒ TRIỂN KHAI

### Development Environment (Local)
```
├── Folder d:\xampp\htdocs\vlxd\
│   ├── api-gateway/
│   ├── services/
│   │   ├── user-service/
│   │   ├── product-service/
│   │   ├── warehouse-service/
│   │   ├── customer-service/
│   │   └── manufacturing-analytics/
│   └── shared/
│       └── config.php
│
├── XAMPP (MySQL + PHP Server)
│   ├── MySQL: 5 databases
│   └── Web Server: localhost
│
└── All services respond over REST API
```

### Production Environment (XAMPP Server)
```
├── d:\xampp\htdocs\vlxd\
│   ├── All 5 services running
│   ├── API Gateway routing requests
│   └── MySQL with 5 databases
│
└── Access via: http://localhost/vlxd/api/v1/*
```

---

## LỊCH TRÌNH THỰC HIỆN

### Phase 1: Preparation & Setup (Tuần 1)
- ✓ Kick-off & requirement review
- ✓ Design 5 database schemas
- ✓ Create API specifications
- ✓ Setup environment (.env, helpers)
- Task: Tất cả thành viên

### Phase 2: Development (Tuần 1-2)
**Tuần 1:**
- Thành viên 1: User Service API
- Thành viên 2: Product Service API
- Thành viên 3: Warehouse Service API
- Thành viên 4: Customer Service API
- Thành viên 5: Manufacturing + Analytics API

**Tuần 2:**
- API Gateway integration
- Cross-service testing
- Bug fixes & refinements

### Phase 3: Testing & Deployment (Tuần 2-3)
- Unit testing
- Integration testing
- Deploy to XAMPP
- Documentation

---

## CÔNG NGHỆ SỬ DỤNG

| Lớp | Công nghệ | Tại sao |
|-----|-----------|---------|
| **Backend** | PHP native (8.2+) | Đơn giản, không cần framework |
| **Database** | MySQL 8.0 | Stable, một database per service |
| **API Communication** | HTTP REST | JSON, lightweight |
| **Authentication** | JWT (native PHP) | Simple token-based auth |
| **Testing** | PHPUnit | Unit & integration tests |
| **Documentation** | Markdown | API docs, setup guide |
| **Deployment** | PHP Server / XAMPP | Easy to manage |
| **Version Control** | Git | Standard workflow |

---

## RỦI RO VÀ GIẢI PHÁP

| Rủi ro | Tác động | Giải pháp |
|--------|---------|----------|
| **API không respond** | Trung bình | Health check, restart scripts |
| **Database connection** | Cao | Connection pooling, .env config |
| **Data consistency** | Trung bình | Transaction logs, backup strategy |
| **Debugging** | Trung bình | Logging, error messages rõ ràng |
| **Performance** | Thấp | Caching, optimize queries |
| **Team coordination** | Thấp | Good documentation, clear APIs |
| **Testing coverage** | Trung bình | Unit tests, integration tests |

---

## TIÊU CHÍ THÀNH CÔNG

1. ✓ **Functionality**: Tất cả 5 services API hoạt động
2. ✓ **Integration**: Các services giao tiếp được với nhau
3. ✓ **Testing**: Unit & integration tests pass
4. ✓ **Documentation**: API docs, setup guide hoàn chỉnh
5. ✓ **Deployment**: Chạy được trên PHP/XAMPP
6. ✓ **Code quality**: Code sạch, dễ đọc
7. ✓ **Timeline**: Hoàn thành trong 2-3 tuần
6. ✓ **Documentation**: Complete API docs & deployment guide
7. ✓ **Security**: Pass security audit
8. ✓ **On-time**: Hoàn thành trong 8 tuần

---

## PHÂN CÔNG CHI TIẾT - ĐỰA RA NGAY

### Tuần 1: Preparation
- **Thành viên 1**: Setup User Service project, database schema design
- **Thành viên 2**: Setup Product Service project, database schema design
- **Thành viên 3**: Setup Warehouse Service, database schema design
- **Thành viên 4**: Setup Customer Service, database schema design
- **Thành viên 5**: Setup Manufacturing & Report services
- **Chung**: Setup API Gateway, Docker Compose, CI/CD

### Tuần 2-3: Backend Development Phase 1
- **Thành viên 1**: User authentication, JWT, role-based access
- **Thành viên 2**: Product CRUD, category management, caching
- **Thành viên 3**: Import/Export endpoints, inventory management
- **Thành viên 4**: Customer CRUD, order management, shopping cart
- **Thành viên 5A**: Production order management, status tracking
- **Thành viên 5B**: Report generation, dashboard data aggregation

### Tuần 4-5: Integration & Testing
- All members: Integration testing, bug fixing
- Docker containerization, Kubernetes deployment files
- API Gateway implementation & testing

### Tuần 6-7: Final Testing & Deployment
- End-to-end testing, performance optimization
- Staging deployment, user acceptance testing
- Production deployment preparation

### Tuần 8: Documentation & Handover
- Complete API documentation
- Deployment & operations guide
- Knowledge transfer & training

---

## KẾT LUẬN

Kiến trúc Microservices cho hệ thống VLXD mang lại các lợi ích:

✓ **Tính linh hoạt**: Từng service có thể phát triển, kiểm thử, triển khai độc lập  
✓ **Khả năng mở rộng**: Dễ dàng scale từng service riêng theo nhu cầu  
✓ **Dễ bảo trì**: Mã nguồn nhỏ hơn, dễ hiểu hơn  
✓ **Độc lập công nghệ**: Mỗi team có thể chọn công nghệ phù hợp  
✓ **Tính chịu lỗi**: Một service gặp sự cố không ảnh hưởng toàn bộ hệ thống

Với kế hoạch chi tiết, phân công rõ ràng, và công cụ phù hợp, dự án này có khả năng hoàn thành thành công trong 8 tuần.

---

**Ngày phê duyệt**: 31/03/2026  
**Phê duyệt bởi**: Nhóm phát triển  
**Ghi chú**: Báo cáo này phục vụ mục đích tham khảo và quy hoạch dự án.
