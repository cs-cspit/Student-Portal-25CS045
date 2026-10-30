<?php
declare(strict_types=1);
require __DIR__ . '/includes/auth.php';
app_no_cache_headers();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#132238">
  <meta name="description" content="StudentHub is a focused workspace for student courses, schedules, assignments, attendance, and results.">
  <title>StudentHub · Student learning portal</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="landing-page">
  <header class="landing-header"><a class="auth-brand" href="index.php"><span class="brand-mark">S</span><span>StudentHub<small>STUDENT PORTAL</small></span></a><nav><a class="landing-nav-link" href="login.php">Log in</a><a class="button button-primary" href="register.php">Register</a></nav></header>
  <main>
    <section class="landing-hero"><div class="landing-copy"><p class="auth-eyebrow">YOUR ACADEMIC WORKSPACE</p><h1>Make space for<br><span>what you’re learning.</span></h1><p class="landing-description">A clear, focused place to keep up with courses, class schedules, assignments, and academic progress.</p><div class="landing-actions"><a class="auth-submit landing-primary" href="login.php">Log in to StudentHub <span aria-hidden="true">→</span></a><a class="landing-secondary" href="register.php">Create a student account</a></div><p class="landing-note">Student accounts require a valid student ID and registered email address.</p></div><div class="landing-preview" aria-label="Preview of the StudentHub academic workspace"><div class="preview-top"><span class="preview-brand"><i>S</i> StudentHub</span><span class="preview-tag">STUDENT WORKSPACE</span></div><div class="preview-content"><p>ACADEMIC OVERVIEW</p><h2>Keep your semester in view.</h2><div class="preview-cards"><div><small>MY COURSES</small><strong>Courses</strong><span>Access your current classes</span></div><div><small>THIS WEEK</small><strong>Schedule</strong><span>Plan the days ahead</span></div><div><small>STAY ON TRACK</small><strong>Assignments</strong><span>Review deadlines and work</span></div></div><div class="preview-lines"><span></span><span></span><span></span></div></div></div></section>
    <section class="landing-features"><article><span class="feature-number">01</span><h2>Course workspace</h2><p>Keep enrolled courses and class information together.</p></article><article><span class="feature-number">02</span><h2>Study planning</h2><p>Review a weekly timetable and upcoming assignments.</p></article><article><span class="feature-number">03</span><h2>Academic overview</h2><p>See attendance, results, and campus notices in one place.</p></article></section>
    <p class="landing-disclaimer">StudentHub is a coursework prototype. Academic information shown after login is sample content and is not provided by a college system.</p>
  </main>
  <footer class="landing-footer"><span>StudentHub</span><span>Student learning portal · Demonstration project</span><a href="register.php">Student registration</a></footer>
</body>
</html>
