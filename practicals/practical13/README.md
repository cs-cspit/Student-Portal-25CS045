# Practical 13 — Fetch API Event CRUD

Practical 13 is integrated into the Admin **Events** tab at `admin_events.php`. Its `script.js` uses Fetch to create, list, update, and cancel records in StudentHub's existing `events` table without a full page reload. The API is `practicals/practical13/api.php`; it requires an Admin session and CSRF token. Poster uploads use the same server-side type and size validation as the event manager.

The `index.html` entry redirects to the integrated Events tab. A new install should use `database/studenthub.sql`; this practical's `schema.sql` documents the shared events table. For an existing database without `poster_path`, run `migrations/005_add_event_posters.sql` once.

## Screenshot evidence

Capture the Admin login/dashboard, event list, successful add/edit/delete, valid and invalid poster uploads, size limit error, success/error messages, and unauthorized access. Use DevTools → Network → Fetch/XHR to capture request status and JSON response. Screenshots must come from the live XAMPP site; none are included here.
