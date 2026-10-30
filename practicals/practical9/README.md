# Practical 9 Registration Validation and Database Storage

The portal registration page at `../../register.php` validates every required field on the server, checks the email format, confirms matching passwords, and rejects an existing student ID or email in either the CSV account file or MySQL. New passwords are hashed with PHP `password_hash()` before they are written to MySQL and the existing CSV account file. The page uses a prepared `INSERT` and a database transaction.

## Database setup

- For a new installation, import `database/studenthub.sql`.
- For a database created before Practical 9, run `../../migrations/001_add_student_password_hash.sql` once in phpMyAdmin.
- Start Apache and MySQL, then open `http://localhost/Student-Portal-25CS045-main/register.php`.

## Practical 9 checks

1. Submit valid, unique student details. The page redirects to login after saving the student to MySQL and `data/accounts.csv`.
2. Submit the form with a required value missing. The server displays an empty-field validation message.
3. Submit an invalid email address. The server rejects it.
4. Enter different password and confirmation values. The server reports the mismatch.
5. Register the same student ID or email again. The page reports the duplicate and does not add another account.
6. In phpMyAdmin, inspect `studenthub.students.password_hash`. The saved value is a one-way hash and does not equal the submitted password.

The project retains its CSV account storage and the existing practical work. The MySQL password column is nullable only to support demo/event-only student profiles; registration always writes a password hash.

