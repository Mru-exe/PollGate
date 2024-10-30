<?php 

include (__DIR__ . '/../src/config/_dbcontext.php'); // _dbcontext.php

?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form id="login-form" action="./_register.php" method="POST">
        <label for="username"> Username </label>
        <input type="text" id="username" name="username" required>
        <label for="password"> Password </label>
        <input type="password" id="password" name="password" required>
        <input type="submit" id="submit" value="Login">
    </form>
    <a href="#/register.hmtl">Not registered yet?</a>
    <a href="#/pw-reset.php">Forgot password?</a>
</body>
</html>