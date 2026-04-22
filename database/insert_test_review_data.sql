-- ============================================================================
-- KPI System - D? li?u test cho ch?c nang xét duy?t dánh giá
-- ============================================================================

USE kpi_system;

-- ============================================================================
-- 1. Thêm business_unit n?u chua có
-- ============================================================================
INSERT IGNORE INTO business_unit (id, bu_name, is_deleted) VALUES
(5, 'Phòng Test IT', FALSE),
(6, 'Phòng Test HR', FALSE);

-- ============================================================================
-- 2. Thêm k? dánh giá n?u chua có
-- ============================================================================
INSERT IGNORE INTO Evaluation_Periods (id, period_name, start_date, end_date, status) VALUES
(5, 'Ðánh giá KPI Test 2026', '2026-01-01', '2026-12-31', 'Open');

-- ============================================================================
-- 3. Thêm user test - Password: password123
-- ============================================================================
INSERT IGNORE INTO User (id, business_unit_id, username, password, role, is_deleted) VALUES
-- Thu ký test
(100, 5, 'test_secretary', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, FALSE),

-- PM/Manager test
(101, 5, 'test_manager1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(102, 5, 'test_manager2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),

-- Nhân viên test
(103, 5, 'test_employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(104, 5, 'test_employee2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(105, 5, 'test_employee3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE);

-- ============================================================================
-- 4. Thêm Evaluation_Headers test
-- ============================================================================
INSERT IGNORE INTO Evaluation_Headers (id, member_id, period_id, status, manager_id, dev_head_id, hr_id, final_total_score) VALUES
(1001, 103, 5, 'Ch? duy?t', 101, 102, NULL, 8.5),
(1002, 104, 5, 'Ch? duy?t', 101, 102, NULL, 7.8),
(1003, 105, 5, 'Ch? duy?t', 101, 102, NULL, 8.2);
