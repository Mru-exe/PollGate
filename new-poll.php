<?php
include_once 'src/models/Poll.php';
include_once 'src/config/_dbcontext.php';

if(empty($_GET["pollId"]) || gettype($_GET["pollId"]) != "integer"){
    echo "no param provided";
    exit();
}
$pollId = $_GET["pollId"];

$errorState = '';

function getPollById(PDO $pdo, int $id){
    $query = "SELECT Name, Description, Question, PollTypeCode, Created, CreatedBy, Modified, ModifiedBy FROM Polls WHERE DELETED = 0 AND Id = :pollId";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(":pollId", $id, PDO::PARAM_STR);

    try {
        $stmt->execute();
        $res = $stmt->fetch();
        return $res;
    } catch (PDOException $e) {
        echo $e->getMessage();
        return 0;
    }

    return 0;
}

$_poll = getPollById($conn, $pollId);
if(empty($_poll)){
    echo "neex";
}


