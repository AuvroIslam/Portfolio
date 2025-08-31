<?php
session_start();
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
$conn = new mysqli("localhost", "root", "", "portfolio");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $old = $_POST['old_password'];
    $new = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username=?");
    $stmt->bind_param("s", $_SESSION['admin']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if(password_verify($old, $result['password'])){
        $stmt = $conn->prepare("UPDATE admins SET password=? WHERE username=?");
        $stmt->bind_param("ss", $new, $_SESSION['admin']);
        $stmt->execute();
        echo "Password changed successfully!";
    } else {
        echo "Old password incorrect.";
    }
}
?>
<form method="post">
    <input type="password" name="old_password" placeholder="Old Password" required><br>
    <input type="password" name="new_password" placeholder="New Password" required><br>
    <button type="submit">Change Password</button>
</form>