<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $tags = $_POST['tags'];
    $live_url = $_POST['live_url'];
    $code_url = $_POST['code_url'];
    
    // Handle file upload
    $image_path = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/uploads/';
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'project_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'assets/uploads/' . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
        }
    } else {
        $error = "Please select an image file.";
    }
    
    if(empty($error)) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, image, category, tags, live_url, code_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $description, $image_path, $category, $tags, $live_url, $code_url);
        $stmt->execute();
        $success = "Project added successfully!";
    }
}
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    
    // Get the image path before deleting the record
    $stmt = $conn->prepare("SELECT image FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    
    // Delete the image file if it exists in uploads folder
    if($project && !empty($project['image']) && strpos($project['image'], 'assets/uploads/') === 0) {
        $file_path = '../' . $project['image'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete the database record
    $stmt = $conn->prepare("DELETE FROM projects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = "Project deleted successfully!";
}
$result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects - Portfolio Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container wide">
        <div class="admin-header">
            <h1><i class="fas fa-folder-open" style="margin-right: 0.5rem;"></i>Manage Projects</h1>
            <p>Add, edit, and organize your portfolio projects</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="about.php"><i class="fas fa-user"></i> About</a>
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
            <h2><i class="fas fa-plus-circle"></i> Add New Project</h2>
            <form method="post" enctype="multipart/form-data" class="admin-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Project Title</label>
                        <input type="text" name="title" id="title" placeholder="Enter project title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category"><i class="fas fa-tag"></i> Category</label>
                        <select name="category" id="category" required>
                            <option value="">Select Category</option>
                            <option value="web">🌐 Web Apps</option>
                            <option value="mobile">📱 Mobile</option>
                            <option value="data">📊 Data Science</option>
                            <option value="game">🎮 Games</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" id="description" placeholder="Describe your project..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image"><i class="fas fa-image"></i> Project Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="file-upload-input" required>
                    <div class="upload-info">
                        <i class="fas fa-info-circle"></i> Supported formats: JPG, PNG, GIF, WebP (Max size: 5MB)
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tags"><i class="fas fa-tags"></i> Technologies Used</label>
                    <input type="text" name="tags" id="tags" placeholder="React, Node.js, MongoDB" required>
                    <div class="upload-info">Separate technologies with commas</div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="live_url"><i class="fas fa-external-link-alt"></i> Live Demo URL</label>
                        <input type="url" name="live_url" id="live_url" placeholder="https://example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="code_url"><i class="fab fa-github"></i> Code Repository URL</label>
                        <input type="url" name="code_url" id="code_url" placeholder="https://github.com/username/repo">
                    </div>
                </div>
                
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </form>
        </div>
        
        <div class="admin-card">
            <h2><i class="fas fa-list"></i> Existing Projects (<?php echo $result->num_rows; ?>)</h2>
            <?php if($result->num_rows > 0): ?>
                <div class="admin-list">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="list-item">
                        <div class="list-item-header">
                            <?php if(!empty($row['image'])): ?>
                            <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="admin-image list-image">
                            <?php else: ?>
                            <div style="width: 80px; height: 50px; background: var(--bg-tertiary); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                                <i class="fas fa-image"></i>
                            </div>
                            <?php endif; ?>
                            <div style="flex-grow: 1;">
                                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                                <div class="list-item-meta">
                                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['category']); ?> | 
                                    <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-item-content">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </div>
                        
                        <div class="tags">
                            <?php 
                            $tags = explode(',', $row['tags']);
                            foreach($tags as $tag): 
                            ?>
                            <span class="tag"><?php echo trim(htmlspecialchars($tag)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="list-item-actions">
                            <?php if(!empty($row['live_url'])): ?>
                            <a href="<?php echo htmlspecialchars($row['live_url']); ?>" target="_blank" class="btn btn-secondary btn-sm">
                                <i class="fas fa-external-link-alt"></i> Live Demo
                            </a>
                            <?php endif; ?>
                            <?php if(!empty($row['code_url'])): ?>
                            <a href="<?php echo htmlspecialchars($row['code_url']); ?>" target="_blank" class="btn btn-secondary btn-sm">
                                <i class="fab fa-github"></i> Code
                            </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>No projects found. Add your first project above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>