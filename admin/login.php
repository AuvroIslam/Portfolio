<?php
session_start();

// Check if user has remember me cookie
if (!isset($_SESSION['admin']) && isset($_COOKIE['remember_admin'])) {
    $conn = new mysqli("localhost", "root", "", "portfolio");
    if (!$conn->connect_error) {
        $remember_token = $_COOKIE['remember_admin'];
        $stmt = $conn->prepare("SELECT username, remember_token_created FROM admins WHERE remember_token=? LIMIT 1");
        $stmt->bind_param("s", $remember_token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            // Check if token is not older than 30 days
            $token_age = time() - strtotime($admin['remember_token_created']);
            if ($token_age < (30 * 24 * 60 * 60)) {
                $_SESSION['admin'] = $admin['username'];
                header("Location: dashboard.php");
                exit;
            } else {
                // Token expired, clear it
                $stmt = $conn->prepare("UPDATE admins SET remember_token=NULL, remember_token_created=NULL WHERE remember_token=?");
                $stmt->bind_param("s", $remember_token);
                $stmt->execute();
                setcookie('remember_admin', '', time() - 3600, '/', '', false, true);
            }
        }
        $conn->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);
    $conn = new mysqli("localhost", "root", "", "portfolio");
    
    if ($conn->connect_error) {
        $error = "Database connection failed.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = $admin['username'];
                
                // Handle Remember Me functionality
                if ($remember_me) {
                    // Generate secure random token
                    $remember_token = bin2hex(random_bytes(32));
                    
                    // Store token in database with timestamp
                    $stmt = $conn->prepare("UPDATE admins SET remember_token=?, remember_token_created=NOW() WHERE username=?");
                    $stmt->bind_param("ss", $remember_token, $username);
                    $stmt->execute();
                    
                    // Set cookie for 30 days
                    setcookie('remember_admin', $remember_token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Admin not found.";
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        
        .login-container {
            background: var(--bg-primary);
            padding: 3rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 450px;
            border: 1px solid var(--border-light);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: var(--text-secondary);
            font-size: 1rem;
            margin: 0;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1rem 0;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto !important;
            margin: 0;
            transform: scale(1.2);
            accent-color: var(--primary-color);
        }
        
        .checkbox-group label {
            font-weight: 500 !important;
            color: var(--text-secondary) !important;
            cursor: pointer;
            text-transform: none !important;
            letter-spacing: normal !important;
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-light);
        }
        
        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .back-link a:hover {
            color: var(--primary-dark);
            transform: translateX(-2px);
        }
        
        .btn-login {
            width: 100%;
            margin-top: 1rem;
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Portfolio Admin</h1>
            <p>Sign in to manage your portfolio</p>
        </div>
        
        <?php if(isset($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="post" class="admin-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Remember me for 30 days</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-login">
                Sign In
            </button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← Back to Portfolio</a>
        </div>
    </div>
</body>
</html>