<?php
define('PAGE_TITLE', 'PollGate | New Poll');
require_once "src/common.php";
continueSession(true, "new.php", false);

require_once "src/dbcontext.php";

$errmsg = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST["csrf-token"]) || $_POST["csrf-token"] !== $_SESSION["csrf-token"]) {
        $errmsg["form-error"] = "Invalid CSRF Token";
    }

    // Validate form fields
    $title = trim($_POST["title"]);
    empty($title) ? $errmsg["title-error"] = "Title is required." : $errmsg["title-error"] = "";

    $question = trim($_POST["question"]);
    empty($question) ? $errmsg["question-error"] = "Question is required." : $errmsg["question-error"] = "";

    $description = trim($_POST["description"]);
    empty($description) ? $errmsg["description-error"] = "Description is required." : $errmsg["description-error"] = "";

    $options = $_POST["option"];
    if (empty($options) || count($options) < 3) {
        $errmsg["option-error"] = "Answer options are required.";
    }

    if (!isset($errmsg) || empty(array_filter($errmsg))) {
        try {
            $poll = new Poll(0, $title, $description, $question, "single", 0, "now", $_SESSION['user-id'], "");
            $pollId = $db->insertPoll($poll);
            if($pollId > 0){
                $optionsObj = [];
                foreach ($options as $option) {
                    $optionsObj[] = new PollOption(0, $pollId, $option);
                }
                $db->insertPollOptions($optionsObj);
                header("Location: /~kindlma7/PollGate/index.php");
                exit();
            }
        } catch (\Throwable $th) {
            die(var_dump($th->getMessage()));
        }
    }
}


//CREATE CSRF TOKEN
$csrfToken = bin2hex(random_bytes(16));
$_SESSION["csrf-token"] = $csrfToken;
?>

<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php" ?>
<script src="public/assets/js/new.js" defer></script>
<main id="new" class="default">
<div class="card container-flex-col">
    <span class="title-large">New Poll</span>
    <form class="default" method="POST" action="#" id="new-form">
        <div>
            <span class="div-title">Poll Definition</span>
            <span id="form-error" class="form-error-message">
                    <?php echo isset($errmsg["form-error"]) ? $errmsg["form-error"] : ""; ?>
                </span>
            <label for="title">*Title:
                <input type="text" id="title" name="title" placeholder="Enter poll title" value="<?php echo isset($_POST["title"]) ? $_POST["title"] : "" ?>" required>
                <span id="title-error" class="form-error-message">
                    <?php echo isset($errmsg["title-error"]) ? $errmsg["title-error"] : ""; ?>
                </span>
            </label>

            <label for="question">*Question:
                <input type="text" id="question" name="question" placeholder="Enter poll question" value="<?php echo isset($_POST["question"]) ? $_POST["question"] : "" ?>" required>
                <span id="question-error" class="form-error-message">
                    <?php echo isset($errmsg["question-error"]) ? $errmsg["question-error"] : ""; ?>
                </span>
            </label>

            <label for="description">*Description:
                <textarea rows="3" id="description" name="description" placeholder="Enter poll description" required><?php echo isset($_POST["description"]) ? $_POST["description"] : "" ?></textarea>
                <span id="description-error" class="form-error-message">
                    <?php echo isset($errmsg["description-error"]) ? $errmsg["description-error"] : ""; ?>
                </span>
            </label>

            <input type="hidden" id="csrf-token" name="csrf-token" value="<?php echo $csrfToken; ?>">
        </div>
        <div>
            <span class="div-title ">
                Poll Options
                <!-- <button type="button" id="new-option" class="btn bg-blue"><i class="fa-solid fa-plus"></i></button> -->
            </span>
            <div id="option-list">
                <span id="option-error" class="form-error-message">
                    <?php echo isset($errmsg["options-error"]) ? $errmsg["options-error"] : ""; ?>
                </span>
                <div class="option-wrapper">
                    <input type="text" id="opt1" name="option[]" placeholder="A: option 1" required>
                </div>
                <div class="option-wrapper">
                    <input type="text" id="opt2" name="option[]" placeholder="B: option 2" required>
                </div>
                <div class="option-wrapper">
                    <input type="text" id="opt3" name="option[]" placeholder="C: option 3" required>
                </div>
                <!-- TODO(v2): Do pozdější verze přidělat dynamic list  -->
            </div>
        </div>
        <button type="submit" class="btn bg-blue"><i class="fa-solid fa-plus"></i> Create new Poll</button>
    </form> 
</div>
</main>
<?php require_once "public/partials/footer.php"; ?>