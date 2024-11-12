<?php
    session_start();

    if(isset($_SESSION['token']) && isset($_SESSION['user_id'])){
        //echo "logged in with token: " . $_SESSION['token'] . " | " . $_SESSION['user_id'];
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
    <title>PollGate</title>
    <link rel="stylesheet" href="./public/assets/css/main.css">
    <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon.ico">
    <!-- <script src="public/assets/js/functions.js" defer></script> -->
</head>
<body>
    <?php include 'public/partials/header.php';?>
    <main>

    </main>
    <?php include 'public/partials/footer.php';?>
</body>
</html>