# StudentHub Practical 15 — Test and Security Report

**Environment:** XAMPP / Apache / PHP / MySQL  
**Execution date:** __________________  
**Tester:** __________________

> Record the actual result after running each check locally. Do not mark a case passed without evidence.

| Area | Check | Result | Evidence |
|---|---|---|---|
| Navigation | Public home → login/register; Student and Admin menus | Pending | Screenshot |
| Responsive UI | Home, login, Student dashboard, Admin dashboard at desktop/mobile widths | Pending | Screenshots |
| Authentication | Student/Admin success, invalid credentials, logout, timeout | Pending | Screenshots |
| RBAC | Student requests Admin dashboard and Admin requests Student dashboard | Pending | 403 screenshots |
| Database | Registration, event CRUD, student CRUD, latest registrations | Pending | Screenshots/rows |
| Fetch API | Event list/add/update/delete, status and JSON responses | Pending | Network screenshots |
| Upload security | Valid poster, invalid type, over 2 MB | Pending | Screenshots |
| Audit trail | Admin login/logout and record changes are inserted in `audit_logs` | Pending | phpMyAdmin screenshot |
| Security | Prepared statements, password hashes, CSRF, protected CSV/upload folders | Pending | Code/response evidence |
| Accessibility | Labels, keyboard navigation, visible focus, contrast, responsive layout | Pending | Checklist |

## Security/accessibility checklist

- [ ] All privileged pages enforce the Admin role on the server.
- [ ] Mutating forms/API requests validate the CSRF token.
- [ ] SQL inputs use prepared statements.
- [ ] Passwords are stored as hashes and checked with `password_verify()`.
- [ ] Poster uploads validate MIME, actual image content, and maximum size.
- [ ] CSV/account data is not directly web-accessible.
- [ ] Form fields have labels; controls can be reached with a keyboard; focus is visible.
- [ ] Validate on both desktop and a narrow/mobile viewport.

Screenshots and results are intentionally left pending until captured from the local deployment.
