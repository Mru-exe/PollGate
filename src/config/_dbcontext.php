<?php
$database = "mysql:host=" . $_ENV['dbserver'] . ";dbname=" . $_ENV['dbname'];
$username = $_ENV['dbusername'] ;
$password = $_ENV['dbpassword'];

echo($database . "\n" . $username . "\n" . $password);

// try {
//     $conn = new PDO($database, $username, $password);
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     echo "Connection successful";
//   } catch(PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
//   }
?>