<?php
require_once __DIR__ . "/../src/dbcontext.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("POST request received");
    $username = htmlspecialchars(trim($_POST["username"]));
    if($db->getUsernameAvailability($username) == 0){
        echo json_encode(["available" => true]);
    } else {
        echo json_encode(["available" => false]);
    }
}