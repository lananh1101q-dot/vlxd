# Báo Cáo Kiến trúc Hệ thống Phân tán (Microservices)

---

## PHẦN 1: LÝ THUYẾT NỀN TẢNG — BẠN CẦN BIẾT GÌ TRƯỚC?

### 1.1. Monolith vs Microservices — Hai trường phái xây hệ thống

Hãy tưởng tượng bạn đang xây một tòa nhà.

**Kiến trúc Nguyên khối (Monolith)** — cách cũ:
> Toàn bộ hệ thống là **1 tòa nhà duy nhất**. Phòng khách, bếp, phòng ngủ, kho hàng... tất cả nằm chung 1 mái nhà. Muốn sửa nhà bếp? Bạn phải tắt điện toàn bộ tòa nhà.
>
> Tương đương trong code PHP cũ: File `index.php` vừa hiển thị HTML, vừa kết nối trực tiếp MySQL, vừa xử lý nghiệp vụ — tất cả trong 1 file.

**Kiến trúc Vi dịch vụ (Microservices)** — cách mới (hệ thống hiện tại của bạn):
> Mỗi chức năng là **1 tòa nhà riêng biệt**. Kho hàng ở một tòa, Sản phẩm ở một tòa, Khách hàng ở một tòa. Chúng "nói chuyện" với nhau qua điện thoại (API). Muốn nâng cấp tòa nhà Sản phẩm? Không ảnh hưởng tòa nhà Khách hàng.

---

### 1.2. RESTful API là gì?

API (Application Programming Interface) là "giao thức ngôn ngữ" để các thành phần phần mềm nói chuyện với nhau.

**REST** là một tập quy ước, quy định rằng:
- Mỗi URL đại diện cho một **tài nguyên** (resource). Ví dụ: `/products` = tập hợp tất cả sản phẩm.
- Hành động được thể hiện qua **HTTP Method**:

| HTTP Method | Ý nghĩa | Ví dụ trong dự án |
|---|---|---|
| `GET` | Đọc/Lấy dữ liệu | `GET /products` → Lấy danh sách sản phẩm |
| `POST` | Tạo mới | `POST /products` → Thêm sản phẩm mới |
| `PUT` | Cập nhật toàn bộ | `PUT /products/SP001` → Sửa sản phẩm SP001 |
| `DELETE` | Xóa | `DELETE /products/SP001` → Xóa sản phẩm SP001 |

- Kết quả trả về luôn là **JSON** (JavaScript Object Notation) — định dạng dữ liệu dễ đọc cho cả người và máy.

Ví dụ JSON mà Product Service trả về khi bạn gọi `GET /products`:
```json
{
  "success": true,
  "message": "Products retrieved",
  "data": {
    "products": [
      { "Masp": "SP001", "Tensp": "Gạch 30x30", "Giaban": 150000 },
      { "Masp": "SP002", "Tensp": "Xi măng PCB40", "Giaban": 85000 }
    ]
  }
}
```

---

### 1.3. MVC là gì? — Mô hình 3 lớp tách biệt

**MVC (Model - View - Controller)** là một mô hình thiết kế phần mềm, chia ứng dụng làm 3 phần rõ ràng:

```
┌─────────────────────────────────────────────────────┐
│           MVC trong Product Service                  │
├──────────────┬────────────────┬──────────────────────┤
│    MODEL     │   CONTROLLER   │         VIEW         │
│  (Dữ liệu)  │  (Điều phối)  │    (Trình bày)       │
├──────────────┼────────────────┼──────────────────────┤
│ Nói chuyện  │ Nhận yêu cầu, │ Trong dự án này,     │
│ trực tiếp   │ gọi Model lấy │ View là file PHP/JS  │
│ với MySQL.  │ data, trả về  │ trên Frontend. API   │
│ Chứa các    │ JSON cho      │ chỉ trả JSON thuần.  │
│ câu lệnh    │ người gọi.    │                      │
│ SQL.        │               │                      │
├──────────────┼────────────────┼──────────────────────┤
│ Product.php │ ProductCon-   │ Sanpham.php          │
│ Category.php│ troller.php   │ dmsp.php             │
│ Material.php│               │ Nguyenvatlieu.php    │
└──────────────┴────────────────┴──────────────────────┘
```

