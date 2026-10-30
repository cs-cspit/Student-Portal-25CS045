<?php
declare(strict_types=1);
if (!isset($pageTitle)) {
    $pageTitle = 'StudentHub';
}
if (!isset($portalType)) {
    $portalType = 'student';
}
?>
<!doctype html>
<html lang="en" data-portal="<?= htmlspecialchars($portalType, ENT_QUOTES, 'UTF-8') ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#15163A">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> · StudentHub</title>
  <link rel="stylesheet" href="<?= app_base_url() ?>css/tokens.css?v=2.3">
  <link rel="stylesheet" href="<?= app_base_url() ?>css/app.css?v=2.3">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('studenthub_theme');
      const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = savedTheme ? savedTheme : (prefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      if (theme === 'dark') document.documentElement.classList.add('dark');

      const isSidebarCollapsed = localStorage.getItem('studenthub_sidebar_collapsed') === 'true';
      if (isSidebarCollapsed && window.innerWidth >= 769) {
        document.documentElement.classList.add('sidebar-collapsed');
      }
    })();
  </script>
</head>
<body class="<?= $portalType === 'admin' ? 'admin-page' : 'student-page' ?>">


