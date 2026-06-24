# Tamboli Samaj Portal — Final Folder Structure (Simplified v2.0)

**Stack**

* PHP 8.3
* MySQL 8
* Bootstrap 5
* Laragon
* Hostinger Shared Hosting

**Architecture Style**

* Lightweight MVC
* No framework
* Beginner-friendly
* Long-term maintainable
* No over-engineering
* **Simplified Workflow:** Approve / Reject / Dispute (No tracking)

---

```text
tamboli-samaj-portal/
│
├── .env
├── .env.example
├── .gitignore
├── index.php                    # Application entry point
│
├── app/
│   │
│   ├── config/
│   │   ├── app.php
│   │   ├── database.php
│   │   ├── constants.php
│   │   └── paths.php
│   │
│   ├── core/
│   │   ├── App.php
│   │   ├── Database.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   ├── Auth.php
│   │   ├── Validator.php
│   │   ├── FileUploader.php
│   │   ├── Logger.php
│   │   ├── Helpers.php
│   │   └── Response.php
│   │
│   ├── middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   ├── RepresentativeMiddleware.php
│   │   ├── StudentMiddleware.php
│   │   └── GuestMiddleware.php
│   │
│   ├── models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── AcademicSession.php
│   │   ├── StudentAcademic.php
│   │   ├── Application.php
│   │   ├── ApplicationDocument.php
│   │   ├── ApplicationType.php
│   │   ├── ApplicationStatus.php
│   │   ├── DocumentType.php
│   │   ├── Announcement.php
│   │   └── Setting.php
│   │
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── StudentController.php
│   │   ├── ProfileController.php
│   │   ├── ApplicationController.php
│   │   ├── AnnouncementController.php
│   │   ├── AdminController.php
│   │   └── SettingsController.php
│   │
│   ├── services/
│   │   ├── ApplicationService.php
│   │   ├── DocumentService.php
│   │   └── StatusService.php
│   │
│   ├── views/
│   │   │
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navbar.php
│   │   │   ├── sidebar.php
│   │   │   └── flash-message.php
│   │   │
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   └── forgot-password.php
│   │   │
│   │   ├── dashboard/
│   │   │   └── index.php
│   │   │
│   │   ├── profile/
│   │   │   ├── index.php
│   │   │   └── edit.php
│   │   │
│   │   ├── academics/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   └── edit.php
│   │   │
│   │   ├── applications/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── scholarship.php
│   │   │   ├── pratibha.php
│   │   │   ├── documents.php
│   │   │   └── show.php
│   │   │
│   │   ├── announcements/
│   │   │   ├── index.php
│   │   │   └── show.php
│   │   │
│   │   ├── admin/
│   │   │   ├── dashboard.php
│   │   │   ├── students.php
│   │   │   ├── applications.php
│   │   │   ├── application-show.php
│   │   │   ├── announcements.php
│   │   │   └── settings.php
│   │   │
│   │   └── errors/
│   │       ├── 401.php
│   │       ├── 403.php
│   │       ├── 404.php
│   │       └── 500.php
│   │
│   └── routes/
│       └── web.php
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── bootstrap.min.css
│   │   │   ├── style.css
│   │   │   └── admin.css
│   │   │
│   │   ├── js/
│   │   │   ├── bootstrap.bundle.min.js
│   │   │   ├── app.js
│   │   │   └── admin.js
│   │   │
│   │   ├── images/
│   │   │   ├── logo/
│   │   │   ├── banners/
│   │   │   └── icons/
│   │   │
│   │   └── favicon.ico
│   │
│   └── index.php
│
├── uploads/
│   │
│   ├── profile/
│   │
│   └── applications/
│       ├── marksheets/
│       ├── passbooks/
│       ├── certificates/
│       ├── photos/
│       └── other/
│
├── storage/
│   │
│   ├── logs/
│   │   └── app.log
│   │
│   ├── cache/
│   │
│   └── temp/
│
├── database/
│   │
│   ├── schema/
│   │
│   ├── seeds/
│   │
│   └── backups/
│
├── docs/
│   ├── Database-Schema.md
│   ├── ER-Diagram.md
│   ├── Folder-Structure.md
│   ├── Setup-Guide.md
│   ├── Deployment-Guide.md
│   └── API-Notes.md
│
└── vendor/
```

---

# Final Notes

## Changes from v1.0 to v3.0 (Hyper-Simplified)

### Removed (Over-Engineered)

❌ TrackingController.php — No complex tracking
❌ TrackingService.php — No tracking service
❌ ScholarshipDetail.php — Merged into Application
❌ PratibhaDetail.php — Merged into Application
❌ scholarship_details table — Merged into applications
❌ pratibha_details table — Merged into applications
❌ ApplicationStatusLog model — Simplified to dispute_message
❌ application_status_logs table — Not needed
❌ Complex status history — Simple Pending/Approved/Rejected/Disputed only
❌ ApplicationNumberGenerator.generate() — Use AUTO_INCREMENT + format()
❌ Complex Input.php methods (all, only, except, etc.) — Keep only post(), get(), file()
❌ Pagination.php methods — Not needed for MVP

### Kept (Necessary Only)

✅ Application model — Single table, all fields in one place
✅ ApplicationService — Business logic
✅ StatusService — Simple status updates
✅ Logger.php — Error tracking
✅ Auth.php — Core security
✅ Database.php — PDO wrapper
✅ Router.php — Lightweight routing
✅ Validator.php — Data validation
✅ Flash.php — User feedback messages
✅ CSRF.php — Security protection
✅ Input.php (minimal) — Just post(), get(), file()
✅ Response.php — View & error rendering
✅ Error pages — 401, 403, 404, 500
✅ Settings table — Portal configuration

---

## Simplified Workflow

**Old Complex Flow:**
Draft → Submitted → Under Review → Under Scrutiny → Approved/Rejected

**New Simple Flow:**
Pending → Approved ✅
       → Rejected ❌
       → Disputed ⚠️ (with admin message)

**What Student Sees:**
- Approved: ✅ Application Approved
- Not Approved: ❌ Application Not Approved
- Disputed: ⚠️ Application Disputed - [Admin's Message]

---

## Freeze Here

Do **not** redesign further.

Implementation order (updated):

1. Database schema (11 tables, simplified)
2. `.env` configuration
3. Database connection
4. Router initialization
5. Authentication (login/register)
6. Student profile module
7. Academic records module
8. Applications (Scholarship & Pratibha in ONE table)
9. Document uploads
10. Admin dashboard (approve/reject/dispute)
11. Announcements module
12. UI polish & responsive design

**Total Estimated Time:** 15-20 days (faster due to simplifications)

---

## Why Simplified?

- **< 100 applications per year** → No pagination needed
- **2 application types only** → Merge into one table
- **Simple approval workflow** → No complex tracking
- **Small team** → Less code to maintain
- **Fast to build** → Focus on features, not architecture