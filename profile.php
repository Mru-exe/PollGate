<?php
define('PAGE_TITLE', 'My Profile');
require_once "src/common.php";
continueSession(true, "login.php", false);

require_once "src/dbcontext.php";

$currentUser = $db->getUserById($_SESSION["user-id"]);
?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php" ?>
<script src="public/js/index.js"></script>
<main class="default">
    <h2> Profile </h2>
    <div class="user-profile">
        <span>
            <img class="avatar-lg" height="64" width="64" src="<?php echo $currentUser->avatarUrl; ?>@2x.png"></img>
            <span class="title">
                <?php echo $currentUser->username; ?>
            </span>
        </span>
        <span>Joined <?php echo $currentUser->created->format("m/d/Y"); ?></span>
        <span>Role <?php echo $currentUser->roleName; ?></span>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>