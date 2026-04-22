-- ============================================================================
-- KPI System - Dữ liệu kiểm thử cho màn hình Xét duyệt đánh giá
-- ============================================================================

USE kpi_system;

-- ============================================================================
-- 0. DELETE DATA IN CORRECT ORDER (Respect Foreign Keys)
-- ============================================================================
-- Must delete child tables first, then parent tables
DELETE FROM Evaluation_Headers;
DELETE FROM Evaluation_Periods;
DELETE FROM User;
DELETE FROM business_unit;

-- ============================================================================
-- 1. PHÒNG BAN (Business Units)
-- ============================================================================
INSERT INTO business_unit (bu_name, is_deleted) VALUES 
('Phòng IT', 0),
('Phòng Nhân sự', 0),
('Phòng Kinh doanh', 0),
('Phòng Kỹ thuật', 0);

-- ============================================================================
-- 2. KỲ ĐÁNH GIÁ (Evaluation Periods)
-- ============================================================================

INSERT INTO Evaluation_Periods (period_name, start_date, end_date, status) VALUES 
('Đánh giá KPI Quý 1/2026', '2026-01-01', '2026-03-31', 'Open'),
('Đánh giá KPI Quý 2/2026', '2026-04-01', '2026-06-30', 'Open'),
('Đánh giá KPI Quý 3/2026', '2026-07-01', '2026-09-30', 'Closed');

-- ============================================================================
-- 3. NGƯỜI DÙNG (Users) - Password: password123
-- ============================================================================
INSERT INTO User (business_unit_id, username, password, role, is_deleted) VALUES 
-- Thư ký (Role 2) - Người duyệt
(1, 'secretary1', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 2, 0),

-- PM (Role 3) - Người chấm
(1, 'pm1', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 3, 0),
(2, 'pm2', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 3, 0),

-- Trưởng phòng (Role 4) - Người quản lý
(1, 'head1', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 4, 0),
(2, 'head2', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 4, 0),

-- HRBP (Role 5)
(1, 'hrbp1', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 5, 0),

-- Nhân viên (Role 6) - Người được đánh giá
(1, 'nhvien1', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 6, 0),
(1, 'nhvien2', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 6, 0),
(1, 'nhvien3', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 6, 0),
(2, 'nhvien4', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 6, 0),
(2, 'nhvien5', '$2y$10$TxuZ0xyM0n5yKB8vGz0d.uWz5Z0q5vK5K5K5K5K5K5K5K5K5K5K5C', 6, 0);

-- ============================================================================
-- 4. ĐÁNH GIÁ HEADER (Evaluation_Headers) - Dữ liệu xét duyệt
-- ============================================================================
-- Lấy các ID cần thiết
SET @period_1 = (SELECT id FROM Evaluation_Periods WHERE period_name = 'Đánh giá KPI Quý 1/2026');
SET @member_1 = (SELECT id FROM User WHERE username = 'nhvien1');
SET @member_2 = (SELECT id FROM User WHERE username = 'nhvien2');
SET @member_3 = (SELECT id FROM User WHERE username = 'nhvien3');
SET @member_4 = (SELECT id FROM User WHERE username = 'nhvien4');
SET @member_5 = (SELECT id FROM User WHERE username = 'nhvien5');
SET @pm_1 = (SELECT id FROM User WHERE username = 'pm1');
SET @head_1 = (SELECT id FROM User WHERE username = 'head1');
SET @head_2 = (SELECT id FROM User WHERE username = 'head2');

INSERT INTO Evaluation_Headers (member_id, period_id, status, manager_id, dev_head_id, hr_id, final_total_score) VALUES 
-- Chờ duyệt (status = 'Chờ duyệt')
(@member_1, @period_1, 'Chờ duyệt', @head_1, @pm_1, NULL, 8.5),
(@member_2, @period_1, 'Chờ duyệt', @head_1, @pm_1, NULL, 7.8),
(@member_3, @period_1, 'Chờ duyệt', @head_1, @pm_1, NULL, 8.2),
(@member_4, @period_1, 'Chờ duyệt', @head_2, @pm_2, NULL, 7.5),
(@member_5, @period_1, 'Chờ duyệt', @head_2, @pm_2, NULL, 8.0);

-- ============================================================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================================================
-- 
-- Đăng nhập bằng:
--   Username: secretary1
--   Password: password123
--
-- Người dùng được tạo:
--   - secretary1 (Thư ký) - Người xét duyệt
--   - pm1, pm2 (PM) - Người đánh giá
--   - head1, head2 (Trưởng phòng) - Người quản lý
--   - hrbp1 (HRBP)
--   - nhvien1-5 (Nhân viên) - Người được đánh giá
--
-- Dữ liệu đánh giá:
--   - 5 nhân viên chờ duyệt (status = 'Chờ duyệt')
--   - Mỗi nhân viên có điểm số, PM và trưởng phòng được gán
--   - Kỳ đánh giá: Quý 1/2026
