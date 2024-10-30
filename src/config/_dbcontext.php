<?php
// $database = "mysql:host=" . $_ENV['dbserver'] . ";dbname=" . $_ENV['dbname'];
// $username = $_ENV['dbusername'] ;
// $password = $_ENV['dbpassword'];

// echo($database . "\n" . $username . "\n" . $password);

try {
    $conn = new PDO("mysql:host=localhost;dbname=kindlma7", "kindlma7", "webove aplikace");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
  }
?>