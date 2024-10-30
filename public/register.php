<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once __DIR__ . '/../src/models/User.php';
    require_once __DIR__ . '/../src/config/_dbcontext.php';

    // Get the form data and sanitize it
    $username = htmlspecialchars(trim($_POST["username"]));
    $password = htmlspecialchars(trim($_POST["password"]));
    $salt = bin2hex(random_bytes(16));

    $newUser = new User([
        'username' => $username,
        'passwordSalt' => $salt,
        'passwordHash' => hash('sha256', $salt . $password, false)
        // 'roleId' => 2,
        // 'avatarPath' => 'path/to/avatar.jpg',
    ]);

    $newUser->insert($conn);

    // Simple validation
    // if (empty($username) || empty($password)) {
    //     $messages[] = "Both username and password are required.";
    // } else {
    //     // (In real use, you would save to a database here)
    //     // For demo purposes, we're storing data in session or simply showing success
    //     $messages[] = "Registration successful for user: $username";
    // }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/public/assets/img/favicon.ico">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
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
        <button type="submit">Register</button>
    </form>
</body>
</html>
