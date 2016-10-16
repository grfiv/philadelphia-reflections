<?php
    # pull in the database constants and open a PDO connection

    # see https://phpdelusions.net/pdo
    #     ============================

    include("secure/db-constants.php");
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE . ';charset='. DB_CHARSET;
    $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
           ];
    $pdo = new PDO($dsn, DB_USER, DB_PSWD, $opt);
?>
