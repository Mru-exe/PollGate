<?php
//VALIDATION API
require_once __DIR__ . "/../src/common.php";
// continueSession(false, "", false);
require_once __DIR__ . "/../src/dbcontext.php";

//Listen AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("POST request received");
    $username = htmlspecialchars(trim($_POST["username"]));
    if($db->getUsernameAvailability($username) == 0){
        echo json_encode(["available" => true]);
    } else {
        echo json_encode(["available" => false]);
    }
}