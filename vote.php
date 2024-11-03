<?php

require_once 'src/config/_dbcontext.php';
require_once 'src/functions/auth.php';

session_start();

if (isset($_SESSION['user_token']) && isset($_COOKIE['user_token'])) {
    if (hash_equals($_SESSION['user_token'], $_COOKIE['user_token'])) {
        // Session is valid
    } else {
        // Token mismatch: handle invalid session, e.g., log out the user
        logoutUser();
    }
} else {
    // Missing token: handle as needed
    logoutUser();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
</head>
<body>
    Vote
</body>
</html>