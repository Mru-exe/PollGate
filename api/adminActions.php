<?php
require_once __DIR__ . "/../src/common.php";
continueSession(true, "login.php", true);
require_once __DIR__ . "/../src/dbcontext.php";

$action = $_GET["action"] ?? null;
$userId = $_GET["user-id"] ?? null;

if($action == null || $userId == null){
    header("Location: /~kindlma7/PollGate/admin.php");
    exit();
} else {
    if($action == "promote"){
        try {
            $user = $db->getUserById($userId);
            $user->roleId = 2;
            $db->updateUser($user);
        } catch (PDOException $e) {
            die($e);
        }
        header("Location: /~kindlma7/PollGate/admin.php");
        exit();
    }
    if($action == "delete"){
        try {
            $user = $db->getUserById($userId);
            $user->roleId = 2;
            $db->deleteUser($user);
        } catch (PDOException $e) {
            die($e);
        }
        header("Location: /~kindlma7/PollGate/admin.php");
        exit();
    }
}
