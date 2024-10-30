<?php
    session_start();

    if(isset($_SESSION['token']) && isset($_SESSION['user_id'])){
        echo "logged in with token: " . $_SESSION['token'] . " | " . $_SESSION['user_id'];
    } else {
        session_unset();
        session_destroy();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Index
</body>
</html>