> **Lợi ích**: Nếu bạn muốn đổi câu SQL lấy sản phẩm, bạn chỉ cần sửa `Product.php` (Model). Controller và View không cần biết gì.

---

### 1.4. JWT Token — Chìa khóa bảo mật

**JWT (JSON Web Token)** là một chuỗi ký tự mã hóa, được dùng như chiếc "Thẻ nhân viên":

1. Bạn đăng nhập → User Service xác minh đúng mật khẩu → cấp cho bạn một Token.
2. Mỗi lần bạn muốn lấy dữ liệu, bạn phải xuất trình Token này.
3. API Gateway kiểm tra Token có hợp lệ không → nếu có, mới cho đi tiếp.

**Token trong dự án** được lưu trong `localStorage` của trình duyệt. Javascript lấy ra như sau:
```javascript
const token = localStorage.getItem('token');
// Token trông như thế này: eyJNYW52IjoiTlYwMSIs...
```

---

## PHẦN 2: CẤU TRÚC TỔNG THỂ HỆ THỐNG

### 2.1. Sơ đồ luồng dữ liệu

Khi bạn mở trang **Sản phẩm**, đây là toàn bộ hành trình dữ liệu:

```
Trình duyệt (Sanpham.php)
       │
       │  1. JS gọi fetch('http://localhost:8000/api/v1/products')
       │     kèm theo Token trong Header
       ▼
┌─────────────────┐
│   API GATEWAY   │  2. Kiểm tra Token có hợp lệ không?
│  (Cổng 8000)   │     Nếu không → trả về lỗi 401
│api-gateway/     │     Nếu có → chuyển tiếp yêu cầu
│  index.php      │
└────────┬────────┘
         │  3. Forward yêu cầu đến đúng service
         ▼
┌─────────────────┐
│ PRODUCT SERVICE │  4. Router nhận yêu cầu GET /products
│  (Cổng 3002)   │     → gọi ProductController::getProducts()
│services/product │     → Controller gọi Product Model
│  -service/      │     → Model chạy SQL lấy dữ liệu
└────────┬────────┘     → trả JSON về Gateway
         │
         │  5. Gateway trả JSON về Trình duyệt
         ▼
Trình duyệt (Sanpham.php)
       │
       │  6. JS nhận JSON, vẽ bảng HTML hiển thị cho người dùng
       ▼
  👤 Người dùng thấy danh sách sản phẩm
```

### 2.2. Các Service trong hệ thống và Database tương ứng

| Service | Cổng | Database | Chức năng |
|---|---|---|---|
| user-service | 3001 | `vlxd_user` | Đăng nhập, quản lý người dùng |
| product-service | 3002 | `vlxd_product` | Sản phẩm, NVL, Nhà cung cấp |
| warehouse-service | 3003 | `vlxd_warehouse` | Kho, Nhập, Xuất, Điều chuyển |
| manufacturing-service | 3004 | `vlxd_manufacturing` | Lệnh sản xuất |
| customer-service | 3005 | `vlxd_customer` | Khách hàng |
| **api-gateway** | **8000** | *(Không có DB riêng)* | **Cổng bảo mật trung tâm** |

---

## PHẦN 3: PHÂN HỆ SẢN PHẨM (PRODUCT SERVICE) — CHI TIẾT KỸ THUẬT

**Product Service** là service phức tạp nhất trong hệ thống, quản lý 5 nhóm dữ liệu liên quan đến hàng hóa.

### 3.1. Cơ sở dữ liệu: `vlxd_product`

File: `services/product-service/product_db.sql`

Bảng dữ liệu và quan hệ giữa chúng:

