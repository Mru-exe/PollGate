<?php
define('PAGE_TITLE', 'Vote');
require_once "src/common.php";

$pid = isset($_GET['pid']) ? $_GET['pid'] : 0;

if($pid <= 0){
    header("Location: error.php?code=404");
}

continueSession(false, "vote.php?pid=".$pid, false);
require_once "src/dbcontext.php";

try {
    $userVote = isset($_SESSION['user-id']) ? $db->getUserVote($_SESSION['user-id'], $pid) : false;
    $poll = $db->getPollById($pid);
    $pollOptions = $db->getPollOptionsByPollId($pid);
} catch (\Throwable $th) {
    header("Location: error.php?code=404");
    exit();
} catch (Exception $e) {
    exit($e);
}


$errmsg = null;

if($_SERVER["REQUEST_METHOD"] == "POST" && !$userVote){
    if(!isset($_SESSION['user-id'])){
        header("Location: login.php?redirect=vote.php?pid=".$pid);
        exit();
    }

    //VALIDATE CSRF TOKEN
    if(!hash_equals($_SESSION["csrf-token"], $_POST["csrf-token"])) {
        $errmsg = "Invalid CSRF Token.";
    }

    //VALIDATE INPUT
    $selectedOption = isset($_POST['radio']) ? $_POST['radio'] : null;
    if($selectedOption == null){
        $errmsg = "Select from options to vote...";
    }


    if (!isset($errmsg)) {
        try {
            $vote = new Vote(0, $pid, $selectedOption, "now", $_SESSION['user-id']);
            $db->insertVote($vote);
            header("Location: index.php?");
            exit();
        } catch (PDOException $e) {
            header("Location: error.php?code=500&msg=".$e->getMessage());
            exit();
        }
    }
}

//CREATE CSRF TOKEN
$csrfToken = bin2hex(random_bytes(16));
$_SESSION["csrf-token"] = $csrfToken;
?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php" ?>
<script src="public/assets/js/vote.js" defer></script>
<main class="default" id="vote">
    <div class="card vote">
    <form id="vote-form" method="POST" action="#">
        <div class="poll-header">
            <span class="title-large">
                <?php echo htmlspecialchars($poll->title); ?>
            </span>
            <span class="div-title">
                <?php echo htmlspecialchars($poll->question); ?>
            </span>
            <span class="helper">
                <?php echo htmlspecialchars($poll->description); ?>
            </span>
        </div>
        <div class="poll-body">
            <input type="hidden" id="csrf-token" name="csrf-token" value="<?php echo(($userVote) ? " " : $csrfToken); ?>">
            <div class="vote-poll-options">
                <?php
                if(!$userVote){
                    foreach($pollOptions as $option){
                        echo '<div class="vote-option"><label for="'.$option->id.'" >';
                        echo '<input id="'.$option->id.'" type="radio" name="radio" value="'.$option->id.'">';
                        echo htmlspecialchars($option->optionValue);
                        echo '</label></div>';
                    }
                } else {
                    foreach($pollOptions as $option){
                        echo '<div class="vote-option"><label for="'.$option->id.'" >';
                        if($option->id == $userVote){
                            echo '<input id="'.$option->id.'" type="radio" name="radio" value="'.$option->id.'" disabled checked>';
                        } else {
                            echo '<input id="'.$option->id.'" type="radio" name="radio" value="'.$option->id.'" disabled>';
                        }
                        echo htmlspecialchars($option->optionValue);
                        echo '</label></div>';
                    }     
                }
                ?>
            </div>
        </div>
        <div class="poll-footer">
            <span id="form-error" class="form-error-message">
                <?php if(isset($errmsg)){ echo $errmsg; }?>
            </span>
            <?php 
            if(isset($_SESSION['user-id']) && !$userVote){
                echo '<button type="submit" disabled id="vote-submit" data-state="disabled" class="btn bg-blue"><i class="fa-solid fa-envelope"></i>Send my vote</button>';
            } elseif(isset($_SESSION['user-id'])) {
                echo '<span class="bg-subtle" id="vote-casted">Your vote has been casted. Thank You! <a href="index.php"> Return to landing page.</a></span>';
            } else {
                echo '<a href="login.php?redirect=vote.php?pid='. $pid .'" id="vote-submit" class="btn bg-grey"><i class="fa-solid fa-lock"></i>Login Required</a>';
            }
            ?>
        </div>
    </form>  
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>