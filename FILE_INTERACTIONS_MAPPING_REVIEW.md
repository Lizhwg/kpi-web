# 📋 Phân tích tương tác - Trang xét duyệt đánh giá

## 🎯 Tổng quan

Trang **xét duyệt đánh giá** (`/KPI/kpi-web/mapping/review`) được xây dựng theo mô hình MVC:

```
┌─────────────────────────────────────────────────────────┐
│  USER                                                     │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  BROWSER - HTTP Request/Response                        │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  index.php (Entry Point)                                │
│  - Session & Auth check                                 │
│  - Routing                                              │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  routes/web.php (Routing Table)                         │
│  - GET /mapping/review → MappingController@review       │
│  - GET /mapping/detail → MappingController@detail       │
│  - POST /mapping/approve → MappingController@reviewApprove
│  - POST /mapping/edit → MappingController@reviewEdit    │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  MappingController                                       │
│  - review() - Hiển thị danh sách                        │
│  - detail() - Xem chi tiết                              │
│  - reviewApprove() - Phê duyệt                          │
│  - reviewEdit() - Chỉnh sửa người đánh giá              │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  DATABASE (Truy vấn SQL)                                │
│  - Evaluation_Headers                                   │
│  - Evaluation_Periods                                   │
│  - User                                                 │
│  - business_unit                                        │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  resources/views/mapping/review.php (View)              │
│  - HTML Template                                        │
│  - CSS Styling (Inline)                                 │
│  - JavaScript (Inline)                                  │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│  HTML Response → Browser                                │
└─────────────────────────────────────────────────────────┘
```

---

## 📁 Danh sách các file tương tác

### 1️⃣ **Entry Point & Routing**

| File | Tác vụ |
|------|--------|
| [index.php](../kpi-web/index.php) | Điểm vào chính - Load config, session, routing |
| [routes/web.php](../kpi-web/routes/web.php) | Định nghĩa routes (GET/POST) |

**Quy trình:**
- `index.php` kiểm tra session & auth
- Gọi routes từ `web.php`
- Route `/mapping/review` gọi `MappingController@review`

---

### 2️⃣ **Controller - Logic Xử lý**

| File | Method | Tác vụ |
|------|--------|--------|
| [MappingController.php](../kpi-web/app/Controllers/MappingController.php) | `__construct()` | Kiểm tra role = 2, kết nối DB |
| | `review()` | Lấy dữ liệu & hiển thị danh sách |
| | `detail()` | Xem chi tiết một bản đánh giá |
| | `reviewApprove()` | Xử lý phê duyệt (POST) |
| | `reviewEdit()` | Xử lý sửa người đánh giá (POST) |

**Chi tiết các method:**

#### `review()` - Hiển thị danh sách
```php
// Truy vấn:
- SELECT FROM Evaluation_Periods (kỳ đánh giá)
- SELECT FROM User WHERE role IN (3, 4) (PM/Manager)
- SELECT FROM Evaluation_Headers WHERE status = 'Chờ duyệt' (danh sách chờ duyệt)

// Include view:
resources/views/mapping/review.php
```

#### `reviewApprove()` - Phê duyệt
```php
// POST data:
- action: 'approve'
- selected_ids: [id1, id2, ...]

// Update:
UPDATE Evaluation_Headers SET status = 'Approved' WHERE id IN (...)

// Redirect:
/mapping/review
```

#### `reviewEdit()` - Chỉnh sửa người đánh giá
```php
// POST data:
- action: 'edit'
- selected_ids: [id1, id2, ...]
- evaluator_id: pm_id

// Update:
UPDATE Evaluation_Headers SET dev_head_id = ? WHERE id IN (...)

// Redirect:
/mapping/review
```

---

### 3️⃣ **Model - Truy vấn Database**

| File | Ghi chú |
|------|--------|
| [User.php](../kpi-web/app/Models/User.php) | Model người dùng (không trực tiếp dùng trong review) |
| [EvaluationHeader.php](../kpi-web/app/Models/EvaluationHeader.php) | **CHƯA TẠO** - Nên tạo để quản lý Evaluation_Headers |

**Ghi chú:** MappingController hiện viết SQL trực tiếp, không dùng model. Nên tạo `EvaluationHeader.php` model.

---

### 4️⃣ **View - Giao diện**

| File | Tác vụ |
|------|--------|
| [resources/views/mapping/review.php](../kpi-web/resources/views/mapping/review.php) | Template HTML chính |

**Nội dung review.php:**
- Thanh toolbar (filter, action button)
- Bảng danh sách nhân viên
- Checkbox chọn hàng
- Pagination
- 2 Modal:
  - `approveModal` - Xác nhận phê duyệt
  - `editModal` - Chọn người đánh giá mới

**JavaScript inline:**
```js
- toggleSelectAll() - Chọn tất cả
- updateSelectAll() - Cập nhật checkbox
- handleActionChange() - Thay đổi hành động
- executeAction() - Thực hiện hành động
- confirmApprove() - Xác nhận phê duyệt
- confirmEdit() - Xác nhận sửa
- prevPage() / nextPage() - Phân trang
```

---

### 5️⃣ **Database - Bảng dữ liệu**

| Bảng | Liên quan | Tác vụ |
|------|----------|--------|
| `Evaluation_Headers` | PRIMARY | Lưu bản đánh giá |
| `Evaluation_Periods` | Foreign Key | Kỳ đánh giá |
| `User` | Foreign Key | Người dùng (member, manager, dev_head) |
| `business_unit` | Foreign Key | Phòng ban |

