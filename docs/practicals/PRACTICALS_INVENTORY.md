# StudentHub Practicals Inventory (Practicals 1 to 14)

![Academic Workflow](../../structure/academic-workflow.png)

This inventory lists all features, files, tables, forms, validations, and storage mechanisms implemented across Practicals 1 to 14. These are non-negotiable requirements that must be strictly preserved and fully functional.

---

## Practical 1 to 5: Foundational UI, Pages & Registration Form
- **Features**:
  - Semantic HTML5 structure for student portal pages (courses, timetable/schedule, notices, results, profile, settings, faq, events, about, contact).
  - Responsive layout, CSS variables, typography, navigation headers and responsive design.
  - Interactive registration form with real-time field validation, password strength indicator, email/mobile pattern checks (`practical5-register.html`).
- **Files**:
  - `practicals/archive/previous-version/html/*.html`
  - `practicals/archive/previous-version/practical5-register.html`
  - `practicals/archive/previous-version/css/style.css`

---

## Practical 6: Fetch API & Asynchronous Client Data
- **Features**:
  - Client-side Fetch API integration to asynchronously load JSON data (`events.json`, `students.json`).
  - Search, filter by category, sort by date/popularity, pagination and slider/carousel UI.
- **Files**:
  - `practicals/archive/previous-version/js/practical6.js`
  - `data/events.json`, `data/students.json`, `data/faqs.json`

---

## Practical 7: PHP Form Processing & CSV Data Persistence
- **Features**:
  - Server-side form processing via HTTP POST.
  - Input sanitization (`trim`, `htmlspecialchars`), validation (regex for 10-digit mobile, valid email, course whitelist `B.Tech`, `BCA`, `B.Sc`, `MCA`).
  - CSV append persistence into `data/registrations.csv`.
  - User feedback (success and error states).
- **Files**:
  - `practicals/practical7/index.html`
  - `practicals/practical7/php-registration.php`
  - `practicals/practical7/style.css`
  - `data/registrations.csv`

---

## Practical 8 & 9: Server-side Registration Validation & Database Integration
- **Features**:
  - Complete server-side registration validation in `register.php`.
  - Required fields, email format, student ID duplicate checks, password confirmation.
  - Password hashing via `password_hash()` (BCRYPT / Argon2).
  - Dual persistence: Prepared SQL `INSERT` into MySQL `students` table AND synchronized record storage into `data/accounts.csv`.
  - Transaction safety and rollback handling.
- **Files**:
  - `register.php`
  - `migrations/001_add_student_password_hash.sql`
  - `data/accounts.csv`
  - `database/studenthub.sql` (`students` table)

---

## Practical 10: PHP Authentication & Session Management
- **Features**:
  - Secure login system with email or Student ID identifier.
  - Arithmetic security check (CAPTCHA) generated per session with "Get a different question" capability.
  - Password verification using `password_verify()`.
  - Session regeneration on login (`session_regenerate_id(true)`) to prevent session fixation.
  - Role-based routing upon successful authentication (Admin -> Admin Dashboard, Student -> Student Dashboard).
  - 15-minute idle session timeout enforcement (`timeout.php`).
  - Secure logout clearing session and cookie (`logout.php`).
  - Restricted administrator registration form (`admin_register.php`) for creating admin accounts into `admins` table.
- **Files**:
  - `login.php`
  - `authenticate.php`
  - `dashboard.php`
  - `logout.php`
  - `timeout.php`
  - `unauthorized.php`
  - `admin_register.php`
  - `includes/auth.php`
  - `practicals/practical10/user_registration.sql`
  - `migrations/002_add_student_role.sql`
  - `migrations/003_create_admin_accounts.sql`

---

## Practical 11: Student Management CRUD
- **Features**:
  - Admin student record management at `student_management.php`.
  - Prepared MySQLi statements to List, Add (with initial hashed password), Edit student profiles, Search by name/ID/email, and Filter by course and status.
  - Soft-delete functionality setting status to `inactive`.
  - CSV Export feature generating dynamic student exports and saving to `data/student_records.csv`.
- **Files**:
  - `student_management.php`
  - `migrations/004_add_student_status.sql`
  - `data/student_records.csv`

---

## Practical 12: Event Management CRUD & Poster Upload
- **Features**:
  - Event management module at `admin_events.php`.
  - Full CRUD: Create event, List events, Edit event details, Soft-delete / Cancel event preserving registration history.
  - Secure file upload for event posters: MIME validation, real image verification via `getimagesize()`, extension whitelist (`jpg`, `png`, `webp`), 2 MB size ceiling.
  - Secure file storage under `uploads/event-posters/` with `.htaccess` execution prevention.
- **Files**:
  - `admin_events.php`
  - `migrations/005_add_event_posters.sql`
  - `uploads/event-posters/`

---

## Practical 13: Fetch API Event CRUD Integration
- **Features**:
  - Asynchronous CRUD operations for events via Fetch API without page reload.
  - Dedicated API endpoint `practicals/practical13/api.php` supporting GET, POST, PUT, DELETE actions.
  - Admin session validation and CSRF token verification on all mutation requests.
  - Robust client-side validation and responsive error/success feedback.
- **Files**:
  - `practicals/practical13/api.php`
  - `practicals/practical13/script.js`
  - `practicals/practical13/index.html`
  - `practicals/practical13/schema.sql`

---

## Practical 14: Admin Dashboard, RBAC & Audit Logging
- **Features**:
  - Comprehensive Admin Dashboard at `admin_dashboard.php`.
  - Dynamic KPI stats: Total users, Student count, Active events, Total registrations.
  - Live tables: Latest event registrations, Recent audit log entries.
  - Server-side RBAC enforcement: 403 Forbidden on role mismatch.
  - Audit logging system in `includes/audit.php` tracking admin actions: `admin.login`, `admin.logout`, `admin.create`, `student.create`, `student.update`, `student.delete`, `event.create`, `event.update`, `event.delete`.
- **Files**:
  - `admin_dashboard.php`
  - `includes/audit.php`
  - `migrations/006_create_audit_logs.sql`
  - `schema.sql` / `database/studenthub.sql` (`audit_logs` table)

---

## Verification Criteria
All features, database schema, data columns, endpoints, APIs, forms, sessions, CSV exports, uploads, and security guarantees remain 100% active, integrated, and fully functional within the unified StudentHub platform.

