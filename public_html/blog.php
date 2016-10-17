<?php
    # this routine is called by
    # blog.php?key=####
    if (!isset($_GET['key'])) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }
    echo "<h1>passes</h1>";
    $key = $_GET['key'];


?>