-- ====================================================================
-- Tamboli Samaj Portal - Database Schema v4.0 (10-Year Stable)
-- ====================================================================
-- Changes from v3.0:
-- 1. Dropped `type` ENUM from applications (redundant with application_type_id)
-- 2. Split applications into parent + child tables (scholarship_details, pratibha_details)
-- 3. Replaced hardcoded year columns with scholarship_history table
-- 4. Expanded application_status from 4 to 7 states
-- 5. Added application_no, self-declaration, correction tracking, audit columns
-- 6. Added application_history audit table
-- 7. Added application_counters for race-free number generation
-- 8. Added application_disputes table
-- 9. Added soft deletes (deleted_at) to critical tables
-- 10. Added CHECK constraints for data integrity
-- 11. Added sessions table for cross-subdomain auth
-- 12. Added events, event_registrations, blog_posts for main website
-- ====================================================================

-- ────────────────────────────────────────────────────────────────────
-- 1. USERS (Delight Auth - Admin, Reps, Students, General Members)
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
-- 2. STUDENTS (Student profiles - 1:1 with users.id)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
    id BIGINT UNSIGNED PRIMARY KEY,
    student_code VARCHAR(30) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    dob DATE DEFAULT NULL,
    mobile VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    father_name VARCHAR(120) DEFAULT NULL,
    mother_name VARCHAR(120) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    district VARCHAR(100) DEFAULT NULL,
    state VARCHAR(100) DEFAULT NULL,
    pincode VARCHAR(10) DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    status TINYINT DEFAULT 1,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_mobile (mobile),
    INDEX idx_email (email),
    INDEX idx_student_code (student_code),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NOTE: mobile/email are NOT UNIQUE here. If students self-register, they are unique by nature.
-- If parents register children, remove UNIQUE from mobile/email and handle duplicates in PHP.

