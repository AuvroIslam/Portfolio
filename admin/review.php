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
    <title>Manage Client Reviews - Portfolio Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-container wide">
        <div class="admin-header">
            <h1><i class="fas fa-star" style="margin-right: 0.5rem;"></i>Manage Client Reviews</h1>
            <p>Add and manage client testimonials and reviews</p>
        </div>
        
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <a href="project.php"><i class="fas fa-folder-open"></i> Projects</a>
            <a href="about.php"><i class="fas fa-user"></i> About</a>
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
            <h2><i class="fas fa-plus-circle"></i> Add New Review</h2>
            <form method="post" enctype="multipart/form-data" class="admin-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="client_name"><i class="fas fa-user"></i> Client Name</label>
                        <input type="text" name="client_name" id="client_name" placeholder="Enter client name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_title"><i class="fas fa-building"></i> Client Title/Company</label>
                        <input type="text" name="client_title" id="client_title" placeholder="e.g., Web Development Project, Mio App User" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="review_text"><i class="fas fa-quote-left"></i> Review/Testimonial</label>
                    <textarea name="review_text" id="review_text" placeholder="Client's testimonial..." required></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 200px 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="rating"><i class="fas fa-star"></i> Star Rating</label>
                        <select name="rating" id="rating" required>
                            <option value="">Select Rating</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                            <option value="4">⭐⭐⭐⭐☆ 4 Stars</option>
                            <option value="3">⭐⭐⭐☆☆ 3 Stars</option>
                            <option value="2">⭐⭐☆☆☆ 2 Stars</option>
                            <option value="1">⭐☆☆☆☆ 1 Star</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_image"><i class="fas fa-camera"></i> Client Photo</label>
                        <input type="file" name="client_image" id="client_image" accept="image/*" class="file-upload-input">
                        <div class="upload-info">
                            <i class="fas fa-info-circle"></i> Optional. Supported formats: JPG, PNG, GIF, WebP (Max size: 5MB)
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Review
                </button>
            </form>
        </div>
        
        <div class="admin-card">
            <h2><i class="fas fa-list"></i> Existing Reviews (<?php echo $result->num_rows; ?>)</h2>
            <?php if($result->num_rows > 0): ?>
                <div class="admin-list">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="list-item">
                        <div class="list-item-header">
                            <img src="<?php 
                                if(strpos($row['client_image'], 'assets/uploads/') === 0) {
                                    echo '../' . htmlspecialchars($row['client_image']);
                                } else {
                                    echo htmlspecialchars($row['client_image']);
                                }
                            ?>" alt="<?php echo htmlspecialchars($row['client_name']); ?>" class="admin-image list-image rounded">
                            <div style="flex-grow: 1;">
                                <h4><?php echo htmlspecialchars($row['client_name']); ?></h4>
                                <div class="list-item-meta">
                                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($row['client_title']); ?> | 
                                    <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                </div>
                            </div>
                            <div class="rating-display">
                                <?php echo str_repeat('⭐', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?>
                                <span class="rating-text"><?php echo $row['rating']; ?>/5</span>
                            </div>
                        </div>
                        
                        <div class="list-item-content">
                            <i class="fas fa-quote-left" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                            <?php echo htmlspecialchars($row['review_text']); ?>
                            <i class="fas fa-quote-right" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                        </div>
                        
                        <div class="list-item-actions">
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this review?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <i class="fas fa-star" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>No reviews found. Add your first review above!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .rating-display {
            text-align: right;
            font-size: 1.2rem;
        }
        .rating-text {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-left: 0.5rem;
        }
        .rounded {
            border-radius: 50% !important;
        }
    </style>
</body>
</html>