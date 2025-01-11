<?php
//search API
require_once __DIR__ . "/../src/dbcontext.php";
session_start();

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["query"])){
    $query = trim($_GET["query"]);
    $offset = isset($_GET["offset"]) ? $_GET["offset"] : 0;
    $polls = $db->searchPolls($query, 3, $offset);
    
    // var_dump($polls[0]['count']);
    $response = ["logged"=>isset($_SESSION["user-id"]), "count"=>$polls['count'], "results"=>$polls['polls']];
    echo json_encode($response);
} else {
    echo "Invalid request";
}