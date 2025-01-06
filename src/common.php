<?php

function continueSession(bool $authenticate, string $redirect, bool $adminOnly) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($authenticate && !isset($_SESSION["user-id"])) {
        header("Location: /~kindlma7/PollGate/login.php?redirect=$redirect");
        exit();
    }

    if ($adminOnly && !isset($_SESSION["user-role-id"]) && $_SESSION["user-role-id"] != 1) {
        header("Location: /~kindlma7/PollGate/error.php?code=403");
        exit();
    }
}

function login($username, $password) {
    require_once "dbcontext.php";

    // $user = $db->getUsernameAvailability($username);
    $user = $db->getUserByUsername($username);
    
    if ($user !== null && hash('sha256', $user->passwordSalt.$password, false) == $user->passwordHash) {
        $_SESSION["user-id"] = $user->id;
        $_SESSION["username"] = $user->username;
        $_SESSION["user-role-id"] = $user->roleId;
        $_SESSION["avatarUrl"] = $user->avatarUrl;
        return true;
    } else {
        return false;
    }
}

function resizeImage($image, $width, $height) {
    $img = imagecreatefromstring(file_get_contents($image["tmp_name"]));
    $resized = imagescale($img, $width, $height);
    ob_start();
    imagepng($resized);
    $image = ob_get_contents();
    ob_end_clean();
    return $image;
}

function saveFile($image, $filename, $suffix = "") {
    $filename = $filename.$suffix;
    $path = "public/assets/avatars/$filename";
    file_put_contents($path.".png", $image);
    return $path;
}

function register($username, $password, $image = false){
    require_once "dbcontext.php";
    $salt = bin2hex(random_bytes(16)); // TODO: change to obey unique constraint

    $avatar = resizeImage($image, 32, 32);
    $avatar2x = resizeImage($image, 64, 64);

    $filaname = bin2hex(random_bytes(16));

    if($image){
        $avatarUrl = saveFile($avatar, $filaname);
        saveFile($avatar2x, $filaname, "@2x");
    } else {
        $avatarUrl = "public/assets/avatars/default.png";
    }

    $newUser = new User(0, $username, $salt, hash('sha256', $salt.$password, false), 4, $avatarUrl);

    try {
        $userId = $db->insertUser($newUser);
        if($userId > 0 ){
            $_SESSION["user-id"] = $userId;
            $_SESSION["username"] = $username;
            $_SESSION["user-role-id"] = $newUser->roleId;
            $_SESSION["avatarUrl"] = $newUser->avatarUrl;
            return true;
        }
    } catch (\Exception $e) {
        // if($e->getCode() == 23000){
        //     $errmsg = 'User already exists';
        // }
        die($e->getMessage());
    }
    return false;
}

function logout() {
    $_SESSION["user-id"] = null;
    $_SESSION["username"] = null;
    $_SESSION["user-role-id"] = null;
    session_destroy();
    header("Location: /~kindlma7/PollGate/index.php");
    exit();
}


