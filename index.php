<?php
// index.php

// Define a helper function to load views
function renderView($viewName) {
    $viewFile = __DIR__ . "/public/{$viewName}.html";
    if (file_exists($viewFile)) {
        include $viewFile;
    } else {
        include __DIR__ . "/public/404.php";
    }
}

// Parse the requested path
$request = $_GET['page'] ?? 'home';

renderView($request);

// // Handle routing based on the request
// switch ($request) {
//     case 'home':
//         renderView('home');
//         break;
//     case 'login':
//         renderView('about');
//         break;
//     case 'contact':
//         renderView('contact');
//         break;
//     default:
//         renderView('404');  // Show 404 page for undefined routes
//         break;
// }