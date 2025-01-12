<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo PAGE_TITLE; ?></title>
    <link rel="stylesheet" href="public/assets/css/layout.css">
    <link rel="stylesheet" href="public/assets/css/animations.css">
    <link rel="icon" href="public/assets/img/pollgate-icon.svg" type="image/svg+xml">
    <script src="public/assets/js/common.js" defer></script>
    <script src="https://kit.fontawesome.com/4d461626b0.js" crossorigin="anonymous"></script>
    <!-- <script src="public/assets/js/script.js" defer></script> -->
</head>
<body>
<header class="default-header">
    <div class="header-title">
        <img src="public/assets/img/pollgate-icon.svg" height="32" width="32" alt="PollGate icon">
        <a class="silent bold" id="home-anchor" href="/~kindlma7/PollGate/index.php">PollGate</a>
        |
        <a class="silent" href="/~kindlma7/PollGate/docs.php">Docs</a>
        <?php if(isset($_SESSION["user-role-id"]) && $_SESSION["user-role-id"] < 2){ echo '| <a class="silent" href="admin.php">Admin Panel</a>'; } ?>
    </div>
    <?php
    if(isset($_SESSION["user-id"])){
        echo '<a class="silent centered bold" href="/~kindlma7/PollGate/profile.php">';
        echo htmlspecialchars($_SESSION["username"]);
        echo '&nbsp;<img class="avatar" alt="User Avatar" height="32" width="32" src="public/assets/avatars/' . $_SESSION["avatar-token"] . '">';
        echo '</a>';
        echo '<a class="silent" href="/~kindlma7/PollGate/api/logout.php"><i class="fa-solid fa-sign-out"></i></a>';
    } else {
        echo '<a class="silent btn bg-blue" href="/~kindlma7/PollGate/login.php"><i class="fa-solid fa-key"></i> Login</a>';
        echo '<a class="silent btn bg-blue" href="/~kindlma7/PollGate/register.php"><i class="fa-solid fa-user-plus"></i> Register</a>';
    }
    ?>
</header>