<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $old = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate new password
    if($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif(strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("SELECT password FROM admins WHERE username=?");
        $stmt->bind_param("s", $_SESSION['admin']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if(password_verify($old, $result['password'])){
            $stmt = $conn->prepare("UPDATE admins SET password=? WHERE username=?");
            $stmt->bind_param("ss", $new_hashed, $_SESSION['admin']);
            $stmt->execute();
            $success = "Password changed successfully!";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Portfolio Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-key" style="margin-right: 0.5rem;"></i>Change Password</h1>
            <p>Update your admin account password for security</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="project.php"><i class="fas fa-folder-open"></i> Projects</a>
            <a href="about.php"><i class="fas fa-user"></i> About</a>
            <a href="review.php"><i class="fas fa-star"></i> Reviews</a>
        </div>
        
        <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="admin-card" style="max-width: 500px; margin: 0 auto;">
            <h2><i class="fas fa-shield-alt"></i> Security Settings</h2>
            <form method="post" class="admin-form">
                <div class="form-group">
                    <label for="old_password"><i class="fas fa-lock"></i> Current Password</label>
                    <div style="position: relative;">
                        <input type="password" name="old_password" id="old_password" placeholder="Enter current password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('old_password')">
                            <i class="fas fa-eye" id="old_password_icon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-key"></i> New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password (min 6 characters)" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye" id="new_password_icon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-check-double"></i> Confirm New Password</label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye" id="confirm_password_icon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="security-tips">
                    <h4><i class="fas fa-lightbulb"></i> Password Security Tips</h4>
                    <ul>
                        <li>Use at least 6 characters (longer is better)</li>
                        <li>Include a mix of letters, numbers, and symbols</li>
                        <li>Avoid using personal information</li>
                        <li>Don't reuse passwords from other accounts</li>
                    </ul>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </form>
        </div>
    </div>
    
    <style>
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .security-tips {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            margin: 1.5rem 0;
            border-left: 4px solid var(--primary-color);
        }
        
        .security-tips h4 {
            margin: 0 0 1rem 0;
            color: var(--text-primary);
            font-size: 1rem;
        }
        
        .security-tips ul {
            margin: 0;
            padding-left: 1.5rem;
            color: var(--text-secondary);
        }
        
        .security-tips li {
            margin-bottom: 0.5rem;
        }
    </style>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>