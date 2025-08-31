<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
?>
<h2>Admin Dashboard</h2>
<ul>
    <li><a href="project.php">Manage Projects</a></li>
    <li><a href="about.php">Manage About</a></li>
    <li><a href="review.php">Manage Reviews</a></li>
    <li><a href="change_password.php">Change Password</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>