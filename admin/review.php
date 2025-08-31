<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if(isset($_POST['add'])){
    $client_name = $_POST['client_name'];
    $client_title = $_POST['client_title'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    $client_image = '';
    
    // Handle file upload for client image
    if(isset($_FILES['client_image']) && $_FILES['client_image']['error'] == 0) {
        $upload_dir = '../assets/uploads/';
        $file_extension = strtolower(pathinfo($_FILES['client_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_extension, $allowed_extensions)) {
            $new_filename = 'client_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['client_image']['tmp_name'], $upload_path)) {
                $client_image = 'assets/uploads/' . $new_filename;
            } else {
                $error = "Failed to upload client image.";
            }
        } else {
            $error = "Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.";
        }
    } else {
        // Use a default placeholder if no image uploaded
        $client_image = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=100&auto=format&fit=crop';
    }
    
    if(empty($error)) {
        $stmt = $conn->prepare("INSERT INTO reviews (client_name, client_title, review_text, rating, client_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $client_name, $client_title, $review_text, $rating, $client_image);
        $stmt->execute();
        $success = "Review added successfully!";
    }
}
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    
    // Get the image path before deleting the record
    $stmt = $conn->prepare("SELECT client_image FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $review = $result->fetch_assoc();
    
    // Delete the image file if it exists in uploads folder
    if($review && !empty($review['client_image']) && strpos($review['client_image'], 'assets/uploads/') === 0) {
        $file_path = '../' . $review['client_image'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete the database record
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = "Review deleted successfully!";
}
$result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Portfolio Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        .header { background: #333; color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #007cba; text-decoration: none; }
        .form-container { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-row input, .form-row textarea, .form-row select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-row textarea { height: 80px; resize: vertical; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .reviews-list { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .review-item { border: 1px solid #eee; margin-bottom: 15px; padding: 15px; border-radius: 4px; }
        .review-header { display: flex; align-items: center; margin-bottom: 10px; gap: 15px; }
        .client-image { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .client-info h4 { margin: 0; color: #333; }
        .client-info span { color: #666; font-size: 0.9em; }
        .stars { color: #ffc107; margin-bottom: 10px; }
        .delete-btn { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.9em; }
        .delete-btn:hover { background: #c82333; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .file-upload { position: relative; display: inline-block; width: 100%; }
        .file-upload input[type=file] { width: 100%; padding: 10px; border: 2px dashed #ddd; border-radius: 4px; background: #f9f9f9; }
        .file-upload input[type=file]:hover { border-color: #007cba; }
        .upload-info { font-size: 0.9em; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Client Reviews</h1>
        </div>
        
        <div class="nav">
            <a href="dashboard.php">← Dashboard</a>
            <a href="project.php">Projects</a>
            <a href="about.php">About</a>
            <a href="../index.php" target="_blank">View Portfolio</a>
        </div>
        
        <?php if(isset($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Add New Review</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="client_name">Client Name:</label>
                    <input type="text" name="client_name" id="client_name" placeholder="Client Name" required>
                </div>
                
                <div class="form-row">
                    <label for="client_title">Client Title/Company:</label>
                    <input type="text" name="client_title" id="client_title" placeholder="e.g., Web Development Project, Mio App User, Data Science Consultant" required>
                </div>
                
                <div class="form-row">
                    <label for="review_text">Review/Testimonial:</label>
                    <textarea name="review_text" id="review_text" placeholder="Client's testimonial..." required></textarea>
                </div>
                
                <div class="form-row">
                    <label for="rating">Star Rating:</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select Rating</option>
                        <option value="5">5 Stars (★★★★★)</option>
                        <option value="4">4 Stars (★★★★☆)</option>
                        <option value="3">3 Stars (★★★☆☆)</option>
                        <option value="2">2 Stars (★★☆☆☆)</option>
                        <option value="1">1 Star (★☆☆☆☆)</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <label for="client_image">Client Image:</label>
                    <div class="file-upload">
                        <input type="file" name="client_image" id="client_image" accept="image/*">
                        <div class="upload-info">Upload client photo (optional). Supported formats: JPG, PNG, GIF, WebP</div>
                    </div>
                </div>
                
                <button type="submit" name="add" class="btn">Add Review</button>
            </form>
        </div>
        <div class="reviews-list">
            <h2>Existing Reviews (<?php echo $result->num_rows; ?>)</h2>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="review-item">
                    <div class="review-header">
                        <img src="<?php 
                            if(strpos($row['client_image'], 'assets/uploads/') === 0) {
                                echo '../' . htmlspecialchars($row['client_image']);
                            } else {
                                echo htmlspecialchars($row['client_image']);
                            }
                        ?>" alt="Client" class="client-image">
                        <div class="client-info">
                            <h4><?php echo htmlspecialchars($row['client_name']); ?></h4>
                            <span><?php echo htmlspecialchars($row['client_title']); ?></span>
                        </div>
                    </div>
                    
                    <div class="stars">
                        <?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?>
                    </div>
                    
                    <p>"<?php echo htmlspecialchars($row['review_text']); ?>"</p>
                    
                    <div style="margin-top: 10px;">
                        <small style="color: #666;">Added: <?php echo date('M j, Y', strtotime($row['created_at'])); ?></small>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this review?')" style="float: right;">Delete</a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews found. Add your first review above!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>