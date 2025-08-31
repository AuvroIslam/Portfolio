<?php
$conn = new mysqli("localhost", "root", "", "portfolio");
$password = password_hash("admin123", PASSWORD_DEFAULT);
$conn->query("INSERT INTO admins (username, password) VALUES ('admin', '$password')");
echo "Admin user created with username: admin and password: admin123";
?>