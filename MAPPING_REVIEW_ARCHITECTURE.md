# 🎨 Biểu đồ tương tác - Trang xét duyệt đánh giá

## Architecture Diagram

```
┌────────────────────────────────────────────────────────────┐
│                        USER BROWSER                         │
└────────────────────────────────────────────────────────────┘
                            │
                   ┌─────────▼─────────┐
                   │  HTTP Request     │
                   │ GET /mapping/review│
                   └─────────┬─────────┘
                            │
┌────────────────────────────▼─────────────────────────────────┐
│                        index.php                              │
│                                                               │
│  1. Load Config (.env)                                       │
│  2. Session Start                                            │
│  3. Auth Check                                               │
│  4. Routing Logic                                            │
└────────────────────────────┬─────────────────────────────────┘
                            │
        ┌───────────────────▼───────────────────┐
        │   routes/web.php                      │
        │   GET /mapping/review →               │
        │   MappingController@review            │
        └───────────────────┬───────────────────┘
                            │
┌───────────────────────────▼──────────────────────────────────┐
│             MappingController                                │
│                                                               │
│  __construct()                                               │
│  ├─ Check Session                                            │
│  ├─ Check Role = 2 (Thư ký)                                 │
│  └─ Connect Database                                         │
│                                                               │
│  review()                                                    │
│  ├─ Query Evaluation_Periods                                │
│  ├─ Query User (PM/Manager)                                 │
│  ├─ Query Evaluation_Headers (Chờ duyệt)                    │
│  └─ include review.php                                      │
│                                                               │
│  reviewApprove() [POST]                                      │
│  ├─ Validate input                                           │
│  ├─ UPDATE Evaluation_Headers status='Approved'              │
│  └─ Redirect review()                                       │
│                                                               │
│  reviewEdit() [POST]                                         │
│  ├─ Validate input                                           │
│  ├─ UPDATE Evaluation_Headers dev_head_id=new_pm             │
│  └─ Redirect review()                                       │
└───────────────────────────┬──────────────────────────────────┘
                            │
        ┌───────────────────▼───────────────────┐
        │   Database Connection                 │
        │   (PDO MySQL)                         │
        └───────────────────┬───────────────────┘
                            │
        ┌───────────────────▼───────────────────┐
        │   Database Tables                     │
        │                                       │
        │   Evaluation_Headers                  │
        │   ├─ id                               │
        │   ├─ member_id → User                 │
        │   ├─ period_id → Evaluation_Periods   │
        │   ├─ manager_id → User                │
        │   ├─ dev_head_id → User               │
        │   ├─ status                           │
        │   └─ final_total_score                │
        │                                       │
        │   Evaluation_Periods                  │
        │   ├─ id                               │
        │   ├─ period_name                      │
        │   ├─ start_date                       │
        │   ├─ end_date                         │
        │   └─ status                           │
        │                                       │
        │   User                                │
        │   ├─ id                               │
        │   ├─ username                         │
        │   ├─ password (hash)                  │
        │   ├─ role                             │
        │   └─ business_unit_id → business_unit │
        │                                       │
        │   business_unit                       │
        │   ├─ id                               │
        │   └─ bu_name                          │
        └────────────────────────────────────────┘
                            │
        ┌───────────────────▼───────────────────┐
        │  resources/views/mapping/review.php   │
        │                                       │
        │  HTML + CSS (Inline) + JS (Inline)    │
        │                                       │
        │  Components:                          │
        │  ├─ Toolbar (Filter, Action)          │
        │  ├─ Table (Danh sách)                 │
        │  ├─ Pagination                        │
        │  ├─ Modal Approve                     │
        │  └─ Modal Edit                        │
        │                                       │
        │  JavaScript Functions:                │
        │  ├─ toggleSelectAll()                 │
        │  ├─ handleActionChange()              │
        │  ├─ executeAction()                   │
        │  ├─ confirmApprove()                  │
        │  ├─ confirmEdit()                     │
        │  └─ prevPage() / nextPage()           │
        └────────────────────────────────────────┘
                            │
                   ┌─────────▼─────────┐
                   │  HTTP Response    │
                   │  HTML Page        │
                   └─────────┬─────────┘
                            │
└───────────────────────────▼────────────────────────────────┐
│                        Browser Rendering                   │
│                                                            │
│   Display Table with 5 items                              │
│   Checkboxes selectable                                   │
│   Modal interactive                                       │
└────────────────────────────────────────────────────────────┘
```

