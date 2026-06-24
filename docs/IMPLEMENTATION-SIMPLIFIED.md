# Tamboli Samaj Portal — Simplified Implementation Roadmap

**Version:** 2.0 (Simplified)
**Architecture:** Lightweight PHP MVC
**Workflow:** Simple Approval + Dispute System (No Complex Tracking)

---

## Key Changes from v1.0

- ❌ Removed: Complex status tracking system
- ❌ Removed: `application_status_logs` table
- ✅ Added: Simple `dispute_message` column in applications
- ✅ Simplified: Student only sees Approved / Rejected / Disputed
- ✅ Simplified: Admin can approve, reject, or dispute (with message)

---

## Application Status Workflow

```
Student Submits Application
         ↓
     PENDING
         ↓
    Admin Reviews
         ├─→ APPROVED ✅ (Student sees: Approved)
         ├─→ REJECTED ❌ (Student sees: Not Approved)
         └─→ DISPUTED ⚠️ (Student sees: Disputed + Admin's Message)
```

---

## Implementation Phases

### Phase 1: Project Bootstrap
- `.env` configuration
- Database connection
- Router initialization
- Session handling
- Error logging
- **Duration:** 1-2 days

### Phase 2: Authentication System
- Student registration (email/mobile)
- Student login/logout
- Admin login
- Session management
- Password hashing
- **Duration:** 2-3 days

### Phase 3: Student Profile Module
- Create profile (personal details, address)
- Edit profile
- Profile photo upload
- **Duration:** 2 days

### Phase 4: Academic Module
- Add academic year details
- Manage education records
- **Duration:** 1 day

### Phase 5: Application Module
- Create application (Scholarship / Pratibha Samman)
- Auto-generate application number (TSVS-2026-000001)
- Application status: Pending → Approved/Rejected/Disputed
- Student views result
- **Duration:** 3 days

### Phase 6: Document Upload Module
- Upload marksheet, passbook, certificate, photo
- File validation (type, size)
- Store in `uploads/applications/{type}/`
- **Duration:** 2 days

### Phase 7: Admin Dashboard
- View all applications
- Approve/Reject applications
- Mark as Disputed (with message)
- Export applications list
- **Duration:** 3 days

### Phase 8: Representative Dashboard
- View assigned applications
- Add remarks (optional)
- **Duration:** 1 day

### Phase 9: Announcements Module
- Admin creates announcements
- Students view announcements
- **Duration:** 1 day

---

## Database Schema (Simplified)

**Total Tables: 11** (simplified from 12)
- Removed `scholarship_details` table  
- Removed `pratibha_details` table
- Merged all fields into `applications` table using ENUM type column
- Single JOIN per query instead of multiple
- Simpler model code

**Estimated Time Reduction:** 3-5 days faster

---

## Simplified Code & Database

### Code Simplifications
- ✅ ApplicationNumberGenerator: From 50 lines → 10 lines (use AUTO_INCREMENT)
- ✅ Input.php: From 120 lines → 25 lines (only post/get/file)
- ✅ Pagination.php: From 50 lines → Empty (not needed)
- ❌ Removed: ScholarshipDetail, PratibhaDetail models
- ❌ Removed: scholarship_details, pratibha_details tables

### Database Simplifications
```sql
-- BEFORE: 3 separate tables
SELECT a.*, s.family_income, s.bank_name, p.achievement_title
FROM applications a
LEFT JOIN scholarship_details s ON a.id = s.application_id
LEFT JOIN pratibha_details p ON a.id = p.application_id

-- AFTER: 1 table, no JOINs needed
SELECT * FROM applications WHERE id = 1
```

### Result
- 50% less code
- 2 fewer database tables
- Easier to understand and maintain
- 15-20 day implementation (vs 19-24 days)

### Student Features
- ✅ Register with email/mobile
- ✅ Create & edit profile
- ✅ Add academic records
- ✅ Apply for Scholarship
- ✅ Apply for Pratibha Samman
- ✅ Upload documents
- ✅ View application status (Approved / Rejected / Disputed)
- ✅ See dispute message if applicable

### Admin Features
- ✅ Review all applications
- ✅ Approve applications
- ✅ Reject applications
- ✅ Mark Disputed with message
- ✅ View student details & documents
- ✅ Export application reports
- ✅ Publish announcements

### Representative Features
- ✅ View assigned applications
- ✅ Add remarks

---

## Core Files to Build (Simplified List)

### Models (11 files, not 13)
- User.php
- Student.php
- Application.php ← **Single model, handles both types**
- ApplicationDocument.php
- AcademicSession.php
- StudentAcademic.php
- ApplicationType.php
- ApplicationStatus.php
- DocumentType.php
- Announcement.php
- Setting.php

### Controllers (7 files)
- AuthController.php
- ProfileController.php
- ApplicationController.php
- AdminController.php
- AnnouncementController.php
- DashboardController.php
- StudentController.php

### Services (3 files)
- ApplicationService.php
- DocumentService.php
- StatusService.php

---

## Build Order

1. **Database Schema** → Create all 12 tables
2. **Config & Connection** → `.env` setup
3. **Auth System** → Student login/register
4. **Student Profile** → Create & edit profile
5. **Academic Records** → Add academic year data
6. **Application Form** → Create application
7. **Document Upload** → Upload files
8. **Admin Dashboard** → Review & approve/reject
9. **Dispute System** → Add dispute message
10. **Announcements** → Publish & view
11. **UI Polish** → Style & responsive design
12. **Testing & Deployment** → Test all flows

--- (Revised)

| Phase | Duration |
|-------|----------|
| Phase 1 | 1-2 days |
| Phase 2 | 2-3 days |
| Phase 3 | 1-2 days |
| Phase 4 | 1 day |
| Phase 5 | 2-3 days |
| Phase 6 | 1-2 days |
| Phase 7 | 2-3 days |
| Phase 8 | 1 day |
| Phase 9 | 1 day |
| Testing & Polish | 2-3 days |
| **Total** | **15-20
## Deployment

- **Hosting:** Hostinger Shared Hosting
- **PHP Version:** 8.3
- **Database:** MySQL 8
- **Entry Point:** `public/index.php`
- **Server:** Apache with .htaccess (no index.php in URL)

---

## Estimated Timeline

| Phase | Duration |
|-------|----------|
| Phase 1 | 1-2 days |
| Phase 2 | 2-3 days |
| Phase 3 | 2 days |
| Phase 4 | 1 day |
| Phase 5 | 3 days |
| Phase 6 | 2 days |
| Phase 7 | 3 days |
| Phase 8 | 1 day |
| Phase 9 | 1 day |
| Testing & Polish | 3-5 days |
| **Total** | **19-24 days** |

---

## Success Criteria

- ✅ Students can apply online without manual forms
- ✅ Admin can approve/reject/dispute quickly
- ✅ Simple interface (no over-engineering)
- ✅ Works on shared hosting
- ✅ Scalable for multiple years
- ✅ Secure password storage
- ✅ Document uploads working
