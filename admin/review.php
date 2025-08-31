<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: /Portfolio/admin/login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $review = $_POST['review'];
    $conn->query("INSERT INTO reviews (name, review) VALUES ('$name', '$review')");
}
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reviews WHERE id=$id");
}
$result = $conn->query("SELECT * FROM reviews");
?>
<h2>Client Reviews</h2>
<form method="post">
    <input type="text" name="name" placeholder="Client Name" required><br>
    <textarea name="review" placeholder="Review"></textarea><br>
    <button type="submit" name="add">Add Review</button>
</form>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
<li><?php echo $row['name']; ?> - "<?php echo $row['review']; ?>" 
<a href="?delete=<?php echo $row['id']; ?>">Delete</a></li>
<?php endwhile; ?>
</ul>