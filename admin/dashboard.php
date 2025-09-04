<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }

// Database connection
$conn = new mysqli("localhost", "root", "", "portfolio");

// Get statistics
$projects_count = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$reviews_count = $conn->query("SELECT COUNT(*) as count FROM reviews")->fetch_assoc()['count'];
$about_exists = $conn->query("SELECT COUNT(*) as count FROM about WHERE id = 1")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-tachometer-alt" style="margin-right: 0.5rem;"></i>Dashboard</h1>
            <p>Welcome back! Manage your portfolio content from here.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $projects_count; ?></div>
                <div class="stat-label">Total Projects</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $reviews_count; ?></div>
                <div class="stat-label">Client Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $about_exists ? '✓' : '✗'; ?></div>
                <div class="stat-label">About Section</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Live</div>
                <div class="stat-label">Portfolio Status</div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="project.php" class="menu-item">
                <h3><i class="fas fa-folder-open"></i> Manage Projects</h3>
                <p>Add, edit, or delete your portfolio projects with images, tags, and links</p>
                <div class="btn btn-primary">Manage Projects</div>
            </a>
            
            <a href="about.php" class="menu-item">
                <h3><i class="fas fa-user-circle"></i> About Section</h3>
                <p>Update your about information, skills, education, and profile image</p>
                <div class="btn btn-primary">Edit About</div>
            </a>
            
            <a href="review.php" class="menu-item">
                <h3><i class="fas fa-star"></i> Client Reviews</h3>
                <p>Manage testimonials and client feedback with ratings and photos</p>
                <div class="btn btn-primary">Manage Reviews</div>
            </a>
            
            <a href="change_password.php" class="menu-item">
                <h3><i class="fas fa-lock"></i> Account Settings</h3>
                <p>Change your admin password and security settings</p>
                <div class="btn btn-secondary">Settings</div>
            </a>
        </div>

        <div class="admin-card">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <a href="../index.php" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View Portfolio
                </a>
                <a href="project.php" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Add New Project
                </a>
                <a href="review.php" class="btn btn-secondary">
                    <i class="fas fa-plus"></i> Add Review
                </a>
                <a href="logout.php" class="btn btn-danger" style="margin-left: auto;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</body>
</html>