<?php
/**
 * Continues the current session, optionally authenticating the user and checking for admin privileges.
 *
 * @param bool $authenticate If true, the function will check if the user is authenticated.
 * @param string $redirect The URL to redirect to after successful login if authentication is required.
 * @param bool $adminOnly If true, the function will check if the user has admin privileges.
 *
 * @return void
 *
 * @throws void This function does not throw any exceptions.
 *
 * @example
 * // Continue session with authentication and admin check
 * continueSession(true, '/dashboard', true);
 *
 * // Continue session with authentication only
 * continueSession(true, '/dashboard', false);
 *
 * // Continue session without authentication or admin check
 * continueSession(false, '', false);
 */
function continueSession(bool $authenticate, string $redirect, bool $adminOnly) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if ($adminOnly && (!isset($_SESSION["user-role-id"]) || $_SESSION["user-role-id"] > 2)) {
        header("Location: /~kindlma7/PollGate/error.php?code=403");
        exit();
    }

    if ($authenticate && !isset($_SESSION["user-id"])) {
        header("Location: /~kindlma7/PollGate/login.php?redirect=$redirect");
        exit();
    }
}

/**
 * Logs in a user by verifying the provided username and password.
 *
 * @param string $username The username of the user attempting to log in.
 * @param string $password The password of the user attempting to log in.
 * @return bool Returns true if the login is successful, otherwise false.
 */
function login($username, $password) {
    require_once "dbcontext.php";

    // $user = $db->getUsernameAvailability($username);
    $user = $db->getUserByUsername($username);
    
    if ($user !== null && hash('sha256', $user->passwordSalt.$password, false) == $user->passwordHash) {
        $_SESSION["user-id"] = $user->id;
        $_SESSION["username"] = $user->username;
        $_SESSION["user-role-id"] = $user->roleId;
        $_SESSION["avatarToken"] = $user->avatarToken;
        return true;
    } else {
        return false;
    }
}

/**
 * Resizes an image to the specified width and height.
 *
 * @param array $image An associative array containing the image file information. 
 *                     The array should have a "tmp_name" key with the path to the temporary file.
 * @param int $width The desired width of the resized image.
 * @param int $height The desired height of the resized image.
 * @return string The resized image in PNG format as a binary string.
 */
function resizeImage($image, $width, $height) {
    $img = imagecreatefromstring(file_get_contents($image["tmp_name"]));
    $resized = imagescale($img, $width, $height);
    ob_start();
    imagepng($resized);
    $image = ob_get_contents();
    ob_end_clean();
    return $image;
}

/**
 * Saves an image file to the specified path with an optional prefix.
 *
 * @param string $image The image data to be saved.
 * @param string $filename The name of the file to save the image as.
 * @param string $prefix Optional. A prefix to add to the filename. Default is an empty string.
 * @return string The path where the image file was saved.
 */
function saveFile($image, $filename, $prefix = "") {
    $filename = $prefix.$filename;
    $path = "public/assets/avatars/$filename";
    file_put_contents($path.".png", $image);
    return $path;
}

/**
 * Registers a new user in the system.
 *
 * @param string $username The username of the new user.
 * @param string $password The password of the new user.
 * @param mixed $image Optional. The image file for the user's avatar. Default is false.
 * 
 * @return bool Returns true if the user was successfully registered, false otherwise.
 * 
 * @throws \Exception If there is an error during the user registration process.
 */
function register($username, $password, $image = false){
    require_once "dbcontext.php";
    $salt = bin2hex(random_bytes(16)); // TODO: change to obey unique constraint
    
    if($image){
        $avatar = resizeImage($image, 32, 32);
        $avatar2x = resizeImage($image, 64, 64);
    
        $filaname = bin2hex(random_bytes(16));
        $avatarToken = saveFile($avatar, $filaname);
        saveFile($avatar2x, $filaname, "2x@");
    } else {
        $avatarToken = "public/assets/avatars/default";
    }

    $newUser = new User(0, $username, $salt, hash('sha256', $salt.$password, false), 4, $avatarToken);

    try {
        $userId = $db->insertUser($newUser);
        if($userId > 0 ){
            $_SESSION["user-id"] = $userId;
            $_SESSION["username"] = $username;
            $_SESSION["user-role-id"] = $newUser->roleId;
            $_SESSION["avatarToken"] = $newUser->avatarToken;
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

/**
 * Logs out the current user by clearing session variables and destroying the session.
 * Redirects the user to the homepage after logging out.
 *
 * @return void
 */
function logout() {
    $_SESSION["user-id"] = null;
    $_SESSION["username"] = null;
    $_SESSION["user-role-id"] = null;
    session_destroy();
    header("Location: /~kindlma7/PollGate/index.php");
    exit();
}