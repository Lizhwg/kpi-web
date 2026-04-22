-- ============================================================================
-- KPI System - Database Schema
-- Database: kpi_system
-- ============================================================================

-- Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS kpi_system 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database
USE kpi_system;

-- Cấu hình sử dụng charset utf8mb4 để hỗ trợ tốt tiếng Việt
SET NAMES utf8mb4;
-- Tạm tắt kiểm tra khóa ngoại để tạo bảng không bị lỗi thứ tự
SET FOREIGN_KEY_CHECKS = 0; 

-- 1. Tạo bảng Phòng ban (business_unit)
CREATE TABLE business_unit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bu_name VARCHAR(255) NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tạo bảng Nhóm tiêu chí (Evaluation_Groups)
CREATE TABLE Evaluation_Groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tạo bảng Kỳ đánh giá (Evaluation_Periods)
CREATE TABLE Evaluation_Periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(100) NOT NULL COMMENT 'VD: Đánh giá KPI Quý 1/2026',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Open', 'Closed') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tạo bảng Người dùng (User) - ĐÃ CẬP NHẬT PASSWORD
CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_unit_id INT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Mật khẩu lưu dạng Hash (Bcrypt)',
    role TINYINT NOT NULL COMMENT '1: Admin, 2: Thư ký, 3: PM, 4: Trưởng phòng, 5: HRBP, 6: Nhân viên',
    is_deleted BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (business_unit_id) REFERENCES business_unit(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tạo bảng Tiêu chí chính (Evaluation_Criteria)
CREATE TABLE Evaluation_Criteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_groups_id INT NOT NULL,
    business_unit_id INT,
    criteria_name VARCHAR(255) NOT NULL,
    total_weight DECIMAL(5,2) NOT NULL,
    effective_date DATE NOT NULL COMMENT 'Ngày hiệu lực của bộ tiêu chí',
    FOREIGN KEY (evaluation_groups_id) REFERENCES Evaluation_Groups(id),
    FOREIGN KEY (business_unit_id) REFERENCES business_unit(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tạo bảng Hạng mục đánh giá (Assessment_Items)
CREATE TABLE Assessment_Items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_criteria_id INT NOT NULL,
    content_description VARCHAR(255) NOT NULL,
    item_weight DECIMAL(5,2) NOT NULL,
    source_type ENUM('Manual', 'Redmine') DEFAULT 'Manual' COMMENT 'Manual: PM tự chấm, Redmine: Hệ thống tự tính',
    FOREIGN KEY (evaluation_criteria_id) REFERENCES Evaluation_Criteria(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tạo bảng Mức độ (score_level)
CREATE TABLE score_level (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_item_id INT NOT NULL,
    score_level INT NOT NULL,
    definition VARCHAR(255) NOT NULL,
    FOREIGN KEY (assessment_item_id) REFERENCES Assessment_Items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tạo bảng Tổng kết đánh giá (Evaluation_Headers)
CREATE TABLE Evaluation_Headers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    period_id INT NOT NULL,
    status ENUM('Chưa đánh giá', 'Chờ duyệt', 'Đánh giá lại', 'Chờ HRBP', 'Hoàn thành') DEFAULT 'Chưa đánh giá',
    manager_id INT,
    dev_head_id INT,
    hr_id INT,
    final_total_score FLOAT DEFAULT NULL,
    FOREIGN KEY (member_id) REFERENCES User(id),
    FOREIGN KEY (period_id) REFERENCES Evaluation_Periods(id),
    FOREIGN KEY (manager_id) REFERENCES User(id),
    FOREIGN KEY (dev_head_id) REFERENCES User(id),
    FOREIGN KEY (hr_id) REFERENCES User(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tạo bảng Chi tiết điểm số (Evaluations)
CREATE TABLE Evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_header_id INT NOT NULL,
    assessment_items_id INT NOT NULL,
    manager_score DECIMAL(5,2) DEFAULT NULL,
    dev_head_score DECIMAL(5,2) DEFAULT NULL,
    system_score DECIMAL(5,2) DEFAULT NULL COMMENT 'Điểm đồng bộ từ Redmine',
    head_note VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (evaluation_header_id) REFERENCES Evaluation_Headers(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_items_id) REFERENCES Assessment_Items(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bật lại kiểm tra khóa ngoại sau khi tạo xong
SET FOREIGN_KEY_CHECKS = 1;