# Tamboli Samaj Portal (TSP)

## Product Requirements Document (PRD) v1.0

### Project Overview

Tamboli Samaj Portal is a web-based platform for managing scholarship applications, Pratibha Samman registrations, announcements, and future community services through a single digital portal.

The goal is to replace manual PDF forms, WhatsApp document collection, and spreadsheet management with a centralized system.

---

# Vision

Create a long-term digital platform for Tamboli Samaj that can be used every year for:

* Scholarship Applications
* Pratibha Samman Registrations
* Event Registrations
* Community Announcements
* Member Services

The platform should be simple enough for non-technical administrators and scalable enough for future growth.

---

# Objectives

### Student Side

* Apply online
* Upload documents
* View application result (Approved / Not Approved / Disputed)
* See admin's dispute message if application is disputed
* Avoid duplicate data entry
* Receive application reference number

### Representative Side

* Review applications
* Verify submitted details
* Add remarks

### Admin Side

* View all applications
* Approve/Reject applications
* Mark application as Disputed (with message)
* Export reports
* Publish announcements

---

# User Roles

## Guest

Permissions:

* View homepage
* View announcements
* View event details
* Register account
* Login

---

## Student

Permissions:

* Create profile
* Edit profile
* Upload documents
* Apply for Scholarship
* Apply for Pratibha Samman
* View application result (Approved / Not Approved / Disputed)
* View dispute message (if any)
* Download acknowledgement

---

## Representative

Permissions:

* View assigned applications
* Verify applications
* Add remarks
* Recommend approval

---

## Admin

Permissions:

* Manage users
* View all applications
* Approve/Reject applications
* Mark dispute with message
* Manage announcements
* Export reports
* Manage portal settings

---

# Modules

## Module 1 – Homepage

Sections:

* Hero Banner
* Event Information
* Announcement Board
* Important Dates
* Statistics
* Contact Information
* Footer

---

## Module 2 – Student Registration

Fields:

* Full Name
* Father/Guardian Name
* Mobile Number
* Email (Optional)
* Address
* Password

Features:

* Mobile OTP (Future)
* Email Verification (Future)

---

## Module 3 – Student Profile

Fields:

* Photo
* Personal Details
* Address
* Education Details
* Family Information

Student fills once.

Used for all future applications.

---

## Module 4 – Scholarship Application

Fields based on official form:

* Passed Class
* Percentage
* Current Class
* School/College Name
* Previous Scholarship Details
* Bank Details
* Future Career Goal
* Marksheet Upload
* Bank Passbook Upload

---

## Module 5 – Pratibha Samman Registration

Fields:

* Category
* Academic Details
* Marksheet Upload

System auto-fetches profile details.

No duplicate entry.

---

## Module 6 – Document Management

Supported:

* JPG
* PNG
* PDF

Documents:

* Student Photo
* Marksheet
* Passbook

---

## Module 7 – Application Tracking

Student sees:

Reference Number

Example:

TSP-2026-000123

Status:

* Submitted
* Under Verification
* Under Review
* Approved
* Rejected
* Dispute
* Scholarship Released

---

## Module 8 – Announcements

Admin can:

* Create Announcement
* Pin Announcement
* Set Expiry Date

Homepage displays latest notices.

---

## Module 9 – Admin Dashboard

Cards:

* Total Applications
* Approved
* Pending
* Rejected
* Scholarship Amount Distributed

Charts:

* Applications by Year
* Applications by District

---

## Module 10 – Representative Dashboard

Representative can:

* View assigned applications
* Verify details
* Add notes
* Recommend approval

---

# Application Workflow

Student Registration

↓

Create Profile

↓

Upload Documents

↓

Choose:

* Scholarship
* Pratibha Samman
* Both

↓

Submit

↓

Representative Verification

↓

Committee Review

↓

Approved / Rejected / Dispute

↓

Scholarship Distribution

---

# Database Entities

## students

Stores profile information.

---

## applications

Stores yearly applications.

---

## documents

Stores uploaded files.

---

## admins

Stores admin accounts.

---

## representatives

Stores representative accounts.

---

## announcements

Stores notices.

---

## activity_logs

Stores audit history.

---

# Technology Stack

Backend:
PHP 8.3

Database:
MySQL 8

Frontend:
HTML5
Bootstrap 5
Bootstrap Icons

Authentication:
PHP Sessions

Hosting:
Hostinger Shared Hosting

Storage:
Local Storage (uploads folder)

---

# Branding

Primary Color:
Dark Green

Secondary Color:
Light Green

Accent:
Gold

Theme:
Modern Community Portal

Design Inspiration:
University of Rajasthan ERP

---

# Future Roadmap

Phase 2

* Donation Management
* Member Directory
* Blood Donor Directory
* Volunteer Registration

Phase 3

* Mobile App
* WhatsApp Notifications
* SMS Integration
* Digital Membership Cards

---

# Success Metrics

Year 1

* 100% online applications
* 0 manual data entry
* 80% reduction in paperwork
* Centralized student database
* Faster scholarship processing

End of PRD
