<?php
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true); 

echo $data["name"];

?>