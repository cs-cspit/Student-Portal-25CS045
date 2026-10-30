# Practical 11 — Student Management CRUD

The student management module is located at `admin/student_management.php` (Admin-only). It uses prepared MySQLi statements to list, add, edit, search, filter by course, export the current result set as CSV, and soft-delete records by setting `status` to `inactive`.

## Existing database setup

Select the `studenthub` database in phpMyAdmin, open **SQL**, and run `migrations/004_add_student_status.sql` once. New installations get the `status` field from `database/studenthub.sql`.

Sign in with an Admin account and open `student_management.php` from the Admin dashboard. Add operations require a starting password, which is stored with `password_hash()`. Clicking **Export CSV** downloads the filtered results and replaces the protected `data/student_records.csv` file with the same export. The supplied CSV starts with headers only; export from the live database to populate it.

## Screenshot evidence to capture

- Student rows displayed in the management table.
- Add a student and show the success notice.
- Edit a student and show the updated values/success notice.
- Search by name, ID or email; filter by course.
- Press Delete, capture its confirmation prompt, then show the record under the inactive filter.
- Show a success/error notice and open the exported CSV in Excel.

Screenshots must be captured from the running local XAMPP project; this checklist does not claim that screenshots have been captured.
