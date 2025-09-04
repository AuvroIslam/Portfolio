<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
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
    <title>Manage About Section - Portfolio Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-user" style="margin-right: 0.5rem;"></i>Manage About Section</h1>
            <p>Update your personal information and bio</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="project.php"><i class="fas fa-folder-open"></i> Projects</a>
            <a href="review.php"><i class="fas fa-star"></i> Reviews</a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Portfolio</a>
        </div>
        
        <?php if(isset($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <div class="admin-card">
            <h2><i class="fas fa-edit"></i> Update About Information</h2>
            <form method="post" enctype="multipart/form-data" class="admin-form">
                <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start;">
                    <div>
                        <div class="form-group">
                            <label for="description"><i class="fas fa-pen"></i> About Description</label>
                            <textarea name="description" id="description" rows="8" placeholder="Tell your story..." required><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="skills"><i class="fas fa-tools"></i> Skills</label>
                            <input type="text" name="skills" id="skills" placeholder="HTML,CSS,JavaScript,React,Next.js,Python,C++,Unity,React Native" value="<?php echo htmlspecialchars($row['skills'] ?? ''); ?>" required>
                            <div class="upload-info">Separate skills with commas for better display</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="education"><i class="fas fa-graduation-cap"></i> Education</label>
                            <textarea name="education" id="education" rows="4" placeholder="Education details..." required><?php echo htmlspecialchars($row['education'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label for="about_image"><i class="fas fa-camera"></i> About Image</label>
                            <?php if(!empty($row['about_image'])): ?>
                            <div class="current-image">
                                <img src="../<?php echo htmlspecialchars($row['about_image']); ?>" alt="Current About Image" class="admin-image profile-preview">
                                <div class="image-overlay">
                                    <span>Current Image</span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <input type="file" name="about_image" id="about_image" accept="image/*" class="file-upload-input">
                            <div class="upload-info">
                                <i class="fas fa-info-circle"></i> Supported formats: JPG, PNG, GIF, WebP (Max size: 5MB)
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update About Section
                </button>
            </form>
        </div>
        
        <?php if(isset($row) && !empty($row)): ?>
        <div class="admin-card">
            <h2><i class="fas fa-eye"></i> Current About Information</h2>
            <div class="preview-content">
                <div style="display: grid; grid-template-columns: 1fr 200px; gap: 2rem; align-items: start;">
                    <div>
                        <div class="preview-item">
                            <h4><i class="fas fa-pen"></i> Description</h4>
                            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        </div>
                        
                        <div class="preview-item">
                            <h4><i class="fas fa-tools"></i> Skills</h4>
                            <div class="tags">
                                <?php 
                                $skills = explode(',', $row['skills']);
                                foreach($skills as $skill): 
                                ?>
                                <span class="tag"><?php echo trim(htmlspecialchars($skill)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="preview-item">
                            <h4><i class="fas fa-graduation-cap"></i> Education</h4>
                            <p><?php echo nl2br(htmlspecialchars($row['education'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if(!empty($row['about_image'])): ?>
                    <div class="preview-image">
                        <img src="../<?php echo htmlspecialchars($row['about_image']); ?>" alt="About Image" class="admin-image">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>