---

## Data Flow - Phê duyệt

```
┌──────────────────────────────────────────────┐
│  User checks checkboxes on review.php        │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  User selects "Phê duyệt" from dropdown      │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  JavaScript: handleActionChange()            │
│  - Show "Thực hiện" button                   │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  User clicks "Thực hiện"                     │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  JavaScript: executeAction()                 │
│  - Gather selected IDs                       │
│  - Show approveModal                         │
│  - Show count of items                       │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  User confirms in Modal                      │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  JavaScript: confirmApprove()                │
│  - Create FormData                           │
│  - POST to /mapping/approve                  │
│  - Include: action, selected_ids             │
└──────────────────────┬───────────────────────┘
                       │
                   ┌───▼────┐
                   │ Network│
                   └───┬────┘
                       │
┌──────────────────────▼───────────────────────┐
│  index.php                                   │
│  Route: POST /mapping/approve                │
│  → MappingController@reviewApprove()         │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  MappingController@reviewApprove()           │
│  1. Get selected_ids from POST               │
│  2. Validate                                 │
│  3. UPDATE Evaluation_Headers                │
│     SET status = 'Approved'                  │
│     WHERE id IN (selected_ids)               │
│  4. Return JSON response                     │
└──────────────────────┬───────────────────────┘
                       │
                   ┌───▼────┐
                   │ Network│
                   └───┬────┘
                       │
┌──────────────────────▼───────────────────────┐
│  Browser receives response                   │
│  JavaScript redirects to /mapping/review     │
└──────────────────────┬───────────────────────┘
                       │
┌──────────────────────▼───────────────────────┐
│  Page reloads                                │
│  - MappingController@review()                │
│  - Query DB (items now have Approved status) │
│  - Display updated table                     │
└──────────────────────────────────────────────┘
```

---

## File Dependencies

```
index.php
    │
    ├── .env (Config)
    │
    ├── routes/web.php
    │
    ├── app/Controllers/
    │   ├── AuthController.php
    │   └── MappingController.php
    │        ├── Database (PDO)
    │        │   ├── Evaluation_Headers
    │        │   ├── Evaluation_Periods
    │        │   ├── User
    │        │   └── business_unit
    │        │
    │        └── resources/views/mapping/review.php
    │             ├── HTML Structure
    │             ├── CSS (Inline <style>)
    │             ├── JavaScript (Inline <script>)
    │             │   ├── DOM manipulation
    │             │   ├── Modal control
    │             │   ├── Form submission
    │             │   └── Pagination
    │             └── PHP loops for data display

resources/
    └── views/
        ├── auth/
        │   ├── login.php (not directly related)
        │   ├── register.php (not directly related)
        │   └── dashboard.php (links to review)
        └── mapping/
            ├── review.php (MAIN)
            ├── detail.php (not used)
            └── list.php (not used)

app/Models/
    ├── User.php (not directly used)
    └── EvaluationHeader.php (NOT CREATED YET)
```

---

## Route Map

| Route | Method | Handler | View |
|-------|--------|---------|------|
| `/mapping/review` | GET | `MappingController@review()` | `review.php` |
| `/mapping/approve` | POST | `MappingController@reviewApprove()` | JSON response |
| `/mapping/edit` | POST | `MappingController@reviewEdit()` | JSON response |
| `/mapping/detail` | GET | `MappingController@detail()` | `detail.php` (not used) |

---

## Summary

**Trang xét duyệt đánh giá tương tác với:**

1. ✅ **index.php** - Entry point & routing
2. ✅ **routes/web.php** - Route definitions
3. ✅ **MappingController.php** - Main business logic
4. ✅ **Database** - 4 tables (Headers, Periods, User, BusinessUnit)
5. ✅ **review.php** - Main view with inline CSS & JS
6. ❌ **EvaluationHeader.php** - Model (Not created)
7. ⚠️ **detail.php** & **list.php** - Views (Not used)

**Total file count: 7 main files**