```
┌──────────────┐         ┌──────────────────┐         ┌──────────────────┐
│  Nhacungcap  │         │    Danhmucsp     │         │     Sanpham      │
├──────────────┤         ├──────────────────┤         ├──────────────────┤
│ Mancc (PK)  │         │ Madm (PK, AI)   │◄────────│ Madm (FK)       │
│ Tenncc      │         │ Tendm (UNIQUE)  │         │ Masp (PK)       │
│ Sdtncc      │         │ Mota            │         │ Tensp            │
│ Diachincc   │         └──────────────────┘         │ Dvt              │
└──────────────┘                                      │ Giaban           │
                                                      └────────┬─────────┘
                                                               │
                                                               │ (Khóa ngoại)
┌──────────────────┐         ┌─────────────────────────┐      │
│  Nguyenvatlieu   │         │    Congthucsanpham       │◄─────┘
├──────────────────┤         ├─────────────────────────┤
│ Manvl (PK)      │◄───────│ Masp (FK, PK tổng hợp)  │
│ Tennvl           │         │ Manvl (FK, PK tổng hợp) │
│ Dvt              │         │ Soluong                  │
│ Giavon           │         └─────────────────────────┘
└──────────────────┘
```

**Giải thích từng bảng:**

| Bảng | Ý nghĩa thực tế | Ví dụ dữ liệu |
|---|---|---|
| `Danhmucsp` | Phân nhóm sản phẩm | "Gạch&Đá", "Xi Măng", "Sơn" |
| `Sanpham` | Hàng hóa bán ra | Gạch 30x30, Xi măng PCB40 |
| `Nhacungcap` | Đơn vị cung cấp nguyên liệu | Cty VLXD Minh Đức, Cty Xi Măng Hà Tiên |
| `Nguyenvatlieu` | Vật liệu thô đầu vào | Cát, Đá, Clinker, Bột màu |
| `Congthucsanpham` | **BOM** — 1kg Xi Măng cần bao nhiêu kg Clinker? | SP001 cần 0.9kg NVL001 + 0.1kg NVL002 |

> **Chú ý kỹ thuật**: Bảng `Congthucsanpham` có **Khóa chính tổng hợp** `(Masp, Manvl)`. Nghĩa là cặp (Sản phẩm + Nguyên vật liệu) là duy nhất. 1 sản phẩm có thể cần nhiều NVL, và 1 NVL có thể xuất hiện trong nhiều công thức.

---

### 3.2. Cấu trúc thư mục — MVC thực tế

```
services/product-service/
│
├── index.php                ← Điểm vào (Entry Point) — Định nghĩa tất cả Routes
├── db.php                   ← Kết nối đến database vlxd_product
├── product_db.sql           ← Script tạo bảng
│
└── src/
    ├── Core/
    │   ├── Router.php       ← Bộ định tuyến: URL nào → hàm nào?
    │   └── Database.php     ← Lớp kết nối PDO (PHP Data Objects)
    │
    ├── Models/              ← TẦNG MODEL: Tương tác trực tiếp với Database
    │   ├── BaseModel.php    ← Lớp cha chứa hàm dùng chung (getAll, getById...)
    │   ├── Product.php      ← SQL cho bảng Sanpham
    │   ├── Category.php     ← SQL cho bảng Danhmucsp
    │   ├── Material.php     ← SQL cho bảng Nguyenvatlieu
    │   ├── Supplier.php     ← SQL cho bảng Nhacungcap
    │   └── Formula.php      ← SQL cho bảng Congthucsanpham
    │
    └── Controllers/         ← TẦNG CONTROLLER: Điều phối yêu cầu
        ├── BaseController.php  ← Lớp cha chứa hàm jsonResponse(), getBody()
        └── ProductController.php ← Xử lý logic cho tất cả 5 nhóm dữ liệu
```

---

### 3.3. Tầng ROUTER — Ai xử lý yêu cầu nào?

File: `services/product-service/index.php`

Router đóng vai trò như **tổng đài điện thoại**: khi có yêu cầu đến, nó nhìn vào URL và HTTP Method rồi kết nối đến đúng người trực.

