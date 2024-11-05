<?php
    session_start();

    if(isset($_SESSION['token']) && isset($_SESSION['user_id'])){
        echo "logged in with token: " . $_SESSION['token'] . " | " . $_SESSION['user_id'];
    } else {
        session_unset();
        session_destroy();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PollGate</title>
    <link rel="stylesheet" href="./public/assets/css/main.css">
    <link rel="icon" type="image/x-icon" href="./public/assets/img/favicon.ico">
</head>
<body>
    <?php include 'public/partials/header.php';?>

    <h1>Heading Level 1</h1>
    <p>This is a paragraph following an <strong>H1</strong> heading. It should be styled based on the body text size in the typography.css file.</p>

    <h2>Heading Level 2</h2>
    <p>This is a paragraph following an <strong>H2</strong> heading.</p>

    <h3>Heading Level 3</h3>
    <p>This is a paragraph following an <strong>H3</strong> heading.</p>

    <h4>Heading Level 4</h4>
    <p>This is a paragraph following an <strong>H4</strong> heading.</p>

    <h5>Heading Level 5</h5>
    <p>This is a paragraph following an <strong>H5</strong> heading.</p>

    <h6>Heading Level 6</h6>
    <p>This is a paragraph following an <strong>H6</strong> heading.</p>

    <hr>

    <h2>Paragraphs and Text Elements</h2>

    <p>This is a standard paragraph of text. It is styled with the base <code>font-size</code> defined in the typography.css file.</p>
    
    <p><small>This is small text, typically used for disclaimers or fine print. It is smaller than the base font size.</small></p>

    <blockquote>
        This is a blockquote. It should stand out with different font sizing, styling, and possibly an indentation. Blockquotes are used to highlight important excerpts.
    </blockquote>

    <hr>

    <h2>Lists</h2>

    <ul>
        <li>This is an unordered list item.</li>
        <li>Another unordered list item.</li>
        <li>Yet another unordered list item.</li>
    </ul>

    <ol>
        <li>This is an ordered list item.</li>
        <li>Another ordered list item.</li>
        <li>Yet another ordered list item.</li>
    </ol>
</body>
</html>