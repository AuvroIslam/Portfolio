<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $content = $_POST['content'];
    $conn->query("UPDATE about SET content='$content' WHERE id=1");
}
$row = $conn->query("SELECT * FROM about WHERE id=1")->fetch_assoc();
?>
<h2>About Section</h2>
<form method="post">
    <textarea name="content"><?php echo $row['content']; ?></textarea><br>
    <button type="submit">Update</button>
</form>