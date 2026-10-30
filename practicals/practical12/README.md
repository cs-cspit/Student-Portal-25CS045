# Practical 12 — Event Management CRUD with Poster Upload and File Validation

Admin event management is available at `admin/admin_events.php`. It supports event creation, listing, editing, cancellation/deletion, and optional poster uploads. Accepted poster formats are JPG, PNG, and WEBP, up to 2 MB. Event rows are marked cancelled on deletion to preserve registrations.

For an existing database, select `studenthub` in phpMyAdmin, open **SQL**, and run `migrations/005_add_event_posters.sql` once. New databases get `poster_path` from `database/studenthub.sql`. The web server must be allowed to write to `uploads/event-posters`.

## Evidence checklist

Capture these from the running XAMPP site: Admin login/dashboard; event creation, list with poster, update, and deletion; valid poster upload; invalid file type and over-2-MB validation; success/error messages; and a non-Admin denied from `admin_events.php`. Screenshots must come from the live site; none are claimed in this project note.
