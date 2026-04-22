# 🔧 HƯỚNG DẪN CẤU HÌNH VÀ FIX LỖI 404

## 1️⃣ KIỂM TRA XAMPP

Đảm bảo:
- ✅ Apache đang chạy (green)
- ✅ MySQL đang chạy (green)

---

## 2️⃣ TẠO DATABASE

### Cách 1: Sử dụng phpMyAdmin

1. Mở **http://localhost/phpmyadmin**
2. Vào tab **SQL**
3. Copy-paste nội dung file: `c:\install\xampp-v2\htdocs\KPI\database\01_create_tables.sql`
4. Nhấp **Execute** (tạo bảng)
5. Sau đó copy-paste: `c:\install\xampp-v2\htdocs\KPI\database\02_insert_sample_data.sql`
6. Nhấp **Execute** (insert dữ liệu)

### Cách 2: Sử dụng Command Line

```bash
cd c:\install\xampp-v2\mysql\bin

# Tạo database và bảng
mysql -u root -p kpi_db < c:\install\xampp-v2\htdocs\KPI\database\01_create_tables.sql

# Insert dữ liệu mẫu
mysql -u root -p kpi_db < c:\install\xampp-v2\htdocs\KPI\database\02_insert_sample_data.sql
```

---

## 3️⃣ CẤU HÌNH .env

Mở file: `c:\install\xampp-v2\htdocs\KPI\kpi-web\.env`

Điền thông tin database:

```ini
DB_HOST=localhost
DB_NAME=kpi_db
DB_USER=root
DB_PASS=

APP_ENV=development
APP_DEBUG=true
APP_NAME=KPI System
APP_URL=http://localhost/KPI/kpi-web

REDMINE_API_URL=http://localhost:8080/api
REDMINE_API_KEY=your-secret-api-key-here

SESSION_NAME=kpi_session
SESSION_TIMEOUT=3600
```

**Lưu ý:** 
- `DB_PASS=` (để trống nếu password mặc định)
- Nếu MySQL có password, điền vào `DB_PASS=your_password`

---

## 4️⃣ KIỂM TRA MOD_REWRITE

Mở file `c:\install\xampp-v2\apache\conf\httpd.conf` và tìm dòng:

```apache
#LoadModule rewrite_module modules/mod_rewrite.so
```

**Bỏ dấu `#` ở đầu dòng** (nếu còn):

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Sau đó **restart Apache**.

---

## 5️⃣ RESTART APACHE & MYSQL

```bash
# Dừng Apache
net stop Apache2.4

# Khởi động lại Apache
net start Apache2.4

# Nếu dùng GUI XAMPP, nhấp "Stop" rồi "Start"
```

---

## 6️⃣ TRUY CẬP ỨNG DỤNG

### URL chính:

```
http://localhost/KPI/kpi-web/
```

### Đăng nhập:

```
http://localhost/KPI/kpi-web/login
```

### Tài khoản test:

```
Username: admin
Password: password123
```

---

## 7️⃣ TROUBLESHOOT

### ❌ Vẫn hiển thị lỗi 404?

1. **Kiểm tra Apache error log:**
   ```
   c:\install\xampp-v2\apache\logs\error.log
   ```

2. **Kiểm tra MySQL connection:**
   - Mở `http://localhost/phpmyadmin`
   - Chọn database `kpi_db`
   - Kiểm tra có các bảng: User, business_unit, Evaluation_Periods, ...

3. **Kiểm tra .htaccess tồn tại:**
   ```
   c:\install\xampp-v2\htdocs\KPI\kpi-web\.htaccess
   ```

4. **Xem Apache error.log:**
   Trong XAMPP Control Panel → Apache → Logs

### ❌ Lỗi "Kết nối cơ sở dữ liệu thất bại"?

1. Kiểm tra `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` trong `.env`
2. Khởi động lại MySQL
3. Chạy lại SQL scripts để tạo database

### ❌ Lỗi "504 Gateway Timeout"?

1. Restart Apache
2. Kiểm tra quá trình MySQL
3. Tăng timeout trong `.env`: `SESSION_TIMEOUT=7200`

---

## 8️⃣ KIỂM TRA CẤU HÌNH HOÀN CHỈNH

Chạy các lệnh sau để verify:

```bash
# Kiểm tra Apache mod_rewrite
apache2ctl -M | findstr rewrite

# Kiểm tra MySQL database
mysql -u root -p kpi_db -e "SHOW TABLES;"

# Kiểm tra file .htaccess
type c:\install\xampp-v2\htdocs\KPI\kpi-web\.htaccess

# Kiểm tra file .env
type c:\install\xampp-v2\htdocs\KPI\kpi-web\.env
```

---

## 9️⃣ CÓ CÂU HỎI?

Kiểm tra các file hướng dẫn khác:
- [LOGIN_GUIDE.md](../LOGIN_GUIDE.md) - Hướng dẫn đăng nhập
- [01_create_tables.sql](../database/01_create_tables.sql) - Schema cơ sở dữ liệu
- [02_insert_sample_data.sql](../database/02_insert_sample_data.sql) - Dữ liệu mẫu