```php
// Khai báo Router
$router = new Router();

// Sản phẩm (Products)
$router->add('GET',    '/products',      [ProductController::class, 'getProducts']);
$router->add('GET',    '/products/{id}', [ProductController::class, 'getProduct']);
$router->add('POST',   '/products',      [ProductController::class, 'createProduct']);
$router->add('PUT',    '/products/{id}', [ProductController::class, 'updateProduct']);
$router->add('DELETE', '/products/{id}', [ProductController::class, 'deleteProduct']);

// Danh mục SP, NVL, NCC, Công thức... tương tự
```

**Đọc hiểu**: Dòng đầu tiên nghĩa là:
> "Nếu có yêu cầu GET đến đường dẫn `/products` → gọi hàm `getProducts()` trong class `ProductController`."

---

### 3.4. Tầng CONTROLLER — Điều phối không xử lý

File: `src/Controllers/ProductController.php`

Controller **không viết SQL**, không làm việc với Database. Nó chỉ:
1. Gọi Model lấy dữ liệu
2. Trả kết quả về dưới dạng JSON

```php
class ProductController extends BaseController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        // Khởi tạo các Model cần dùng
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    // Lấy danh sách sản phẩm (xử lý cho GET /products)
    public function getProducts() {
        $data = $this->productModel->getAllWithCategory(); // Gọi Model
        $this->jsonResponse(true, 'Products retrieved', ['products' => $data]); // Trả JSON
    }

    // Tạo sản phẩm mới (xử lý cho POST /products)
    public function createProduct() {
        $body = $this->getBody();    // Đọc JSON gửi lên từ Frontend
        if ($this->productModel->create($body)) {
            $this->jsonResponse(true, 'Product created', null, 201); // 201 = Created
        }
        $this->jsonResponse(false, 'Create failed', null, 400); // 400 = Bad Request
    }
}
```

---

### 3.5. Tầng MODEL — Kết nối trực tiếp Database

Đây là tầng "thợ lặn" — nó lặn xuống Database, lấy dữ liệu lên rồi trả cho Controller.

**Ví dụ thực tế**: Model `Product` khi lấy danh sách sản phẩm phải **JOIN** với bảng `Danhmucsp` để lấy kèm tên danh mục:

```sql
-- SQL mà Model Product chạy để lấy danh sách
SELECT sp.*, dm.Tendm AS TenDanhMuc
FROM Sanpham sp
LEFT JOIN Danhmucsp dm ON sp.Madm = dm.Madm
ORDER BY sp.Masp
```

> **Tại sao phải JOIN?** Vì bảng `Sanpham` chỉ lưu mã danh mục (`Madm`). Để hiển thị được tên "Gạch&Đá" thay vì "1", chúng ta phải ghép (JOIN) với bảng `Danhmucsp`.

---

### 3.6. Tầng VIEW (Frontend) — Hiển thị dữ liệu

Đây là các file PHP trong thư mục gốc như `Sanpham.php`, `dmsp.php`. Nhưng khác hệ thống cũ, PHP ở đây **chỉ tạo ra khung HTML rỗng**. Toàn bộ dữ liệu được Javascript nạp vào sau.

**Quy trình hoàn chỉnh — Ví dụ trang Danh mục sản phẩm (`dmsp.php`):**

```
Bước 1: Người dùng truy cập /vlxd/dmsp
        → Apache/PHP trả về trang HTML với bảng trống

Bước 2: Trình duyệt chạy đoạn JavaScript:
        async function loadCategories() {
            const headers = getHeaders(); // Lấy Token từ localStorage
            const res = await fetch(API + '/categories', { headers });
            const data = await res.json();
            // data = { success: true, data: { categories: [...] } }
        }

Bước 3: fetch() gửi yêu cầu đến:
        http://localhost:8000/api/v1/categories
        (Header: Authorization: Bearer eyJNYW52...)

Bước 4: API Gateway (cổng 8000) kiểm tra Token
        → Hợp lệ → chuyển tiếp sang Product Service (cổng 3002)

Bước 5: Product Service Router nhận GET /categories
        → Gọi ProductController::getCategories()
        → Gọi Category Model → Chạy SQL → Lấy dữ liệu
        → Trả JSON về Gateway → Gateway trả về Trình duyệt

Bước 6: JavaScript nhận JSON, vẽ bảng HTML:
        data.data.categories.forEach(cat => {
            tbody.innerHTML += `<tr>
                <td>${cat.Madm}</td>
                <td>${cat.Tendm}</td>
            </tr>`;
        });

Kết quả: Bảng Danh mục hiện ra trước mắt người dùng ✓
```

