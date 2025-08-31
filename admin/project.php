<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $conn->query("INSERT INTO projects (title, description) VALUES ('$title', '$desc')");
}
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM projects WHERE id=$id");
}
$result = $conn->query("SELECT * FROM projects");
?>
<h2>Projects</h2>
<form method="post">
    <input type="text" name="title" placeholder="Project Title" required><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <button type="submit" name="add">Add Project</button>
</form>
<ul>
<?php while($row = $result->fetch_assoc()): ?>
<li><?php echo $row['title']; ?> - <?php echo $row['description']; ?> 
<a href="?delete=<?php echo $row['id']; ?>">Delete</a></li>
<?php endwhile; ?>
</ul>