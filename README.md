# StudentHub

StudentHub is a student-portal coursework prototype with a public home page, student registration, login, a protected academic workspace, and retained practical exercises.

## Run with XAMPP

1. Copy the project folder into `C:\xampp\htdocs`.
2. Start **Apache** in the XAMPP Control Panel.
3. Open `http://localhost/Student-Portal-25CS045-main/` (or replace the URL folder name with the name you chose).
4. Choose **Register** to create a student account, then log in with the student ID or email and password.

The `data/` directory must be writable by PHP. Registration requires MySQL and also writes to `data/accounts.csv` to preserve the existing CSV-backed login flow. Passwords are hashed before being saved to both stores. Direct web access to CSV records is blocked by `data/.htaccess`. The pre-existing `data/registrations.csv` and its records remain unchanged.

## Account flow

- Public home: `index.php`, with Log in and Register choices.
- Registration: `register.php` collects name, age, student ID, college or registered personal email, phone, gender, enrolled course, password, and password confirmation.
- Successful registration redirects to `login.php`.
- Login accepts a student ID or email, password, and a generated arithmetic verification question. `authenticate.php` checks the password hash with `password_verify()`, regenerates the session ID, and routes the account by role.
- `dashboard.php` checks the authenticated session and routes Admin and Student accounts to their respective dashboards. `student_dashboard.php` and `admin_dashboard.php` enforce their role on every request. Idle sessions expire after 15 minutes; logout destroys the session and cookie.
- The timetable supports previous and next weeks, individual weekdays, and a Today button.

## Events and database setup

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. For a new database, import `database/studenthub.sql`. For an existing database, apply the needed one-time migrations in order: `001` password hash, `002` Student/Admin role, `003` separate Admin accounts, `004` student status, `005` event posters, and `006` audit logs. Skip migrations already applied.
3. Import `practicals/practical10/user_registration.sql` to add demo Student and Admin login accounts.
4. The local XAMPP defaults in `db.php` are host `127.0.0.1`, database `studenthub`, and user `root` with an empty password. Set `STUDENTHUB_DB_HOST`, `STUDENTHUB_DB_NAME`, `STUDENTHUB_DB_USER`, and `STUDENTHUB_DB_PASSWORD` in PHP’s environment to use different credentials. Keep real database credentials out of the project files.
5. Open `http://localhost/Student-Portal-25CS045-main/test_connection.php` to confirm the connection and view upcoming event data retrieved with a prepared query.
6. Sign in and choose **Events** from the student dashboard. The page loads upcoming events from MySQL and lets the signed-in student reserve a place. If MySQL is not configured, it displays a read-only local event preview instead.

### Practical 10 demo accounts

- Admin: `admin.demo@example.edu` / `Admin@12345`
- Student: `student.demo@example.edu` / `Student@12345`
- Login includes the arithmetic security check; answer it in the form.
- Sessions expire after 15 minutes without activity. `timeout.php` redirects an expired session to the login screen.

Student accounts are checked and inserted in MySQL with prepared statements and are also kept in the CSV account file used by the existing login flow. Event registrations are kept in MySQL; the API synchronizes the signed-in student's basic profile into the `students` table when they register for an event.

## Practical 9 registration checks

The portal registration page enforces required fields, email format, password confirmation, and duplicate student ID/email checks in both storage locations. It hashes the password with `password_hash()` before insertion, then writes the matching CSV account row. See `practicals/practical9/README.md` for the check list and setup steps.

## Practical 7 code

The manual-aligned exercise is in `practicals/practical7/`. Its `index.html` form collects name, email, mobile number, course (B.Tech, BCA, or B.Sc), and message. It posts to `php-registration.php`, which validates and sanitizes the values, appends valid rows to `data/registrations.csv`, and shows success or error feedback. This coursework exercise is separate from the portal account registration at `register.php`.

Open `http://localhost/Student-Portal-25CS045-main/practicals/practical7/` under XAMPP to run the exercise.

## Project layout

```text
Student-Portal-25CS045-main/
├── index.php                  # Public landing page
├── login.php                  # Login form and arithmetic verification
├── authenticate.php           # MySQLi login, password_verify and session renewal
├── register.php               # Validated registration with hashed MySQL and CSV storage
├── logout.php                 # Session and cookie invalidation
├── dashboard.php              # Session check and role-based dashboard router
├── student_dashboard.php      # Session-protected student workspace
├── admin_dashboard.php         # Admin-only dashboard
├── timeout.php                # Expired-session redirect
├── db.php                     # PDO event connection and MySQLi authentication connection
├── database/
│   └── studenthub.sql         # Full normalized database dump for a new install
├── migrations/                # One-time updates for existing installations
├── test_connection.php        # Prepared-query connection check
├── events_api.php             # Upcoming events and event registration endpoint
├── admin_events.php           # Integrated Fetch-based Admin event manager
├── student_management.php     # Admin student CRUD, filters and CSV export
├── admin_register.php         # Restricted administrator account setup
├── includes/auth.php          # Session, CSRF, escaping and access helpers
├── includes/audit.php         # Admin audit logging
├── css/
│   ├── auth.css               # Public, login, and registration screens
│   ├── studyhub.css           # Protected workspace and events calendar design
├── js/studyhub.js             # Portal screens, filters, timetable and interactions
├── data/                      # Protected local CSV data and event fallback JSON
├── uploads/event-posters/     # Validated event poster uploads
├── docs/                      # Deployment and Practical 15 test/security report
└── practicals/
    ├── README.md               # Practical source map
    ├── practical7/             # Manual-aligned POST and CSV practical
    ├── practical9/             # Registration validation notes
    ├── practical10/            # Authentication SQL and test notes
    ├── practical11/            # Student CRUD evidence checklist
    ├── practical12/            # Event/poster evidence checklist
    ├── practical13/            # Fetch API source and evidence notes
    ├── practical14/            # RBAC/audit source and evidence notes
    └── archive/                # Inactive prototype retained for reference
```

The root is kept as the web application root so existing XAMPP routes remain simple. Private/generated CSV data and uploaded posters are excluded from Git by `.gitignore`; do not commit real student records or credentials. See `docs/test-security-report.md` and `docs/local-deployment.md` for Practical 15 evidence and deployment steps.

`index.html` forwards to the PHP home page for compatibility. Old registration pages are archived and are not part of the active site.

## Prototype limits

The account workflow runs locally in PHP. It does not verify that a student ID or email belongs to a college, send email verification, or connect to a college identity provider. The arithmetic CAPTCHA is suitable only as a coursework demonstration. Course schedules, attendance, grades, and notices remain labelled sample data; they are not official student records. Login recovery, official submissions, and institutional services are not connected.

## Technologies

HTML5, CSS3, JavaScript, PHP, MySQLi, PDO/MySQL, and CSV/JSON data.



