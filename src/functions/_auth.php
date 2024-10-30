<?php

function authenticate(PDO $pdo, string $username, string $password): int{
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

?>