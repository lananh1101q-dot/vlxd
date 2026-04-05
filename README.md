# Hệ thống Quản lý Kho Vật Liệu Xây Dựng (VLXD) - Microservices

Đây là dự án Quản lý Vật liệu xây dựng được ứng dụng kiến trúc **Microservices** chạy hoàn toàn bằng PHP (không sử dụng framework) và MySQL.

## Yêu cầu hệ thống (Prerequisites)
- **PHP 8.0** trở lên (Khuyến nghị bật extension `pdo_mysql`, `curl`).
- **MySQL / MariaDB** (Bạn có thể sử dụng XAMPP, MAMP, hoặc Docker).

## 🚀 Quick Start (Hướng dẫn cài đặt nhanh)

### Bước 1: Khởi tạo Cơ sở dữ liệu (Database)
1. Hãy chắc chắn rằng dịch vụ **MySQL** của bạn đã được khởi động. (vào xampp start mysql)
2. Mở terminal tại thư mục gốc của dự án `vlxd`, chạy lệnh sau để tự động tạo 5 databases riêng lẻ cho các microservices và import bảng:
   ```bash
   php setup_db.php
   ```
   *(Script này sẽ tạo ra 5 DB: `vlxd_user`, `vlxd_product`, `vlxd_warehouse`, `vlxd_customer`, `vlxd_manufacturing` và khởi tạo tài khoản Admin mặc định tĩnh).*
3. **(Tùy chọn)** Nếu bạn muốn có dữ liệu mẫu (Sample Data) để test:
   ```bash
   php create_sample_data_v3.php
   ```

### Bước 2: Chạy các Microservices & API Gateway
Hệ thống yêu cầu các Service chạy ngầm ở nhiều port khác nhau. Bạn có thể tự động chạy tất cả bằng file script cung cấp sẵn:
- Mở Terminal **(cmd hoặc git bash)** và chạy lệnh `./start_services.sh`.


### Bước 3: Chạy giao diện Web Frontend
Mở một cửa sổ Terminal mới tại thư mục gốc dự án và chạy PHP Built-in Server cho phần frontend (Giao diện người dùng):
```bash
php -S localhost:8080
```
> Hoặc mở thông qua XAMPP: `http://localhost/vlxd` (nếu bạn vứt code vào thư mục htdocs).

### Bước 4: Đăng nhập
Mở trình duyệt lên và truy cập vào địa chỉ: **http://localhost:8080**
Sử dụng tài khoản mặc định:
- **Tên đăng nhập:** `admin`
- **Mật khẩu:** `123456`

---

## 🏗 Cấu trúc Port (Danh sách Dịch vụ)
Nếu chạy thành công, hệ thống của bạn sẽ chiếm dụng các Port sau trên `localhost`:

| Port | Dịch vụ (Service) | Thư mục mã nguồn |
|------|-------------------|--------------------------------------|
| **8080** | **Web Frontend (GUI)** | Root dir `/` |
| **8000** | **API Gateway** | `/api-gateway` |
| 3001 | User Service | `/services/user-service` |
| 3002 | Product Service | `/services/product-service` |
| 3003 | Warehouse Service | `/services/warehouse-service` |
| 3004 | Manufacturing Service | `/services/manufacturing-service` |
| 3005 | Customer Service | `/services/customer-service` |

---

## Troubleshooting cơ bản
1. **Lỗi `Connection refused` hoặc API lỗi 502/500:** Hãy kiểm tra xem file `start_services.cmd/sh` có đang chạy bình thường không, các console chạy port có báo lỗi cấp quyền hay trùng port không.
2. **Lỗi không đăng nhập được SQL (Lỗi báo trên CLI):** Config kết nối database mặc định là `root` và pass rỗng `""`. Nếu máy của bạn có mật khẩu mysql, vui lòng sửa lại ở dòng trên cùng của các file:
   - `setup_db.php`, `create_sample_data_v3.php`
   - Tại thư mục `services/<tên-dịch-vụ>/src/Core/Database.php`

Chúc bạn trải nghiệm và sử dụng hiệu quả hệ thống! 🚀