-- ────────────────────────────────────────────────────────────────────
-- 3. ACADEMIC SESSIONS
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS academic_sessions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    session_name VARCHAR(20) NOT NULL UNIQUE,
    is_active TINYINT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session_name (session_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 4. STUDENT_ACADEMICS (One per student per session - shared across applications)
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
    INDEX idx_session_id (session_id),
    CONSTRAINT chk_percentage CHECK (percentage >= 0 AND percentage <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 5. APPLICATION_TYPES
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO application_types (name) VALUES
('Scholarship'),
('Pratibha Samman');

-- ────────────────────────────────────────────────────────────────────
-- 6. APPLICATION_STATUS (7 states for complete workflow)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_status (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO application_status (id, name) VALUES
(1, 'Draft'),
(2, 'Submitted'),
(3, 'Under Review'),
(4, 'Approved'),
(5, 'Rejected'),
(6, 'Pending Correction'),
(7, 'Resubmitted');

-- ────────────────────────────────────────────────────────────────────
-- 7. APPLICATIONS (Lean parent table - only shared fields)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS applications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_no VARCHAR(50) DEFAULT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    session_id BIGINT UNSIGNED NOT NULL,
    application_type_id BIGINT UNSIGNED NOT NULL,
    status_id BIGINT UNSIGNED NOT NULL DEFAULT 1,

    -- Self-declaration (legal compliance)
    self_declared TINYINT(1) DEFAULT 0,
    self_declared_at DATETIME DEFAULT NULL,
    self_declared_ip VARCHAR(45) DEFAULT NULL,

    -- Submission tracking
    submitted_at DATETIME DEFAULT NULL,
    submitted_ip VARCHAR(45) DEFAULT NULL,
    resubmitted_at DATETIME DEFAULT NULL,

    -- Correction window tracking
    correction_count INT DEFAULT 0,
    correction_deadline DATETIME DEFAULT NULL,

    -- Admin workflow
    reviewed_by BIGINT UNSIGNED DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    admin_remarks TEXT DEFAULT NULL,

    -- Audit
    created_by BIGINT UNSIGNED DEFAULT NULL,
    updated_by BIGINT UNSIGNED DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_application_no (application_no),
    UNIQUE KEY unique_student_session_type (student_id, session_id, application_type_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (application_type_id) REFERENCES application_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (status_id) REFERENCES application_status(id) ON DELETE RESTRICT,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_student_id (student_id),
    INDEX idx_session_id (session_id),
    INDEX idx_status_id (status_id),
    INDEX idx_application_type_id (application_type_id),
    INDEX idx_submitted_at (submitted_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_correction_deadline (correction_deadline),

    CONSTRAINT chk_correction_count CHECK (correction_count >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 8. SCHOLARSHIP_DETAILS (Child table - 1:1 with applications)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS scholarship_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    family_income DECIMAL(12, 2) DEFAULT NULL,
    bank_name VARCHAR(100) DEFAULT NULL,
    account_number VARCHAR(30) DEFAULT NULL,
    ifsc_code VARCHAR(20) DEFAULT NULL,
    account_holder_name VARCHAR(100) DEFAULT NULL,
    family_occupation VARCHAR(150) DEFAULT NULL,
    family_members_count INT DEFAULT NULL,
    earning_members_count INT DEFAULT NULL,
    current_class VARCHAR(50) DEFAULT NULL,
    current_college VARCHAR(150) DEFAULT NULL,
    career_goal VARCHAR(255) DEFAULT NULL,
    prev_scholarship_received VARCHAR(10) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (application_id),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    CONSTRAINT chk_family_income CHECK (family_income >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 9. SCHOLARSHIP_HISTORY (Replaces hardcoded year columns)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS scholarship_history (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    session_year VARCHAR(10) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    UNIQUE KEY (application_id, session_year),
    INDEX idx_session_year (session_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 10. PRATIBHA_DETAILS (Child table - 1:1 with applications)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pratibha_details (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    achievement_title VARCHAR(200) DEFAULT NULL,
    achievement_category VARCHAR(100) DEFAULT NULL,
    achievement_level VARCHAR(100) DEFAULT NULL,
    rank_position VARCHAR(50) DEFAULT NULL,
    family_occupation VARCHAR(150) DEFAULT NULL,
    family_members_count INT DEFAULT NULL,
    earning_members_count INT DEFAULT NULL,
    career_goal VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (application_id),
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 11. APPLICATION_COUNTERS (Race-free number generation)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_counters (
    year INT NOT NULL,
    type VARCHAR(20) NOT NULL,
    counter INT NOT NULL DEFAULT 0,
    PRIMARY KEY (year, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 12. APPLICATION_HISTORY (Audit trail - every action logged)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_history (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(50) NOT NULL,
    performed_by BIGINT UNSIGNED NOT NULL,
    performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    old_data JSON DEFAULT NULL,
    new_data JSON DEFAULT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_app_id (application_id),
    INDEX idx_action (action),
    INDEX idx_performed_at (performed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 13. APPLICATION_DISPUTES (Replaces single dispute_message column)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_disputes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    raised_by BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open','resolved','rejected') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME DEFAULT NULL,
    resolved_by BIGINT UNSIGNED DEFAULT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (raised_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_application_id (application_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 14. DOCUMENT_TYPES
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS document_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO document_types (name) VALUES
('Photo'),
('Marksheet'),
('Passbook'),
('Certificate'),
('Aadhaar'),
('Signature'),
('Other');

-- ────────────────────────────────────────────────────────────────────
-- 15. APPLICATION_DOCUMENTS (Improved with audit fields)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS application_documents (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    application_id BIGINT UNSIGNED NOT NULL,
    document_type_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    storage_path VARCHAR(500) NOT NULL DEFAULT '',
    mime_type VARCHAR(100) DEFAULT NULL,
    file_size BIGINT DEFAULT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    uploaded_by BIGINT UNSIGNED DEFAULT NULL,
    uploaded_ip VARCHAR(45) DEFAULT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (document_type_id) REFERENCES document_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_application_id (application_id),
    INDEX idx_document_type_id (document_type_id),
    INDEX idx_verification_status (verification_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 16. ANNOUNCEMENTS (with soft delete and scope)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS announcements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    is_active TINYINT DEFAULT 1,
    scope ENUM('portal','site','both') DEFAULT 'portal',
    created_by BIGINT UNSIGNED NOT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_scope (scope),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 17. SETTINGS
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (`key`, value) VALUES
('site_name', 'Tamboli Samaj Portal'),
('contact_email', 'admin@tamoli.org'),
('contact_phone', '+91-XXXXXXXXXX'),
('scholarship_open', '1'),
('pratibha_open', '1'),
('current_session_id', '1');

-- ────────────────────────────────────────────────────────────────────
-- 18. SESSIONS (Cross-subdomain database sessions)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) NOT NULL PRIMARY KEY,
    data TEXT NOT NULL,
    last_access INT(10) UNSIGNED NOT NULL,
    INDEX idx_last_access (last_access)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 19. EVENTS (Main website)
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS events (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    description LONGTEXT,
    event_date DATETIME NOT NULL,
    location VARCHAR(255),
    image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    registration_required TINYINT(1) DEFAULT 0,
    max_participants INT DEFAULT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_date (event_date),
    INDEX idx_is_active (is_active),
    INDEX idx_slug (slug),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 20. EVENT REGISTRATIONS
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS event_registrations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    event_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    mobile VARCHAR(20),
    status ENUM('registered','attended','cancelled') DEFAULT 'registered',
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_user (event_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 21. BLOG POSTS
-- ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id BIGINT UNSIGNED DEFAULT NULL,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    published_at DATETIME NULL,
    deleted_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status_published (status, published_at),
    INDEX idx_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────────────
-- 22. DELIGHT-IM/PHP-AUTH TABLES (Unchanged)
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
-- Total tables: 28 (including delight-im/auth)
-- ====================================================================