**Mối quan hệ:**
```
Evaluation_Headers
├── member_id → User (người được đánh giá)
├── period_id → Evaluation_Periods (kỳ đánh giá)
├── manager_id → User (trưởng phòng)
├── dev_head_id → User (PM/người chấm)
└── hr_id → User (HRBP)
```

---

### 6️⃣ **Các View liên quan**

| File | Tác vụ |
|------|--------|
| [resources/views/mapping/detail.php](../kpi-web/resources/views/mapping/detail.php) | Chi tiết đánh giá (chưa dùng) |
| [resources/views/mapping/list.php](../kpi-web/resources/views/mapping/list.php) | Danh sách (chưa dùng) |

---

### 7️⃣ **Other Controllers - Liên quan**

| File | Method | Liên quan |
|------|--------|-----------|
| [AuthController.php](../kpi-web/app/Controllers/AuthController.php) | `handleLogin()` | Redirect role 2 → `/mapping/review` |
| | `login()` | Redirect role 2 → `/mapping/review` |

---

## 🔄 Flow chi tiết - Xét duyệt đánh giá

### 1. Truy cập trang
```
User → Login (secretary1)
  ↓
AuthController@handleLogin()
  ↓
Kiểm tra role = 2
  ↓
Redirect → /KPI/kpi-web/mapping/review
  ↓
index.php route matching
  ↓
MappingController@review()
```

### 2. Hiển thị danh sách
```
MappingController@review()
  ↓
Kết nối DB ($this->pdo)
  ↓
Query 3 bảng:
  - Evaluation_Periods (kỳ đánh giá)
  - User (PM/Manager)
  - Evaluation_Headers (chờ duyệt)
  ↓
Include review.php
  ↓
Render HTML + JavaScript
  ↓
Return browser
```

### 3. Phê duyệt
```
User nhấn "Chọn" checkboxes
  ↓
Chọn action "Phê duyệt"
  ↓
Nhấn "Thực hiện"
  ↓
JavaScript confirmApprove()
  ↓
POST /KPI/kpi-web/mapping/approve
  ↓
MappingController@reviewApprove()
  ↓
Validate & Update DB:
  UPDATE Evaluation_Headers SET status = 'Approved' WHERE id IN (...)
  ↓
Redirect → /mapping/review
  ↓
Reload page → Danh sách cập nhật
```

### 4. Chỉnh sửa người đánh giá
```
User chọn checkboxes
  ↓
Chọn action "Chỉnh sửa người đánh giá"
  ↓
Nhấn "Thực hiện"
  ↓
JavaScript confirmEdit()
  ↓
Chọn PM mới từ modal
  ↓
POST /KPI/kpi-web/mapping/edit
  ↓
MappingController@reviewEdit()
  ↓
Validate & Update DB:
  UPDATE Evaluation_Headers SET dev_head_id = ? WHERE id IN (...)
  ↓
Redirect → /mapping/review
  ↓
Reload page → Danh sách cập nhật
```

---

## 📊 Sơ đồ tương tác các file

```
index.php
    ↓
    ├─→ routes/web.php
    │       ↓
    │       └─→ MappingController
    │            ├─→ review()
    │            │    ├─→ Database Query
    │            │    │    ├─→ Evaluation_Headers
    │            │    │    ├─→ Evaluation_Periods
    │            │    │    ├─→ User
    │            │    │    └─→ business_unit
    │            │    └─→ review.php (View)
    │            │         ├─→ HTML Table
    │            │         ├─→ JavaScript (Modal, Pagination)
    │            │         └─→ Forms (POST)
    │            │
    │            ├─→ reviewApprove() [POST /mapping/approve]
    │            │    ├─→ Update DB
    │            │    └─→ Redirect review()
    │            │
    │            └─→ reviewEdit() [POST /mapping/edit]
    │                 ├─→ Update DB
    │                 └─→ Redirect review()
    │
    └─→ AuthController
         └─→ handleLogin()
              └─→ Redirect review() if role = 2
```

---

## 📝 Tệp cần tạo/sửa

| File | Trạng thái | Ghi chú |
|------|-----------|--------|
| `app/Models/EvaluationHeader.php` | ❌ CHƯA TẠO | Nên tạo để quản lý Evaluation_Headers |
| `resources/views/mapping/detail.php` | ⚠️ TỒN TẠI | Chưa dùng, chỉ khai báo route |
| `resources/views/mapping/list.php` | ⚠️ TỒN TẠI | Chưa dùng |

---

## 🎯 Tóm tắt

**Trang xét duyệt đánh giá tương tác với:**

1. **Entry:** `index.php` → `routes/web.php`
2. **Logic:** `MappingController` (4 methods)
3. **Data:** `Evaluation_Headers`, `Evaluation_Periods`, `User`, `business_unit`
4. **View:** `review.php` (HTML + CSS + JS inline)
5. **Routes:**
   - `GET /mapping/review` → Display
   - `POST /mapping/approve` → Approve
   - `POST /mapping/edit` → Edit evaluator

**Không có:**
- External CSS files (CSS inline trong HTML)
- External JS files (JS inline trong HTML)
- Separate Model class (SQL viết trực tiếp)

---

## 💡 Gợi ý cải tiến

1. **Tạo Model:** `EvaluationHeader.php` - Quản lý logic DB
2. **Tách CSS:** `public/css/mapping.css` - CSS riêng
3. **Tách JS:** `public/js/mapping-review.js` - JS riêng
4. **Hoàn thiện detail view:** `resources/views/mapping/detail.php`
5. **Thêm logging:** Debug các thay đổi
