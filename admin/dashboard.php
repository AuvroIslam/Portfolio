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
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #333; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #007cba; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .menu-item { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .menu-item a { text-decoration: none; color: #333; font-weight: bold; font-size: 1.1em; }
        .menu-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); transition: all 0.3s; }
        .quick-links { background: white; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #005a87; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Portfolio Admin Dashboard</h1>
            <p>Welcome back! Manage your portfolio content from here.</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $projects_count; ?></div>
                <div>Total Projects</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $reviews_count; ?></div>
                <div>Client Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $about_exists ? 'Setup' : 'Missing'; ?></div>
                <div>About Section</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Active</div>
                <div>Portfolio Status</div>
            </div>
        </div>

        <div class="menu-grid">
            <div class="menu-item">
                <h3>📁 Manage Projects</h3>
                <p>Add, edit, or delete your portfolio projects</p>
                <a href="project.php">Manage Projects</a>
            </div>
            <div class="menu-item">
                <h3>👤 About Section</h3>
                <p>Update your about information, skills, and education</p>
                <a href="about.php">Edit About</a>
            </div>
            <div class="menu-item">
                <h3>⭐ Client Reviews</h3>
                <p>Manage testimonials and client feedback</p>
                <a href="review.php">Manage Reviews</a>
            </div>
            <div class="menu-item">
                <h3>🔒 Account Settings</h3>
                <p>Change your admin password</p>
                <a href="change_password.php">Change Password</a>
            </div>
        </div>

        <div class="quick-links">
            <h3>Quick Actions</h3>
            <a href="../index.php" class="btn" target="_blank">View Portfolio</a>
            <a href="project.php" class="btn">Add New Project</a>
            <a href="review.php" class="btn">Add Review</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>