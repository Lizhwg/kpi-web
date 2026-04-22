# KPI System - Hướng Dẫn Sử Dụng Trang Đăng Nhập

## 📋 Mô Tả

Trang đăng nhập KPI System đã được xây dựng hoàn chỉnh với:
- ✅ Giao diện hiện đại theo thiết kế từ hình ảnh
- ✅ Xác thực người dùng với mã hóa Bcrypt
- ✅ Quản lý phiên (Session) với timeout tự động
- ✅ Chuyển hướng thông minh (redirect)
- ✅ Dashboard trang chủ sau khi đăng nhập

## 🛠️ Cấu Trúc Tệp

```
kpi-web/
├── app/
│   ├── Controllers/
│   │   └── AuthController.php          # Xử lý xác thực
│   └── Models/
│       └── User.php                    # Mô hình người dùng
├── resources/views/
│   ├── auth/
│   │   ├── login.php                   # Trang đăng nhập
│   │   └── dashboard.php               # Trang dashboard
│   └── errors/
│       └── 404.php                     # Trang lỗi 404
├── index.php                           # Tệp khởi chạy chính
├── .env                                # Cấu hình biến môi trường
└── routes/web.php                      # Định nghĩa routes
```

## 📦 Cách Thiết Lập

### 1️⃣ Tạo Cơ Sở Dữ Liệu

Chạy các tệp SQL trong thư mục `database/`:

```bash
# Tạo bảng
mysql -u root < database/01_create_tables.sql

# Thêm dữ liệu mẫu (nếu có)
mysql -u root < database/02_insert_sample_data.sql
```

### 2️⃣ Cấu Hình Tệp .env

Chỉnh sửa file `kpi-web/.env` theo cấu hình MySQL của bạn:

```env
DB_HOST=localhost
DB_NAME=kpi_db
DB_USER=root
DB_PASS=
```

### 3️⃣ Tạo Tài Khoản Người Dùng

Thêm người dùng vào cơ sở dữ liệu bằng cách chạy SQL:

```sql
-- Tạo tài khoản admin
INSERT INTO User (username, password, role, is_deleted) 
VALUES ('admin', '$2y$10$...[hash_bcrypt]...', 1, 0);

-- Tạo tài khoản thư ký
INSERT INTO User (username, password, role, is_deleted) 
VALUES ('secretary', '$2y$10$...[hash_bcrypt]...', 2, 0);
```

**Lưu ý:** Mật khẩu phải được mã hóa bằng Bcrypt.

## 🔐 Quản Lý Tài Khoản

### Mã Hóa Mật Khẩu

Để tạo mật khẩu Bcrypt, sử dụng PHP:

```php
$password = 'password123';
$hashed = password_hash($password, PASSWORD_BCRYPT);
// Sao chép $hashed vào cơ sở dữ liệu
```

### Vai Trò (Role)

| Mã | Vai Trò | Mô Tả |
|----|---------|-------|
| 1 | Admin | Quản trị viên hệ thống |
| 2 | Thư ký | Quản lý danh sách đánh giá |
| 3 | PM | Chấm điểm nhân viên |
| 4 | Trưởng phòng | Phê duyệt đánh giá |
| 5 | HRBP | Hỗ trợ nhân sự |
| 6 | Nhân viên | Nhân viên thường |

## 🚀 Sử Dụng

### Truy Cập Trang Đăng Nhập

```
http://localhost/KPI/kpi-web/
```

Bạn sẽ tự động được chuyển hướng đến:
```
http://localhost/KPI/kpi-web/login
```

### Đăng Nhập

1. Nhập **Username**
2. Nhập **Password**
3. Nhấp **Sign in**

### Sau Khi Đăng Nhập

- ✅ Được chuyển hướng đến Dashboard
- ✅ Thông tin phiên được lưu trong Session
- ✅ Thể hiện thông tin tài khoản và vai trò

### Đăng Xuất

Nhấp vào link **"Đăng xuất"** ở góc trên phải để kết thúc phiên làm việc.

## 🔄 Quy Trình Xác Thực

```
1. Người dùng vào trang web
   ↓
2. Kiểm tra Session
   ├─ Đã đăng nhập? → Cho phép truy cập
   └─ Chưa đăng nhập? → Chuyển đến /login
   ↓
3. Nhập thông tin đăng nhập
   ↓
4. Controller xác thực trong DB
   ├─ Thông tin đúng? → Tạo Session → Dashboard
   └─ Thông tin sai? → Hiển thị lỗi → Ở lại trang login
   ↓
5. Có thể đăng xuất bất kỳ lúc nào
```

## ⚙️ Tính Năng An Ninh

- 🔒 **Mã hóa Bcrypt**: Mật khẩu được mã hóa an toàn
- ⏱️ **Session Timeout**: Tự động đăng xuất sau 1 giờ không hoạt động
- 🚫 **Protected Routes**: Các trang khác yêu cầu đăng nhập
- ✅ **Input Validation**: Kiểm tra dữ liệu nhập vào
- 🛡️ **CSRF Protection**: Sẵn sàng để thêm CSRF tokens

## 📝 Ghi Chú

- Tất cả đường dẫn được định nghĩa trong `routes/web.php`
- Controller được tự động load nhờ autoloader
- Cơ sở dữ liệu sử dụng charset `utf8mb4` để hỗ trợ tiếng Việt
- Timezone được đặt thành Asia/Ho_Chi_Minh

## 🔧 Troubleshooting

### Lỗi: "Kết nối cơ sở dữ liệu thất bại"
- Kiểm tra file `.env` có cấu hình đúng
- Đảm bảo MySQL server đang chạy
- Kiểm tra quyền truy cập database

### Lỗi: "Tên đăng nhập không tồn tại"
- Kiểm tra username có trong cơ sở dữ liệu
- Đảm bảo `is_deleted = 0`

### Lỗi: "Mật khẩu không chính xác"
- Kiểm tra mật khẩu đúng chính tả
- Đảm bảo mật khẩu được mã hóa bằng Bcrypt

## 📞 Hỗ Trợ

Liên hệ: support@luvina.com.vn