---

## PHẦN 4: CHI TIẾT 5 CHỨC NĂNG TRONG PRODUCT SERVICE

### 4.1. Danh mục Sản phẩm (`Danhmucsp`)

**Mục đích**: Phân nhóm sản phẩm để quản lý dễ hơn. Như "thư mục" trong máy tính.

**Ví dụ**: Danh mục "Gạch & Đá" → chứa Gạch 30x30, Đá 1x2, Gạch lát sàn...

**Bảng dữ liệu**:
```sql
CREATE TABLE Danhmucsp (
    Madm  INT PRIMARY KEY AUTO_INCREMENT, -- Tự động tăng từ 1
    Tendm VARCHAR(100) NOT NULL UNIQUE,   -- Không được trùng tên
    Mota  VARCHAR(100)                    -- Mô tả tùy chọn
);
```

**API Endpoints**:
- `GET /categories` → Danh sách tất cả danh mục
- `POST /categories` → Thêm danh mục mới
- `PUT /categories/{Madm}` → Sửa danh mục
- `DELETE /categories/{Madm}` → Xóa danh mục

---

### 4.2. Sản phẩm (`Sanpham`)

**Mục đích**: Quản lý hàng hóa bán ra của cửa hàng VLXD.

**Bảng dữ liệu**:
```sql
CREATE TABLE Sanpham (
    Masp  VARCHAR(50) PRIMARY KEY,     -- Mã tự đặt, VD: "SP001"
    Tensp VARCHAR(255) NOT NULL,       -- Tên sản phẩm
    Madm  INT,                         -- Thuộc danh mục nào?
    Dvt   VARCHAR(50) NOT NULL,        -- Đơn vị tính: "kg", "m2", "bao"
    Giaban DECIMAL(18,2) DEFAULT 0,   -- Giá bán lẻ
    FOREIGN KEY (Madm) REFERENCES Danhmucsp(Madm) -- Ràng buộc khóa ngoại
);
```

> **Khóa ngoại (FOREIGN KEY)** là gì? Nó đảm bảo bạn không thể tạo sản phẩm với `Madm = 99` nếu danh mục số 99 không tồn tại. Cơ sở dữ liệu tự động bảo vệ tính nhất quán dữ liệu.

**API Endpoints**:
- `GET /products` → Danh sách sản phẩm (kèm tên danh mục, do JOIN)
- `GET /products/{Masp}` → Thông tin một sản phẩm cụ thể
- `POST /products` → Thêm sản phẩm mới
- `PUT /products/{Masp}` → Cập nhật thông tin sản phẩm
- `DELETE /products/{Masp}` → Xóa sản phẩm

---

### 4.3. Nhà cung cấp (`Nhacungcap`)

**Mục đích**: Lưu trữ thông tin các đơn vị cung cấp hàng hóa/nguyên liệu.

**Bảng dữ liệu**:
```sql
CREATE TABLE Nhacungcap (
    Mancc     VARCHAR(50) PRIMARY KEY, -- Mã NCC, VD: "NCC001"
    Tenncc    VARCHAR(255) NOT NULL,   -- Tên công ty/cá nhân
    Sdtncc    VARCHAR(15),             -- Số điện thoại
    Diachincc VARCHAR(255)             -- Địa chỉ liên hệ
);
```

**API Endpoints**: `GET / POST / PUT / DELETE` tại `/suppliers`

> **Lưu ý**: Nhà cung cấp trong dự án này được lưu cùng database `vlxd_product` vì họ cung cấp hàng **cho kho sản phẩm**. Khi tạo Phiếu nhập kho, Warehouse Service sẽ tham chiếu danh sách nhà cung cấp từ đây.

---

### 4.4. Nguyên vật liệu (`Nguyenvatlieu`)

