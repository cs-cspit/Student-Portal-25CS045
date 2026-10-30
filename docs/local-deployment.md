# Local XAMPP deployment

## Standard localhost setup

1. Place this project folder under `C:\xampp\htdocs`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Import `database/studenthub.sql` in phpMyAdmin for a new database, or apply the needed one-time migrations from `migrations/` to an existing Practical 14 database.
4. Make `data/` and `uploads/event-posters/` writable by Apache.
5. Open `http://localhost/Student-Portal-25CS045-main/`.

## Optional Apache virtual host

Add this to XAMPP Apache's `conf/extra/httpd-vhosts.conf` and adjust the folder path if needed:

```apache
<VirtualHost *:80>
    ServerName studenthub.local
    DocumentRoot "C:/xampp/htdocs/Student-Portal-25CS045-main"
    <Directory "C:/xampp/htdocs/Student-Portal-25CS045-main">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Enable the virtual-host include in `httpd.conf`, add `127.0.0.1 studenthub.local` to the Windows hosts file, restart Apache, then open `http://studenthub.local/`.
