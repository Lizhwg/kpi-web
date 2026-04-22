# 📊 Hướng dẫn tạo dữ liệu kiểm thử - Màn hình xét duyệt đánh giá

## 🚀 Cách nhanh nhất (Khuyên dùng)

### Bước 1: Truy cập tool tạo dữ liệu
```
http://yourserver/KPI/database/insert_test_data.php
```

### Bước 2: Nhấn nút "✅ Tạo dữ liệu kiểm thử"
- Tool sẽ tự động tạo:
  - 4 phòng ban
  - 3 kỳ đánh giá
  - 11 người dùng
  - 5 bản đánh giá chờ duyệt

### Bước 3: Đăng nhập và kiểm tra
```
URL: http://yourserver/KPI/kpi-web/login
Username: secretary1
Password: password123
```

Sẽ tự động chuyển hướng đến trang xét duyệt với 5 nhân viên chờ duyệt.

---

## 📋 Dữ liệu được tạo

### Tài khoản
| Username | Password | Vai trò | Ghi chú |
|----------|----------|--------|--------|
| secretary1 | password123 | Thư ký | 👤 Người xét duyệt (TEST) |
| pm1 | password123 | PM | 📊 Người chấm điểm |
| pm2 | password123 | PM | 📊 Người chấm điểm |
| head1 | password123 | Trưởng phòng | 👔 Người quản lý |
| head2 | password123 | Trưởng phòng | 👔 Người quản lý |
| hrbp1 | password123 | HRBP | 👥 Nhân sự |
| nhvien1-5 | password123 | Nhân viên | Người được đánh giá |

### Phòng ban
- Phòng IT
- Phòng Nhân sự
- Phòng Kinh doanh
- Phòng Kỹ thuật

### Kỳ đánh giá
- Đánh giá KPI Quý 1/2026 (Open)
- Đánh giá KPI Quý 2/2026 (Open)
- Đánh giá KPI Quý 3/2026 (Closed)

### Bản đánh giá (Chờ duyệt)
| Nhân viên | Trưởng phòng | PM | Điểm |
|-----------|-------------|-----|------|
| nhvien1 | head1 | pm1 | 8.5 |
| nhvien2 | head1 | pm1 | 7.8 |
| nhvien3 | head1 | pm1 | 8.2 |
| nhvien4 | head2 | pm2 | 7.5 |
| nhvien5 | head2 | pm2 | 8.0 |

---

## 🔧 Phương án thay thế: Sử dụng SQL Script

Nếu không thể truy cập PHP tool, hãy chạy SQL script:

### MySQL CLI
```sql
USE kpi_system;
SOURCE /path/to/KPI/database/03_insert_test_data.sql;
```

### Lưu ý
- File SQL cần được sửa password hash (hiện tại là placeholder)
- Cách dễ hơn là dùng PHP tool ở trên

---

## ✅ Kiểm tra kết quả

### Cách 1: Từ tool tạo dữ liệu
- Tool sẽ hiển thị danh sách tài khoản, kỳ đánh giá, và bản đánh giá được tạo

### Cách 2: Trực tiếp từ database
```sql
SELECT COUNT(*) as total FROM User;
SELECT COUNT(*) as total FROM Evaluation_Periods;
SELECT COUNT(*) as total FROM Evaluation_Headers WHERE status = 'Chờ duyệt';
```

### Cách 3: Từ trang xét duyệt
1. Đăng nhập: secretary1 / password123
2. Sẽ thấy bảng với 5 nhân viên chờ duyệt

---

## 🧪 Test các tính năng

### Test 1: Phê duyệt một nhân viên
1. Chọn checkbox cho nhân viên
2. Chọn action "Phê duyệt"
3. Nhấn "Thực hiện"
4. Xác nhận

### Test 2: Chỉnh sửa người đánh giá
1. Chọn checkbox cho một hoặc nhiều nhân viên
2. Chọn action "Chỉnh sửa người đánh giá"
3. Chọn PM mới
4. Nhấn "Thực hiện"

### Test 3: Xem chi tiết
1. Nhấn vào ID hoặc tên nhân viên
2. Xem chi tiết đánh giá

---

## ⚠️ Lưu ý quan trọng

1. **Chạy lại tool**: Lần chạy tiếp theo sẽ **xóa dữ liệu cũ** và tạo mới
2. **Backup trước**: Nếu có dữ liệu quan trọng, backup database trước
3. **Password mặc định**: Tất cả tài khoản đều dùng password `password123`
4. **Role**: Chỉ role = 2 (Thư ký) mới có thể xét duyệt

---

## 🆘 Khắc phục sự cố

### Không thấy dữ liệu sau khi tạo
- Refresh trang (Ctrl+F5)
- Logout rồi login lại
- Kiểm tra console browser (F12) xem có lỗi không

### Insert thất bại
- Kiểm tra file `.env` có cấu hình DB đúng không
- Kiểm tra database connection
- Xem error message chi tiết

### Tài khoản không thể đăng nhập
- Kiểm tra username/password có khớp không
- Kiểm tra tài khoản có `is_deleted = 0` không
- Kiểm tra browser cookies

---

## 📚 Tài liệu liên quan

- [Cấu trúc Database](01_create_tables.sql)
- [Trang xét duyệt](../kpi-web/resources/views/mapping/review.php)
- [Controller xét duyệt](../kpi-web/app/Controllers/MappingController.php)

---

**Chúc bạn test thành công! 🎉**
