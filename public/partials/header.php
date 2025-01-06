<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo PAGE_TITLE; ?></title>
    <link rel="stylesheet" href="public/assets/css/layout.css">
    <script src="https://kit.fontawesome.com/4d461626b0.js" crossorigin="anonymous"></script>
    <!-- <script src="public/assets/js/script.js" defer></script> -->
</head>
<body>
<header class="default-header">
    <div class="header-title">
        <a class="silent bold" id="home-anchor" href="/~kindlma7/PollGate/index.php">Home</a>
    </div>
    <?php
    if(isset($_SESSION["user-id"])){
        echo '<a class="silent centered bold" href="/~kindlma7/PollGate/profile.php">';
        echo $_SESSION["username"];
        echo '&nbsp;<img class="avatar" height="32" width="32" src="' . $_SESSION["avatarUrl"] . '.png"></img>';
        echo '</a>';
        echo '<a class="silent" label="asd" href="/~kindlma7/PollGate/api/logout.php"><i class="fa-solid fa-sign-out"></i></a>';
    } else {
        echo '<a class="silent btn" href="/~kindlma7/PollGate/login.php"><i class="fa-solid fa-key"></i> Login</a>';
        echo '<a class="silent btn" href="/~kindlma7/PollGate/register.php"><i class="fa-solid fa-user-plus"></i> Register</a>';
    }
    ?>
</header>