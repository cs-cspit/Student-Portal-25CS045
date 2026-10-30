# Practical 10 PHP Authentication and Session Management

Practical 10 is integrated into StudentHub's existing registration and portal flow. `login.php` renders the sign-in form; `authenticate.php` authenticates with MySQLi, checks hashes using `password_verify()`, regenerates the session ID, and routes the account by role. Student and Admin dashboard routes validate the active session and role. The session expires after 15 minutes of inactivity.

## Setup

1. Import `database/studenthub.sql` into MySQL.
2. If upgrading an existing Practical 9 database, run `migrations/002_add_student_role.sql` once. If the database predates Practical 9, run `migrations/001_add_student_password_hash.sql` first. Run `migrations/003_create_admin_accounts.sql` to enable the separate Admin account form.
3. Import `user_registration.sql` to add the demo accounts.
4. Start Apache and MySQL in XAMPP and open `http://localhost/Student-Portal-25CS045-main/login.php`.

Demo accounts:

- Admin: `admin.demo@example.edu` / `Admin@12345`
- Student: `student.demo@example.edu` / `Student@12345`

The login form also displays a simple arithmetic check; enter its answer.

## Separate administrator registration

Student accounts continue to use `register.php` and require student ID and course details. Administrator accounts use `admin_register.php`, which asks only for name, email and password. This page is restricted to an already authenticated Admin; sign in with an existing Admin account, then choose **Add administrator** in the Admin dashboard. New Admin accounts are saved with hashed passwords in the separate `admins` table and sign in using their email address.

## Practical 10 scenarios

Use `test-report.md` for the test results and screenshot checklist. Capture evidence from the running site for both successful roles, invalid credentials, role denials, logout, blocked dashboard access after logout, idle timeout, and the changed session ID.

The separate `style.css` stylesheet in this folder contains the Practical 10 dashboard and access-message styling. StudentHub's existing `css/auth.css` and `css/studyhub.css` continue to provide the shared portal design.

