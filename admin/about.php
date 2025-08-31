<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $education = $_POST['education'];
    $about_image = '';
    
    // Handle file upload for about image
    if(isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
        $upload_dir = '../assets/uploads/';
        $file_extension = strtolower(pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'about_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['about_image']['tmp_name'], $upload_path)) {
                $about_image = 'assets/uploads/' . $new_filename;
                
                // Delete old image if it exists in uploads folder
                $old_image_result = $conn->query("SELECT about_image FROM about WHERE id=1");
                if($old_image_result && $old_image_row = $old_image_result->fetch_assoc()) {
                    if(!empty($old_image_row['about_image']) && strpos($old_image_row['about_image'], 'assets/uploads/') === 0) {
                        $old_file_path = '../' . $old_image_row['about_image'];
                        if(file_exists($old_file_path)) {
                            unlink($old_file_path);
                        }
                    }
                }
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
        }
    } else {
        // Keep existing image if no new image uploaded
        $existing_result = $conn->query("SELECT about_image FROM about WHERE id=1");
        if($existing_result && $existing_row = $existing_result->fetch_assoc()) {
            $about_image = $existing_row['about_image'];
        }
    }
    
    if(empty($error)) {
        $conn->query("UPDATE about SET description='$description', skills='$skills', education='$education', about_image='$about_image' WHERE id=1");
        $success = "About section updated successfully!";
    }
}
$row = $conn->query("SELECT * FROM about WHERE id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About - Portfolio Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { background: #333; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #007cba; text-decoration: none; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-row input, .form-row textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-row textarea { height: 100px; resize: vertical; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .file-upload { position: relative; display: inline-block; width: 100%; }
        .file-upload input[type=file] { width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9; }
        .file-upload input[type=file]:hover { border-color: #007cba; }
        .upload-info { font-size: 0.9em; color: #666; margin-top: 5px; }
        .current-image { max-width: 200px; height: 120px; object-fit: cover; border-radius: 4px; margin-top: 10px; border: 1px solid #ddd; }
        .image-preview { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage About Section</h1>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">← Dashboard</a>
            <a href="project.php">Projects</a>
            <a href="review.php">Reviews</a>
            <a href="../index.php" target="_blank">View Portfolio</a>
        </div>
        
        <?php if(isset($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Update About Information</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="description"><strong>About Description:</strong></label>
                    <textarea name="description" id="description" rows="6" placeholder="Write your about description here..." required><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-row">
                    <label for="about_image"><strong>About Image:</strong></label>
                    <?php if(!empty($row['about_image'])): ?>
                    <div class="image-preview">
                        <p>Current image:</p>
                        <img src="../<?php echo htmlspecialchars($row['about_image']); ?>" alt="Current about image" class="current-image">
                    </div>
                    <?php endif; ?>
                    <div class="file-upload">
                        <input type="file" name="about_image" id="about_image" accept="image/*">
                        <div class="upload-info">Upload new image (optional). Supported formats: JPG, PNG, GIF, WebP</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <label for="skills"><strong>Skills (comma separated):</strong></label>
                    <input type="text" name="skills" id="skills" placeholder="HTML,CSS,JavaScript,React,Next.js,Python,C++,Unity,React Native" value="<?php echo htmlspecialchars($row['skills'] ?? ''); ?>" required>
                </div>
                
                <div class="form-row">
                    <label for="education"><strong>Education:</strong></label>
                    <textarea name="education" id="education" rows="3" placeholder="Education details..." required><?php echo htmlspecialchars($row['education'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn">Update About Section</button>
            </form>
        </div>
    </div>
</body>
</html>