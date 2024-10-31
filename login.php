<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // require_once __DIR__ . '/../src/models/User.php';
    require_once 'src/config/_dbcontext.php';
    require_once 'src/functions/_auth.php';

    // Get the form data and sanitize it
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    $userId = authenticate($conn, $username, $password);
    if($userId > 0){
        //Start new session
        session_start();
        $_SESSION['user_id'] = $userId;
        // $_SESSION['user'] = new User
        $_SESSION['token'] = bin2hex(random_bytes(16));
        //Redirect home
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
