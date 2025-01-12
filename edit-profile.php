<?php
    define('PAGE_TITLE', 'PollGate | Edit Profile');
    require_once "src/common.php";
    continueSession(true, "edit-profile.php", false);
    require_once "src/dbcontext.php";

    $user = $db->getUserById($_SESSION['user-id']);

    //HANDLE FORM SUBMISSION
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]) !== "" ? trim($_POST["username"]) : null;
        $password = trim($_POST["password"]) !== "" ? trim($_POST["password"]) : null;
        $confpassword = trim($_POST["confpassword"]) !== "" ? trim($_POST["confpassword"]) : null;

        $errmsg = [];

        try {
            if(isset($username) && $db->getUsernameAvailability($username) == 0 && $username !== $user->username){
            $errmsg['username-error'] = "Username is already taken.";
            }
        } catch (Exception $e) {
            $errmsg['form-error'] = "An error occurred while checking username availability.";
        }

        $errmsg['form-error'] = (!isset($_POST['csrf-token']) || $_POST['csrf-token'] !== $_SESSION['csrf-token']) ? "Invalid CSRF token." : null;
        $errmsg['username-error'] = empty($username) ? "Username cannot be empty." : null;

        if (!empty($password) || !empty($confpassword)) {
            $errmsg['password-error'] = empty($password) ? "Password is required." : null;
            $errmsg['confpassword-error'] = empty($confpassword) ? "Confirm password is required." : ($password !== $confpassword ? "Passwords do not match." : null);
        }

        // Remove null values from $errmsg
        $errmsg = array_filter($errmsg);
        
        if(empty($errmsg)){
            $avatar = empty($_FILES["avatar"]["full_path"]) ? null : $_FILES["avatar"];

            $editedUser = clone $user;

            $editedUser = validateProfileUpdate($user, $username, $password, $confpassword, $avatar);
            if($editedUser != false && $editedUser instanceof User){
                try {
                    $db->updateUser($editedUser);
                    $_SESSION['username'] == $editedUser->username;
                    $_SESSION['avatar-token'] = $editedUser->avatarToken;
                    header("Location: index.php");
                    exit();
                } catch (Exception $e) {
                    $errmsg = ["form-error" => "An error occurred while updating the profile."];
                }
            } else {
                $errmsg = ["form-error" => "Something went wrong"];
            }
        }
    }

    //CREATE CSRF TOKEN
    $csrfToken = bin2hex(random_bytes(16));
    $_SESSION["csrf-token"] = $csrfToken;
?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php" ?>
<script src="public/assets/js/usernameAjax.js" defer></script>
<main class="default" id="edit-profile">
    <div class="container-flex-col card">
        <span class="title-large">Edit Profile</span>
        <span id="form-error" class="form-error-message">
            <?php if(isset($errmsg['form-error'])){ echo  $errmsg['form-error']; }?>
        </span>
        <form class="default" method="POST" action="#" enctype="multipart/form-data">
            <label for="username">New username:
                <input type="text" id="username" data-currentUsername="<?php echo htmlspecialchars($user->username); ?>" name="username" value="<?php echo htmlspecialchars($user->username); ?>" >
                <span id="username-error" class="form-error-message">
                    <?php echo isset($errmsg['username-error']) ? $errmsg['username-error'] : ''; ?>
                </span>
            </label>

            <label for="password">New password:
                <input type="password" id="password" name="password">
                <span id="password-error" class="form-error-message">
                    <?php echo isset($errmsg['password-error']) ? $errmsg['password-error'] : ''; ?>
                </span>
            </label>

            <label for="confpassword">Confirm new password:
                <input type="password" id="confpassword" name="confpassword">
                <span id="confpassword-error" class="form-error-message">
                    <?php echo isset($errmsg['confpassword-error']) ? $errmsg['confpassword-error'] : ''; ?>
                </span>
            </label>

            <label for="avatar">New profile picture:
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <span id="file-error" class="form-error-message"></span>
            </label>
            <div id="file-upload-container" class="file-upload">
                <span><i class="fa-solid fa-paperclip"></i> Attach a file</span>
                <span id="file-name" class="helper"></span>
            </div>
            <span class="form-error-message">
                Updating profile with new profile picture, will permanently delete the old one!
            </span>

            <input type="hidden" name="csrf-token" value="<?php echo $csrfToken; ?>">

            <div class="user-actions">
                <button class="btn bg-blue" type="submit">Update profile</button><a class="btn bg-subtle" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>