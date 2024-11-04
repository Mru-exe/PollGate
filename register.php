<?php
require_once 'src/config/_dbcontext.php';
require_once 'src/models/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data and sanitize it
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $confpassword = htmlspecialchars(trim($_POST["confpassword"]));

    $salt = bin2hex(random_bytes(16));
    $errmsg = ''; // commentable?

    if (empty($username) || empty($password) || empty($confpassword)) {
        $errmsg = "All fields are required.";
        exit;
    } elseif ($confpassword != $password) {
        $errmsg = "Passwords don't match";
        exit;
    } else {
        $errmsg = "Unexpected error occured.";
        exit;
    }

    $newUser = new User([
        'username' => $username,
        'passwordSalt' => $salt,
        'passwordHash' => hash('sha256', $salt . $password, false),
        'roleId' => 1   
        // 'avatarPath' => 'path/to/avatar.jpg',
    ]);

    try {
        $userId = $newUser->insert($conn);
        if($userId > 0 ){
            session_start();

            $_SESSION['user_id'] = $userId;
            $_SESSION['token'] = bin2hex(random_bytes(16));

            header('Location: index.php');
            exit;
        } else {
            $errmsg = 'An error occured. Try again.';
            unset($newUser);
        }
    } catch (PDOException $e) {
        if($e->getCode() == 23000){
            $errmsg = 'User already exists';
            unset($newUser);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon.ico">
    <title>Register</title>
    <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon.ico">
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confpassword">Confirm password:</label>
        <input type="password" id="confpassword" name="confpassword" required>

        <button type="submit">Register</button>
        <?php if(isset($errmsg)) echo  "<br><span class=\"form-error-message\">" . $errmsg; ?>
    </form>
</body>
</html>
