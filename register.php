<?php
    define('PAGE_TITLE', 'PollGate | Register');
    require_once "src/common.php";
    continueSession(false, "", false);

    //HANDLE FORM SUBMISSION
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $confpassword = trim($_POST["confpassword"]);
        
        $errmsg = registerValidation($username, $password, $confpassword); 
        
        if(empty($errmsg)){
            $avatar = empty($_FILES["avatar"]["full_path"]) ? false : $_FILES["avatar"];
            $registerStatus = register($username, $password, $avatar);
            if(is_bool($registerStatus) && $registerStatus == true){
                header("Location: index.php");
                exit();
            } else {
                $errmsg = ["form-error" => $registerStatus];
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
<main class="default" id="register">
    <div class="container-flex-col card">
        <span class="title-large">Register</span>
        <span id="form-error" class="form-error-message">
            <?php if(isset($errmsg['form-error'])){ echo  $errmsg['form-error']; }?>
        </span>
        <form class="default" method="POST" action="#" enctype="multipart/form-data">
            <label for="username">*Username:
                <input type="text" id="username" name="username" value="<?php isset($_POST["username"]) ? $_POST["username"] : ""?>" required>
                <span id="username-error" class="form-error-message"></span>
            </label>

            <label for="password">*Password:
                <input type="password" id="password" name="password" required>
                <span id="password-error" class="form-error-message"></span>
            </label>

            <label for="confpassword">*Confirm password:
                <input type="password" id="confpassword" name="confpassword" required>
                <span id="confpassword-error" class="form-error-message"></span>
            </label>

            <label for="avatar">Profile picture:
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <span id="file-error" class="form-error-message"></span>
            </label>
            <div id="file-upload-container" class="file-upload">
                <span><i class="fa-solid fa-paperclip"></i> Attach a file</span>
                <span id="file-name" class="helper"></span>
            </div>

            <input type="hidden" name="csrf-token" value="<?php echo $csrfToken; ?>">

            <span class="helper">* Required fields</span>
            <button class="btn bg-blue" type="submit">Register</button>

            <span class="helper"><a href="login.php">Have an account? Log in here</a></span>
        </form>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>