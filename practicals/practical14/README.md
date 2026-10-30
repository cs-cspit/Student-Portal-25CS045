# Practical 14 — Admin Dashboard, RBAC, Dynamic Menu and Audit Log

The executive administration center is located at `admin/admin_dashboard.php`. It shows live user/student/event/registration counts, latest event registrations, student inquiries from the Help Desk, and recent Admin audit entries. Existing role guards restrict it to Admins; student and Admin pages show their respective navigation rails. `includes/audit.php` records administrator login/logout and student, event, inquiry, and admin account changes.

For an existing database, run `migrations/006_create_audit_logs.sql` once in phpMyAdmin. The dashboard reads the existing students, admins, events, and registrations tables.

## Evidence to capture

- Admin login and live dashboard statistics/latest registrations.
- Student and Admin navigation menus showing role-specific options.
- Student opening `admin_dashboard.php` and receiving the 403 unauthorized page.
- `audit_logs` table showing Admin actions after login and record changes.

Screenshots must be captured from the running XAMPP site; none are included here.

