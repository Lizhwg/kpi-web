-- ============================================================================
-- KPI System - File nháp ch?a các câu INSERT data test
-- Không ghi dè lên các file database ban d?u
-- ============================================================================

USE kpi_system;

-- ============================================================================
-- 1. INSERT BUSINESS UNITS (Phòng ban)
-- ============================================================================
INSERT IGNORE INTO business_unit (id, bu_name, is_deleted) VALUES
(5, 'Phòng Test IT', FALSE),
(6, 'Phòng Test HR', FALSE);

-- ============================================================================
-- 2. INSERT EVALUATION PERIODS (K? dánh giá)
-- ============================================================================
INSERT IGNORE INTO Evaluation_Periods (id, period_name, start_date, end_date, status) VALUES
(5, 'Ðánh giá KPI Test 2026', '2026-01-01', '2026-12-31', 'Open');

-- ============================================================================
-- 3. INSERT USERS (Ngu?i dùng) - Password: password123
-- Hash bcrypt cho password123: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================================
INSERT IGNORE INTO User (id, business_unit_id, username, password, role, is_deleted) VALUES
-- Thu ký test (role 2)
(100, 5, 'test_secretary', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, FALSE),

-- PM/Manager test (role 3, 4)
(101, 5, 'test_manager1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(102, 5, 'test_manager2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),

-- Nhân viên test (role 6)
(103, 5, 'test_employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(104, 5, 'test_employee2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(105, 5, 'test_employee3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE);

-- ============================================================================
-- 4. INSERT EVALUATION HEADERS (Ðánh giá ch? duy?t)
-- ============================================================================
INSERT IGNORE INTO Evaluation_Headers (id, member_id, period_id, status, manager_id, dev_head_id, hr_id, final_total_score) VALUES
-- Nhân viên 1: manager m?c d?nh là test_manager1
(1001, 103, 5, 'Ch? duy?t', 101, 102, NULL, 8.5),

-- Nhân viên 2: manager m?c d?nh là test_manager1
(1002, 104, 5, 'Ch? duy?t', 101, 102, NULL, 7.8),

-- Nhân viên 3: manager m?c d?nh là test_manager1
(1003, 105, 5, 'Ch? duy?t', 101, 102, NULL, 8.2);

-- ============================================================================
-- HU?NG D?N TEST
-- ============================================================================
--
-- 1. Ch?y các câu INSERT trên vào database kpi_system
--
-- 2. Ðang nh?p v?i:
--    Username: test_secretary
--    Password: password123
--
-- 3. Vào trang xét duy?t dánh giá (/mapping/review)
--    - S? th?y 3 nhân viên ch? duy?t
--    - Ngu?i dánh giá m?c d?nh: test_manager1
--    - Có th? phê duy?t ho?c thay d?i ngu?i dánh giá thành test_manager2
--
-- 4. Ð? test v?i phòng ban khác, thêm user v?i business_unit_id = 6
--    và t?o thêm Evaluation_Headers tuong ?ng
--
-- ============================================================================
