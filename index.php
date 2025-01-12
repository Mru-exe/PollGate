<?php
define('PAGE_TITLE', 'PollGate | Home');
require_once "src/common.php";
continueSession(false, "", false);
require_once "src/dbcontext.php";

//Get recently created polls
$recentPolls = $db->getPolls(3, 0, "1=1", "created DESC");
//Get user polls
$userId = isset($_SESSION["user-id"]) ? $_SESSION["user-id"] : 0;
if($userId > 0){
    $user = $db->getUserById($userId);
    $stats = $db->getUserStats($userId);
}

//TODO: EDIT PROFILE
//TODO: USER STATS

?>


<!--HTML STARTS HERE-->
<?php require_once "public/partials/header.php" ?>
<script src="public/assets/js/searchAjax.js" defer></script>
<main class="default" id="index">
    <div class="card" id="recent-polls">
        <span class="div-title">Recently published polls</span>
            <?php
            foreach($recentPolls as $poll){
                echo '<div class="poll-wrapper">';
                echo '<span class="title">' .htmlspecialchars($poll->title).'</span>';
                echo '<span class="helper-spaced"> Created ' . date_format($poll->created, "m/d/Y") . ' by ' . htmlspecialchars($poll->createdByUsername) . '</span>';
                echo '<span class="poll-preview">' . shortenString($poll->question, "50") . '</span>';
                echo '<span><a class="btn bg-blue" href="vote.php?pid='.$poll->id.'"><i class="fa-solid fa-arrow-up-right-from-square"></i>View</a></span>';
                echo '</div>';
            }   
            ?>
    </div>
    <div class="card" id="search-polls">
        <span class="div-title">Search</span>
        <div id="search-bar-wrapper">
            <input id="search-bar" class="search" placeholder="Fulltext search..." type="text">
            <div id="search-btn" class="btn"><i class="btn fa-lg fa-solid fa-magnifying-glass"></i></div>
            <span class="helper"></span>
        </div>
        <div id="search-results"></div>
        <div class="pagination-wrapper">
            <a id="search-pgn-prev" data-state="disabled" class="btn bg-subtle"><i class="fa-solid fa-arrow-left"></i></a>
            <span id="pgn-n">

            </span>
            <a id="search-pgn-next" data-state="disabled" class="btn bg-subtle"><i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
    <div class="card" id="user-about">
        <span class="div-title">My Profile</span>
        <?php
        if($userId > 0) {
            echo '<div class="user-profile">';
            echo '<img class="avatar" alt="User Avatar" height="64" width="64" src="public/assets/avatars/2x@' . $_SESSION["avatar-token"] . '">';
            echo '<span>' . htmlspecialchars($user->username) . '</span><span class="helper">Joined ' . date_format($user->created, "m/d/Y") . '</span>';
            echo '<div class="user-actions"><a href="edit-profile.php" class="btn bg-grey"><i class="fa-solid fa-user-pen"></i> Edit Profile</a><a href="new.php" class="btn bg-blue"><i class="fa-solid fa-plus"></i> Create New Poll</a></div>';
            echo '</div><div class="user-stats">';
            echo '<span class="title">Personal statistics: </span>';
            echo '<span>You created: ' . $stats['polls'] . ' polls</span>';
            echo '<span>You voted: ' . $stats['votes'] . ' times</span>';
            echo '</div><div class="user-stats">';
            echo '<span class="title">Repository statistics: </span>';
            //TODO: napojit na LOC API
            echo '<span>1249* lines of PHP</span>';
            echo '<span>583* lines of JavaScript</span>';
            echo '<span>264* lines of CSS</span>';
            echo '<span class="helper">*without comments</span>';
            echo '</div>';
        } else {
            echo '<div class="container-flex-col blocker">';
            echo '<h4>Nothing to display...</h4>';
            echo '<a class="silent btn bg-blue btn-bloat" href="/~kindlma7/PollGate/login.php"><i class="fa-solid fa-key"></i> Login</a>';
            echo '</div>';
        }
        ?>
    </div>
</main>

<?php require_once "public/partials/footer.php"; ?>