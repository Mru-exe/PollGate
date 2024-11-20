<?php
require_once 'src/config/_dbcontext.php';
require_once 'src/auth.php';
require_once 'src/models/Poll.php';

session_start();

// if (isset($_SESSION['user_token']) && isset($_COOKIE['user_token'])) {
//     if (hash_equals($_SESSION['user_token'], $_COOKIE['user_token'])) {
//         // Session is valid
//     } else {
//         // Token mismatch: force to relogfin
//         logoutUser();
//     }
// } else {
//     // Missing token: froce to relogin
//     logoutUser();
// }

$_poll = null;

if(isset($_GET["pollId"])){
    $pollId = intval($_GET["pollId"]);
    echo $pollId;
    $_poll = getPollById($conn, $pollId);
} else {
    echo "pollId not specified";
    // returnNotFound();
} 

$errorState = '';

function returnNotFound(){
    require 'public/partials/pollNotFound.php';
    echo "not found";
}


echo gettype($_poll);
// if(empty($_poll)){
//     returnNotFound();
//     exit();
// }


