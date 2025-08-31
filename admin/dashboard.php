<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
?>
<h2>Admin Dashboard</h2>
<ul>
    <li><a href="/Portfolio/admin/project.php">Manage Projects</a></li>
    <li><a href="/Portfolio/admin/about.php">Manage About</a></li>
    <li><a href="/Portfolio/admin/review.php">Manage Reviews</a></li>
    <li><a href="/Portfolio/admin/change_password.php">Change Password</a></li>
    <li><a href="/Portfolio/admin/logout.php">Logout</a></li>
</ul>