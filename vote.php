<?php
include_once 'src/models/Poll.php';
include_once 'src/config/_dbcontext.php';
include_once 'src/functions/auth.php';

session_start();

if (isset($_SESSION['user_token']) && isset($_COOKIE['user_token'])) {
    if (hash_equals($_SESSION['user_token'], $_COOKIE['user_token'])) {
        // Session is valid
    } else {
        // Token mismatch: force to relogfin
        logoutUser();
    }
} else {
    // Missing token: froce to relogin
    logoutUser();
}

if(isset($_GET["pollId"])){
    $pollId = intval($_GET["pollId"]);
} else {
    returnNotFound();
}


$errorState = '';

function returnNotFound(){
    require 'public/partials/pollNotFound.php';
    echo "not found";
}

function getPollById(PDO $pdo, int $id){
    $query = "SELECT * FROM vPolls WHERE Id = :pollId";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(":pollId", $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $res = $stmt->fetch();
        return new Poll($res);
    } catch (PDOException $e) {
        echo $e->getMessage();
        return 0;
    }

    return 0;
}

$_poll = getPollById($conn, $pollId);
if(empty($_poll)){
    returnNotFound();
    exit();
}


