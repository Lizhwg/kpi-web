-- ============================================================================
-- KPI System - Dữ liệu Mẫu
-- Chứa dữ liệu mẫu để test hệ thống
-- ============================================================================

-- Sử dụng database
USE kpi_system;

-- Tạm tắt kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- 1. Thêm dữ liệu vào bảng business_unit (Phòng ban)
-- ============================================================================

INSERT INTO business_unit (id, bu_name, is_deleted) VALUES
(1, 'Phòng Quản lý Dự án (PM)', FALSE),
(2, 'Phòng Phát triển (Dev)', FALSE),
(3, 'Phòng Kiểm thử (QA)', FALSE),
(4, 'Phòng Hỗ trợ Kỹ thuật (Support)', FALSE);

-- ============================================================================
-- 2. Thêm dữ liệu vào bảng Evaluation_Groups (Nhóm tiêu chí)
-- ============================================================================

INSERT INTO Evaluation_Groups (id, group_name) VALUES
(1, 'Kỹ năng chuyên môn'),
(2, 'Năng suất công việc'),
(3, 'Tinh thần đội nhóm'),
(4, 'Phát triển bản thân'),
(5, 'Tuân thủ quy tắc');

-- ============================================================================
-- 3. Thêm dữ liệu vào bảng Evaluation_Periods (Kỳ đánh giá)
-- ============================================================================

INSERT INTO Evaluation_Periods (id, period_name, start_date, end_date, status) VALUES
(1, 'Đánh giá KPI Quý 1/2026', '2026-01-01', '2026-03-31', 'Open'),
(2, 'Đánh giá KPI Quý 2/2026', '2026-04-01', '2026-06-30', 'Open'),
(3, 'Đánh giá KPI Quý 3/2026', '2026-07-01', '2026-09-30', 'Closed'),
(4, 'Đánh giá KPI Quý 4/2025', '2025-10-01', '2025-12-31', 'Closed');

-- ============================================================================
-- 4. Thêm dữ liệu vào bảng User (Người dùng)
-- Password: password123 (hash bcrypt)
-- ============================================================================

INSERT INTO User (id, business_unit_id, username, password, role, is_deleted) VALUES
-- Admin
(1, NULL, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, FALSE),

