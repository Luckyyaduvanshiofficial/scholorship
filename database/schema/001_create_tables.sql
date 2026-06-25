-- ====================================================================
-- Tamboli Samaj Portal - Complete Database Schema (v3.0 - Simplified)
-- ====================================================================
-- Database: tamboli_samaj_portal
-- Tables: 11 (Simplified - merged scholarship & pratibha)
-- ====================================================================

-- ────────────────────────────────────────────────────────────────────
-- 1. USERS (Admin & Representatives only)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(249) NOT NULL UNIQUE,
    password VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    username VARCHAR(100) DEFAULT NULL,
    status TINYINT UNSIGNED NOT NULL DEFAULT 0,
    verified TINYINT UNSIGNED NOT NULL DEFAULT 0,
    resettable TINYINT UNSIGNED NOT NULL DEFAULT 1,
    roles_mask INT UNSIGNED NOT NULL DEFAULT 0,
    registered INT UNSIGNED NOT NULL,
    last_login INT UNSIGNED DEFAULT NULL,
    force_logout MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 2. STUDENTS (Student profiles + authentication)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
    id BIGINT UNSIGNED PRIMARY KEY,
    student_code VARCHAR(30) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    mobile VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    father_name VARCHAR(120) DEFAULT NULL,
    mother_name VARCHAR(120) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    district VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    pincode VARCHAR(10) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_mobile (mobile),
    INDEX idx_email (email),
    INDEX idx_student_code (student_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 3. ACADEMIC_SESSIONS (Year information)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS academic_sessions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_name VARCHAR(20) NOT NULL UNIQUE,
    is_active TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_name (session_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 4. STUDENT_ACADEMICS (Year-wise academic records)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS student_academics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    session_id BIGINT UNSIGNED NOT NULL,
    course_name VARCHAR(100) DEFAULT NULL,
    class_year VARCHAR(50) DEFAULT NULL,
    college_name VARCHAR(150) DEFAULT NULL,
    board_university VARCHAR(150) DEFAULT NULL,
    marks_obtained DECIMAL(8, 2) DEFAULT NULL,
    max_marks DECIMAL(8, 2) DEFAULT NULL,
    percentage DECIMAL(5, 2) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_session (student_id, session_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_session_id (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 5. APPLICATION_TYPES (Scholarship, Pratibha Samman)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default application types
INSERT IGNORE INTO application_types (name) VALUES
('Scholarship'),
('Pratibha Samman');

-- ────────────────────────────────────────────────────────────────────
-- 6. APPLICATION_STATUS (Pending, Approved, Rejected, Disputed)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_status (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default statuses
INSERT IGNORE INTO application_status (name) VALUES
('Pending'),
('Approved'),
('Rejected'),
('Disputed');

-- ────────────────────────────────────────────────────────────────────
-- 7. APPLICATIONS (Main table - merged scholarship & pratibha)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    session_id BIGINT UNSIGNED NOT NULL,
    application_type_id BIGINT UNSIGNED NOT NULL,
    status_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
    reviewed_by BIGINT UNSIGNED DEFAULT NULL,
    dispute_message TEXT DEFAULT NULL,
    submitted_at DATETIME DEFAULT NULL,
    type ENUM('scholarship', 'pratibha') NOT NULL,
    
    -- Scholarship fields (NULL if pratibha)
    family_income DECIMAL(12, 2) DEFAULT NULL,
    bank_name VARCHAR(100) DEFAULT NULL,
    account_number VARCHAR(30) DEFAULT NULL,
    ifsc_code VARCHAR(20) DEFAULT NULL,
    family_occupation VARCHAR(150) DEFAULT NULL,
    family_members_count INT DEFAULT NULL,
    earning_members_count INT DEFAULT NULL,
    current_class VARCHAR(50) DEFAULT NULL,
    current_college VARCHAR(150) DEFAULT NULL,
    prev_scholarship_received VARCHAR(10) DEFAULT NULL,
    scholarship_amt_2023_24 DECIMAL(10, 2) DEFAULT NULL,
    scholarship_amt_2024_25 DECIMAL(10, 2) DEFAULT NULL,
    scholarship_amt_2025_26 DECIMAL(10, 2) DEFAULT NULL,
    account_holder_name VARCHAR(100) DEFAULT NULL,
    career_goal VARCHAR(255) DEFAULT NULL,
    
    -- Pratibha fields (NULL if scholarship)
    achievement_title VARCHAR(200) DEFAULT NULL,
    achievement_category VARCHAR(100) DEFAULT NULL,
    achievement_level VARCHAR(100) DEFAULT NULL,
    rank_position VARCHAR(50) DEFAULT NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_student_session_type (student_id, session_id, application_type_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (application_type_id) REFERENCES application_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (status_id) REFERENCES application_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_student_id (student_id),
    INDEX idx_session_id (session_id),
    INDEX idx_status_id (status_id),
    INDEX idx_type (type),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 8. DOCUMENT_TYPES (Marksheet, Passbook, Photo, Certificate, etc.)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS document_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default document types
INSERT IGNORE INTO document_types (name) VALUES
('Photo'),
('Marksheet'),
('Passbook'),
('Certificate'),
('Aadhaar'),
('Signature'),
('Other');

-- ────────────────────────────────────────────────────────────────────
-- 9. APPLICATION_DOCUMENTS (Uploaded files)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    document_type_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) DEFAULT NULL,
    file_size BIGINT DEFAULT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE RESTRICT,
    
    INDEX idx_application_id (application_id),
    INDEX idx_document_type_id (document_type_id),
    INDEX idx_verification_status (verification_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 10. ANNOUNCEMENTS (Admin announcements)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS announcements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    is_active TINYINT DEFAULT 1,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 11. SETTINGS (Key-value configuration)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT IGNORE INTO settings (`key`, value) VALUES
('site_name', 'Tamboli Samaj Portal'),
('contact_email', 'admin@tamoli.org'),
('contact_phone', '+91-XXXXXXXXXX'),
('scholarship_open', '1'),
('pratibha_open', '1'),
('current_session_id', '1');

-- ────────────────────────────────────────────────────────────────────
-- 12. DELIGHT-IM/PHP-AUTH TABLES
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users_2fa (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  mechanism TINYINT UNSIGNED NOT NULL,
  seed VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  created_at INT UNSIGNED NOT NULL,
  expires_at INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY user_id_mechanism (user_id, mechanism),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED DEFAULT NULL,
  event_at INT UNSIGNED NOT NULL,
  event_type VARCHAR(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  admin_id BIGINT UNSIGNED DEFAULT NULL,
  ip_address VARCHAR(49) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  user_agent TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  details_json TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id),
  KEY event_at (event_at),
  KEY user_id_event_at (user_id, event_at),
  KEY user_id_event_type_event_at (user_id, event_type, event_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_confirmations (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(249) NOT NULL,
    selector VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    token VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    expires INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY selector (selector),
    KEY email_expires (email, expires),
    KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_otps (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  mechanism TINYINT UNSIGNED NOT NULL,
  single_factor TINYINT UNSIGNED NOT NULL DEFAULT '0',
  selector VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  token VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  expires_at INT UNSIGNED DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id_mechanism (user_id, mechanism),
  KEY selector_user_id (selector, user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_remembered (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user BIGINT UNSIGNED NOT NULL,
    selector VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    token VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    expires INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY selector (selector),
    KEY user (user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_resets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user BIGINT UNSIGNED NOT NULL,
    selector VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    token VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    expires INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY selector (selector),
    KEY user_expires (user, expires)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users_throttling (
    bucket VARCHAR(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    tokens FLOAT UNSIGNED NOT NULL,
    replenished_at INT UNSIGNED NOT NULL,
    expires_at INT UNSIGNED NOT NULL,
    PRIMARY KEY (bucket),
    KEY expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Database setup complete
-- Total tables: 18 (including delight-im/auth)
-- ====================================================================
