# Practical 10 Test Report

## Verification run

The nine scenarios below were exercised against a disposable MariaDB database named `studenthub_p10_test`. A PHP request/session harness invoked the actual project endpoints. The existing `studenthub` database and project account CSV were not changed. The test run verified session state, route status, rendered error text, role checks, timeout redirects, and session ID changes. No browser screenshots were captured; the evidence cells identify the real browser screenshots to add before submission.

| # | Scenario | Expected result | Result | Screenshot |
|---|---|---|---|---|
| 1 | Sign in as Student | Student workspace opens | Passed | Capture the Student workspace |
| 2 | Sign in as Admin | Admin dashboard opens | Passed | Capture the Admin dashboard |
| 3 | Submit incorrect email/password | Generic sign-in error appears | Passed | Capture the login error |
| 4 | Student opens `admin_dashboard.php` | HTTP 403 Unauthorized Access page | Passed | Capture the access-denied page |
| 5 | Admin opens `student_dashboard.php` | HTTP 403 Unauthorized Access page | Passed | Capture the access-denied page |
| 6 | Log out | Server session is cleared and the login route opens | Passed | Capture the logged-out login screen |
| 7 | Open `dashboard.php` after logout | Request redirects to login | Passed | Capture the login page after the redirect |
| 8 | Open a protected page with an expired activity timestamp | Session is destroyed and request redirects through `timeout.php` to login | Passed | Capture the timeout notice on login |
| 9 | Compare session ID before and after login | The session ID changes after successful authentication | Passed | Capture browser network/session evidence if required |

The test run confirms the server-side behavior. Capture the listed screenshots from the running project for the visual submission; none are represented as already captured.

