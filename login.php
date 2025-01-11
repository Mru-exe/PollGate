<?php
    define('PAGE_TITLE', 'PollGate | Login');
    require_once "src/common.php";
    continueSession(false, "", false);

    $redirect = $_GET["redirect"] ?? "index.php";

    //HANDLE FORM SUBMISSION
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        $errmsg = loginValidation($username, $password);

        if(empty($errmsg)) {
            header("Location: $redirect");
            exit();
        }
    }

    //CREATE CSRF TOKEN
    $csrfToken = bin2hex(random_bytes(16));
    $_SESSION["csrf-token"] = $csrfToken;
?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php"; ?>
<main class="default" id="login">
    <div class="card container-flex-col">
        <span class="title-large">Login</span>
        <span id="form-error" class="form-error-message">
            <?php if(isset($errmsg)) echo $errmsg; ?>
        </span>
        <form class="default" method="POST" action="#">
            <label for="username">*Username:
                <input type="text" id="username" name="username" value="<?php if(isset($username)) echo $username; ?>">
                <span id="username-error" class="form-error-message"></span>
            </label>
            
            <label for="login-password">*Password:
                <input type="password" id="login-password" name="password">
                <span id="password-error" class="form-error-message"></span>
            </label>
            
            <input type="hidden" name="csrf-token" value="<?php echo $csrfToken; ?>">
            
            <span class="helper">* Required fields</span>
            <button class="btn bg-blue" type="submit">Login</button>

            <span class="helper"><a href="register.php">New? Register here</a></span>
        </form>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>