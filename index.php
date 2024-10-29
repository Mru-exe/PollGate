<?php
// Define a helper function to load views
function renderView($viewName) {
    echo($viewName);
    // $viewFile = __DIR__ . "/public/{$viewName}.php";
    // if (file_exists($viewFile)) {
    //     include $viewFile;
    // } else {
    //     include __DIR__ . "/public/404.php";
    // }
}

// Parse the requested path
$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/~kindlma7/PollGate/', '', $request);
$request = strlen($request) == 0 ? 'login' : $request;

renderView($request);

?>
