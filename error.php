<?php
require_once "src/common.php";
continueSession(false, "index.php", false);

if(isset($_GET["code"])) {
    $errCode = $_GET["code"];
    if(is_string($errCode)) {
        $errCode = strtolower($errCode);
    }
} else {
    $errCode = "400";
}

define('PAGE_TITLE', 'Error...');

$errCodes = [
    "400" => 'Bad Request',
    "401" => 'Unauthorized',
    "403" => 'Forbidden',
    "404" => 'Not Found',
    "500" => 'Internal Server Error',
    "502" => 'Bad Gateway',
    "503" => 'Service Unavailable',
    "504" => 'Gateway Timeout',
    // Custom error codes
    "leaf" => 'Database connection could not be established',
];
$errMsg = $errCodes[$errCode] ?? "Unexpected error";

?>

<?php require_once "public/partials/header.php"; ?>
<main class="default">
    <div class="error-container">
        <span class="error-msg"><?php echo $errMsg; ?></span>
        <span class="error-code">CODE <?php echo $errCode; ?></span>

        <?php if($errCode[0] != "4"){
            echo '<span class="error-disclaimer">Administrator has been notified, please try again later...</span>';
        } 
        
        echo error_get_last();
        if(isset($_GET['msg'])){
            echo '<span class="helper">Additional information:</span><p>';
            echo $_GET['msg'] . '</p><br>';
        }
        ?>  
        <a class="bold" href="index.php">Return to landing page</a>
    </div>
</main>
<?php require_once "public/partials/footer.php"; ?>