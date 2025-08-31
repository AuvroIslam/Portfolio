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
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #333; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #007cba; text-decoration: none; }
        .form-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-row input, .form-row textarea, .form-row select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-row textarea { height: 100px; resize: vertical; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .projects-list { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .project-item { border: 1px solid #eee; margin-bottom: 15px; padding: 15px; border-radius: 4px; }
        .project-item h4 { margin: 0 0 10px 0; color: #333; }
        .project-meta { color: #666; font-size: 0.9em; margin-bottom: 10px; }
        .project-tags span { background: #f0f0f0; padding: 2px 8px; border-radius: 3px; font-size: 0.8em; margin-right: 5px; }
        .delete-btn { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.9em; }
        .delete-btn:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .file-upload { position: relative; display: inline-block; width: 100%; }
        .file-upload input[type=file] { width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9; }
        .file-upload input[type=file]:hover { border-color: #007cba; }
        .upload-info { font-size: 0.9em; color: #666; margin-top: 5px; }
        .project-image { max-width: 100px; height: 60px; object-fit: cover; border-radius: 4px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Projects</h1>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">← Dashboard</a>
            <a href="about.php">About</a>
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
            <h2>Add New Project</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="title">Project Title:</label>
                    <input type="text" name="title" id="title" placeholder="Project Title" required>
                </div>
                
                <div class="form-row">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" placeholder="Project Description" required></textarea>
                </div>
                
                <div class="form-row">
                    <label for="image">Project Image:</label>
                    <div class="file-upload">
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <div class="upload-info">Supported formats: JPG, PNG, GIF, WebP (Max size: 5MB)</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <label for="category">Category:</label>
                    <select name="category" id="category" required>
                        <option value="">Select Category</option>
                        <option value="web">Web Apps</option>
                        <option value="mobile">Mobile</option>
                        <option value="data">Data Science</option>
                        <option value="game">Games</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <label for="tags">Tags (comma separated):</label>
                    <input type="text" name="tags" id="tags" placeholder="React,Node.js,MongoDB" required>
                </div>
                
                <div class="form-row">
                    <label for="live_url">Live Demo URL (optional):</label>
                    <input type="url" name="live_url" id="live_url" placeholder="https://example.com">
                </div>
                
                <div class="form-row">
                    <label for="code_url">Code Repository URL (optional):</label>
                    <input type="url" name="code_url" id="code_url" placeholder="https://github.com/username/repo">
                </div>
                
                <button type="submit" name="add" class="btn">Add Project</button>
            </form>
        </div>
        
        <div class="projects-list">
            <h2>Existing Projects (<?php echo $result->num_rows; ?>)</h2>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="project-item">
                    <div style="display: flex; gap: 15px;">
                        <div style="flex-shrink: 0;">
                            <?php if(!empty($row['image'])): ?>
                            <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="project-image">
                            <?php else: ?>
                            <div style="width: 100px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8em; color: #666;">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div style="flex-grow: 1;">
                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                            <div class="project-meta">
                                <strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?> | 
                                <strong>Created:</strong> <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                            </div>
                            <p><?php echo htmlspecialchars($row['description']); ?></p>
                            <div class="project-tags">
                                <strong>Tags:</strong> 
                                <?php 
                                $tags = explode(',', $row['tags']);
                                foreach($tags as $tag): 
                                ?>
                                <span><?php echo trim(htmlspecialchars($tag)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top: 10px;">
                                <?php if(!empty($row['live_url'])): ?>
                                <a href="<?php echo htmlspecialchars($row['live_url']); ?>" target="_blank" style="margin-right: 10px;">Live Demo</a>
                                <?php endif; ?>
                                <?php if(!empty($row['code_url'])): ?>
                                <a href="<?php echo htmlspecialchars($row['code_url']); ?>" target="_blank" style="margin-right: 10px;">View Code</a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects found. Add your first project above!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>