<?php
require_once 'src/config/_dbcontext.php';
require_once 'src/auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data and sanitize it
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    if (empty($username) || empty($password)) {
        $errmsg = "Both username and password are required.";
        unset($newUser);
        exit;
    }

    $userId = authenticateUser($conn, $username, $password);
    
    if($userId > 0){
        session_start();

        $_SESSION['user_id'] = $userId;
        $_SESSION['token'] = bin2hex(random_bytes(16));

        header('Location: index.php');
        exit;
    } else {
        $errmsg = 'Username or password is incorrect.';
        unset($userId);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon.ico">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php
    // Display messages if there are any
    if (!empty($messages)) {
        foreach ($messages as $message) {
            echo "<p>$message</p>";
        }
    }
    ?>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Login</button>
        <?php if(isset($errmsg)) echo  "<br>" . $errmsg; ?>
    </form>
</body>
</html>