-- Thư ký (Role 2)
(2, NULL, 'secretary_hr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, FALSE),

-- PM (Role 3) - Phòng PM
(3, 1, 'pm_leader', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(4, 1, 'pm_member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(5, 1, 'pm_member2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),

-- PM (Role 3) - Phòng Dev
(6, 2, 'dev_lead', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(7, 2, 'dev_senior', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(8, 2, 'dev_member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(9, 2, 'dev_member2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),

-- PM (Role 3) - Phòng QA
(10, 3, 'qa_lead', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(11, 3, 'qa_member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),

-- PM (Role 3) - Phòng Support
(12, 4, 'support_lead', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),
(13, 4, 'support_member1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, FALSE),

-- Trưởng phòng (Role 4)
(14, 1, 'pm_dept_head', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),
(15, 2, 'dev_dept_head', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),
(16, 3, 'qa_dept_head', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),
(17, 4, 'support_dept_head', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, FALSE),

-- HRBP (Role 5)
(18, NULL, 'hrbp_officer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 5, FALSE),

-- Nhân viên thường (Role 6)
(19, 2, 'employee1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(20, 2, 'employee2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE),
(21, 3, 'employee3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 6, FALSE);

-- ============================================================================
-- 5. Thêm dữ liệu vào bảng Evaluation_Criteria (Tiêu chí đánh giá)
-- ============================================================================

INSERT INTO Evaluation_Criteria (id, evaluation_groups_id, business_unit_id, criteria_name, total_weight, effective_date) VALUES
-- Nhóm Kỹ năng chuyên môn
(1, 1, 2, 'Kỹ năng lập trình', 40.00, '2026-01-01'),
(2, 1, 3, 'Kỹ năng kiểm thử', 40.00, '2026-01-01'),
(3, 1, 4, 'Kỹ năng hỗ trợ kỹ thuật', 40.00, '2026-01-01'),
(4, 1, 1, 'Kỹ năng quản lý dự án', 40.00, '2026-01-01'),

-- Nhóm Năng suất công việc
(5, 2, 2, 'Số lượng code hoàn thành', 35.00, '2026-01-01'),
(6, 2, 3, 'Số lượng test case', 35.00, '2026-01-01'),
(7, 2, 4, 'Số ticket giải quyết', 35.00, '2026-01-01'),
(8, 2, 1, 'Số dự án quản lý', 35.00, '2026-01-01'),

-- Nhóm Tinh thần đội nhóm
(9, 3, NULL, 'Hợp tác và giao tiếp', 25.00, '2026-01-01'),

-- Nhóm Phát triển bản thân
(10, 4, NULL, 'Học tập và cải thiện kỹ năng', 20.00, '2026-01-01'),

-- Nhóm Tuân thủ quy tắc
(11, 5, NULL, 'Tuân thủ quy định công ty', 30.00, '2026-01-01');

-- ============================================================================
-- 6. Thêm dữ liệu vào bảng Assessment_Items (Hạng mục đánh giá)
-- ============================================================================

INSERT INTO Assessment_Items (id, evaluation_criteria_id, content_description, item_weight, source_type) VALUES
-- Kỹ năng lập trình (Dev)
(1, 1, 'Viết code sạch và theo chuẩn', 20.00, 'Manual'),
(2, 1, 'Kiến thức về thiết kế hệ thống', 20.00, 'Manual'),
(3, 1, 'Khả năng debug và tối ưu hóa', 20.00, 'Manual'),
(4, 1, 'Sử dụng công cụ phát triển hiệu quả', 20.00, 'Redmine'),

-- Kỹ năng kiểm thử (QA)
(5, 2, 'Thiết kế test case hiệu quả', 20.00, 'Manual'),
(6, 2, 'Phát hiện bug kỹ lưỡng', 20.00, 'Manual'),
(7, 2, 'Kiến thức về automation testing', 20.00, 'Manual'),
(8, 2, 'Báo cáo bug rõ ràng và chi tiết', 20.00, 'Manual'),

-- Kỹ năng hỗ trợ
(9, 3, 'Giải quyết vấn đề nhanh chóng', 20.00, 'Manual'),
(10, 3, 'Giao tiếp tốt với khách hàng', 20.00, 'Manual'),
(11, 3, 'Tài liệu hỗ trợ đầy đủ', 20.00, 'Manual'),
(12, 3, 'Tinh thần phục vụ tích cực', 20.00, 'Manual'),

-- Kỹ năng quản lý dự án
(13, 4, 'Quản lý tiến độ dự án', 20.00, 'Manual'),
(14, 4, 'Quản lý rủi ro hiệu quả', 20.00, 'Manual'),
(15, 4, 'Giao tiếp với stakeholder', 20.00, 'Manual'),
(16, 4, 'Báo cáo trạng thái đủ đầy', 20.00, 'Manual'),

-- Số lượng code hoàn thành (Dev)
(17, 5, 'Số dòng code/tính năng hoàn thành', 100.00, 'Redmine'),

-- Số lượng test case (QA)
(18, 6, 'Số test case được tạo', 100.00, 'Redmine'),

-- Số ticket giải quyết (Support)
(19, 7, 'Số ticket được giải quyết', 100.00, 'Redmine'),

-- Số dự án quản lý (PM)
(20, 8, 'Số dự án quản lý thành công', 100.00, 'Redmine'),

-- Hợp tác và giao tiếp
(21, 9, 'Tham gia tích cực vào các hoạt động nhóm', 50.00, 'Manual'),
(22, 9, 'Giao tiếp hiệu quả với các thành viên', 50.00, 'Manual'),

-- Học tập và cải thiện
(23, 10, 'Tham gia các khóa đào tạo', 50.00, 'Manual'),
(24, 10, 'Chia sẻ kiến thức với đồng nghiệp', 50.00, 'Manual'),

-- Tuân thủ quy định
(25, 11, 'Tuân thủ giờ làm việc', 50.00, 'Manual'),
(26, 11, 'Tuân thủ các quy tắc công ty', 50.00, 'Manual');

-- ============================================================================
-- 7. Thêm dữ liệu vào bảng score_level (Mức độ điểm số)
-- ============================================================================

INSERT INTO score_level (id, assessment_item_id, score_level, definition) VALUES
-- Mục 1: Viết code sạch
(1, 1, 1, 'Code không sạch, không theo chuẩn'),
(2, 1, 2, 'Code ít nhất vẫn tuân theo chuẩn cơ bản'),
(3, 1, 3, 'Code sạch, dễ đọc, tuân theo chuẩn'),
(4, 1, 4, 'Code rất sạch, tối ưu, dễ bảo trì'),
(5, 1, 5, 'Code xuất sắc, được đánh giá cao'),

-- Mục 2: Kiến thức thiết kế hệ thống
(6, 2, 1, 'Hiểu biết hạn chế về thiết kế'),
(7, 2, 2, 'Có kiến thức cơ bản về thiết kế'),
(8, 2, 3, 'Thành thạo thiết kế hệ thống'),
(9, 2, 4, 'Rất tốt trong thiết kế kiến trúc'),
(10, 2, 5, 'Chuyên gia trong thiết kế hệ thống'),

-- Mục 5: Test case
(11, 5, 1, 'Test case không đầy đủ'),
(12, 5, 2, 'Test case cơ bản'),
(13, 5, 3, 'Test case tốt, bao phủ nhiều trường hợp'),
(14, 5, 4, 'Test case rất tốt, chi tiết'),
(15, 5, 5, 'Test case xuất sắc');

-- ============================================================================
-- 8. Thêm dữ liệu vào bảng Evaluation_Headers (Đầu mục đánh giá)
-- ============================================================================

INSERT INTO Evaluation_Headers (id, member_id, period_id, status, manager_id, dev_head_id, hr_id, final_total_score) VALUES
-- Đánh giá nhân viên dev_member1 cho kỳ 1
(1, 8, 1, 'Chờ duyệt', 6, 15, NULL, NULL),

-- Đánh giá nhân viên dev_member2 cho kỳ 1
(2, 9, 1, 'Chờ HRBP', 6, 15, 18, 8.5),

-- Đánh giá nhân viên qa_member1 cho kỳ 1
(3, 11, 1, 'Hoàn thành', 10, 16, 18, 8.0),

-- Đánh giá nhân viên employee1 cho kỳ 1
(4, 19, 1, 'Chưa đánh giá', 6, 15, NULL, NULL),

-- Đánh giá nhân viên employee2 cho kỳ 1
(5, 20, 1, 'Chờ duyệt', 6, 15, NULL, NULL),

-- Đánh giá nhân viên pm_member1 cho kỳ 1
(6, 4, 1, 'Hoàn thành', 3, 14, 18, 7.8),

-- Đánh giá nhân viên employee3 cho kỳ 2
(7, 21, 2, 'Chưa đánh giá', 10, 16, NULL, NULL);

-- ============================================================================
-- 9. Thêm dữ liệu vào bảng Evaluations (Chi tiết điểm số)
-- ============================================================================

INSERT INTO Evaluations (id, evaluation_header_id, assessment_items_id, manager_score, dev_head_score, system_score, head_note) VALUES
-- Đánh giá dev_member2 (ID 9) - Hoàn thành
(1, 2, 1, 8.5, 8.0, NULL, 'Viết code khá sạch'),
(2, 2, 2, 8.0, 8.5, NULL, 'Hiểu rõ thiết kế hệ thống'),
(3, 2, 3, 9.0, 8.5, NULL, 'Giỏi debug vấn đề'),
(4, 2, 4, NULL, 8.0, 7.5, 'Sử dụng công cụ tốt'),
(5, 2, 17, NULL, NULL, 8.0, 'Hoàn thành 45 tính năng'),

-- Đánh giá qa_member1 (ID 11) - Hoàn thành
(6, 3, 5, 8.0, 8.0, NULL, 'Thiết kế test case tốt'),
(7, 3, 6, 8.5, 8.0, NULL, 'Phát hiện bug tốt'),
(8, 3, 7, 7.5, 7.5, NULL, 'Chưa học automation'),
(9, 3, 8, 8.0, 8.0, NULL, 'Báo cáo rõ ràng'),
(10, 3, 18, NULL, NULL, 8.0, 'Tạo 150 test case'),

-- Đánh giá pm_member1 (ID 4) - Hoàn thành
(11, 6, 13, 8.0, 7.5, NULL, 'Quản lý tiến độ tốt'),
(12, 6, 14, 7.5, 7.5, NULL, 'Xử lý rủi ro ổn'),
(13, 6, 15, 8.0, 8.0, NULL, 'Giao tiếp tốt'),
(14, 6, 16, 7.5, 7.5, NULL, 'Báo cáo đầy đủ'),
(15, 6, 20, NULL, NULL, 8.0, 'Quản lý 3 dự án');

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- Ghi chú quan trọng
-- ============================================================================
-- 1. Password mặc định cho tất cả user: password123 (hash Bcrypt)
-- 2. Vai trò (Role):
--    - 1: Admin
--    - 2: Thư ký (Secretary)
--    - 3: PM (Project Manager / Team Lead)
--    - 4: Trưởng phòng (Department Head)
--    - 5: HRBP (Human Resources Business Partner)
--    - 6: Nhân viên thường (Regular Employee)
-- 3. Trạng thái đánh giá:
--    - Chưa đánh giá (Not Yet Evaluated)
--    - Chờ duyệt (Waiting for Approval)
--    - Đánh giá lại (Re-evaluation)
--    - Chờ HRBP (Waiting for HRBP)
--    - Hoàn thành (Completed)
-- ============================================================================
