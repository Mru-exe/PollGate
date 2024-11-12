<?php
function authenticateUser(PDO $pdo, string $username, string $password): int{
    $sql = "SELECT Id, PasswordSalt, PasswordHash FROM Users WHERE Username = :username";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $res = $stmt->fetch();
    } catch (PDOException $e) {
        echo $e->getMessage();
        return 0;
    }

    if(empty($res) != 1 && hash('sha256', $res['PasswordSalt'] . $password, false) == $res['PasswordHash']){
        return $res['Id'];
    } else {
        return 0;
    }
    return 0;
}

function logoutUser() {
    // Clear session and cookie data, then redirect to login page or show an error
    try {
        session_unset();
        session_destroy();
    } catch (\Throwable $th) {
        echo $th;
    }
    setcookie('user_token', '', time() - 3600);  // Delete the cookie
    header("Location: ../../login.php");
    exit();
}

?>