**Mục đích**: Quản lý các vật liệu thô dùng để **sản xuất** ra thành phẩm. Khác với Sản phẩm là hàng hóa bán ra, Nguyên vật liệu là đầu vào của quy trình sản xuất.

**Bảng dữ liệu**:
```sql
CREATE TABLE Nguyenvatlieu (
    Manvl  VARCHAR(50) PRIMARY KEY, -- Mã NVL, VD: "NVL001"
    Tennvl VARCHAR(255) NOT NULL,   -- Tên: "Cát Vàng", "Đá 1x2"
    Dvt    VARCHAR(50) NOT NULL,    -- Đơn vị: "m3", "tấn", "kg"
    Giavon DECIMAL(18,2) DEFAULT 0  -- Giá nhập vào (giá vốn)
);
```

**API Endpoints**: `GET / POST / PUT / DELETE` tại `/materials`

---

### 4.5. Công thức Sản phẩm — BOM (`Congthucsanpham`)

**Mục đích**: Đây là chức năng **quan trọng nhất và phức tạp nhất** của Product Service. Nó định nghĩa: "Để sản xuất 1 đơn vị sản phẩm X, cần bao nhiêu nguyên vật liệu?".

Trong ngành sản xuất, đây gọi là **BOM — Bill of Materials** (Bảng định mức vật liệu).

**Ví dụ thực tế**:
> Để sản xuất 1 tấn Xi Măng (SP001), cần:
> - 0.90 tấn Clinker (NVL001)
> - 0.07 tấn Thạch cao (NVL002)  
> - 0.03 tấn Phụ gia (NVL003)

**Bảng dữ liệu**:
```sql
CREATE TABLE Congthucsanpham (
    Masp    VARCHAR(50), -- Mã sản phẩm
    Manvl   VARCHAR(50), -- Mã nguyên vật liệu
    Soluong DECIMAL(10,2) NOT NULL, -- Số lượng NVL cần cho 1 đơn vị SP
    PRIMARY KEY (Masp, Manvl),   -- Khóa tổng hợp: 1 cặp SP+NVL là duy nhất
    FOREIGN KEY (Masp)  REFERENCES Sanpham(Masp),
    FOREIGN KEY (Manvl) REFERENCES Nguyenvatlieu(Manvl)
);
```

**Cách hoạt động trong thực tế**:
- Khi bạn tạo một **Lệnh sản xuất** (sản xuất 100 tấn Xi Măng), Manufacturing Service sẽ tra cứu bảng `Congthucsanpham` để biết cần bao nhiêu mỗi loại NVL (100 × 0.90 = 90 tấn Clinker...).
- Đây là cầu nối giữa **Product Service** và **Manufacturing Service** — hai service khác database nhưng vẫn chia sẻ thông tin nghiệp vụ qua API.

**API Endpoints**:
- `GET /formulas?Masp=SP001` → Xem công thức của một sản phẩm
- `GET /formulas` → Xem tất cả công thức
- `POST /formulas` → Tạo/cập nhật công thức
- `DELETE /formulas/{id}` → Xóa một dòng trong công thức

---

## PHẦN 5: TẠI SAO LẠI LÀM PHỨC TẠP NHƯ VẬY?

Đây là câu hỏi rất hợp lý. Câu trả lời:

| Vấn đề với Monolith cũ | Lợi ích với Microservices mới |
|---|---|
| Một lỗi SQL có thể làm sập toàn bộ hệ thống | Lỗi ở Product Service không ảnh hưởng Kho |
| Tất cả dùng chung 1 Database → tắc nghẽn | Mỗi service có DB riêng → chạy độc lập |
| Muốn thêm tính năng phải sửa file to, dễ phá code cũ | Thêm tính năng = thêm 1 service mới, không đụng service cũ |
| Code PHP + HTML lẫn lộn khó bảo trì | MVC tách biệt rõ ràng, dễ tìm lỗi |
| Chỉ dùng được trên Web | API có thể dùng cho cả App di động, App desktop |

---

*Tài liệu được tổng hợp từ mã nguồn thực tế của dự án tại `d:\xampp\htdocs\vlxd\`*
