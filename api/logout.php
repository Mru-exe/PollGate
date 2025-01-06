<?php
//LOGOUT API
require_once __DIR__ . "/../src/common.php";
continueSession(false, "", false);
logout();
?>