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
        $_SESSION["avatar-token"] = $user->avatarToken;
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
    return $filename.".png";
}

/**
 * Registers a new user in the system.
 *
 * @param string $username The username of the new user.
 * @param string $password The password of the new user.
 * @param array|bool $image Optional. The image file for the user's avatar. Default is false.
 * 
 * @return bool|string Returns true if the user was successfully registered, error message otherwise.
 * 
 * @throws \Exception If there is an error during the user registration process.
 */
function register($username, $password, $image = false){
    require_once "dbcontext.php";
    $salt = bin2hex(random_bytes(16));
    
    if($image){
        $avatar = resizeImage($image, 32, 32);
        $avatar2x = resizeImage($image, 64, 64);
    
        $filaname = bin2hex(random_bytes(16));
        $avatarToken = saveFile($avatar, $filaname);
        saveFile($avatar2x, $filaname, "2x@");
    } else {
        $avatarToken = "defaultAvatar.png";
    }

    $newUser = new User(0, $username, $salt, hash('sha256', $salt.$password, false), 4, $avatarToken);

    try {
        $userId = $db->insertUser($newUser);
        if($userId > 0 ){
            $_SESSION["user-id"] = $userId;
            $_SESSION["username"] = $username;
            $_SESSION["user-role-id"] = $newUser->roleId;
            $_SESSION["avatar-token"] = $newUser->avatarToken;
            return true;
        }
    } catch (PDOException $e) {
        if($e->getCode() == 23000){
            return "User already exists";
        } else {
            return $e->getMessage();
        }
    }
    return "Something went wrong, try again...";
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

/**
 * Shortens a given string to a specified maximum length, appending an ellipsis if the string is truncated.
 *
 * @param string $inputString The string to be shortened.
 * @param int $maxLength The maximum length of the returned string, including the ellipsis.
 * @return string The shortened string, with an ellipsis appended if it was truncated.
 */
function shortenString($inputString, $maxLength) {
    if (strlen($inputString) > $maxLength) {
        return substr($inputString, 0, $maxLength - 3) . '...';
    }
    return $inputString;
}

/**
 * Validates the registration form input.
 *
 * @param string $username The username input from the registration form.
 * @param string $password The password input from the registration form.
 * @param string $confpassword The confirmation password input from the registration form.
 * 
 * @return array|string An array with an error message if validation fails, or an empty string if validation passes.
 */
function registerValidation($username, $password, $confpassword) { 
    //VALIDATE CSRF TOKEN
    if(!hash_equals($_SESSION["csrf-token"], $_POST["csrf-token"])) {
        return ["form-error" => "Invalid CSRF token."];
    }
    //VALIDATE INPUT
    if(empty($username) || empty($password) || empty($confpassword)) {
        return ["form-error" => "Please fill out all fields."];
    }
    //VALIDATE PASSWORDS MATCH
    if($confpassword != $password) {
        return ["form-error" => "Passwords didn't match."]; //TODO: předělat key na "password-error" (kill me)
    }
    return "";
}

/**
 * Validates the login process by checking CSRF token, input fields, and attempting login.
 *
 * @param string $username The username provided by the user.
 * @param string $password The password provided by the user.
 * @return string Returns an error message if validation fails, otherwise returns an empty string.
 */
function loginValidation($username, $password) {
    //VALIDATE CSRF TOKEN
    if(!hash_equals($_SESSION["csrf-token"], $_POST["csrf-token"])) {
        return "Invalid CSRF token.";
    }
    //VALIDATE INPUT
    if(empty($username) || empty($password)) {
        return "Please fill out all fields.";
    }
    //ATTEMPT LOGIN
    if(!login($username, $password)) {
        return "Invalid username or password.";
    }
    return "";
}

/**
 * Validates and updates the user's profile information.
 *
 * @param User $user The user object to be updated.
 * @param string|null $username The new username, or null to keep the current username.
 * @param string|null $pw The new password, or null to keep the current password.
 * @param string|null $confpw The confirmation of the new password, or null to keep the current password.
 * @param array|null $avatar The new avatar image data, or null to keep the current avatar.
 * 
 * @return bool|User Returns false if no updates are provided, otherwise returns the updated user object.
 */
function validateProfileUpdate(User $user, string $username = null, string $pw = null, string $confpw = null, array|null $avatar = null){
    if (!isset($username) && !isset($pw) && !isset($confpw) && !isset($avatar)) {
        return false;
    }
    $user->username = isset($username) ? $username : $user->username;

    if (isset($pw) && isset($confpw) && $pw === $confpw) {
        var_dump($pw);
        $user->passwordHash = hash('sha256', $user->passwordSalt.$pw, false);
    }
    if (isset($avatar)) {
        $avatarResized = resizeImage($avatar, 32, 32);
        $avatarResized2x = resizeImage($avatar, 64, 64);
        $filename = bin2hex(random_bytes(16));
        $oldAvatarToken = $user->avatarToken;
        if ($oldAvatarToken !== "defaultAvatar.png") {
            unlink("public/assets/avatars/$oldAvatarToken");
            unlink("public/assets/avatars/2x@$oldAvatarToken");
        }
        $user->avatarToken = saveFile($avatarResized, $filename);
        saveFile($avatarResized2x, $filename, "2x@");
    }
    $user->modified = new DateTime();
    return $user;
}