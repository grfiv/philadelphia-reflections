<?php
/** @file
  * Create a PDO connection to the MySQL database
  *
  * The secure/ directory is excluded via .gitignore and .htaccess; it contains the
  * database credentials.
  *
  * PDO::FETCH_OBJ is set as the default because the object
  * instantiation feature is so convenient, but PDO::FETCH_CLASS is more-often used
  * to specify which class to instantiate.
  *
  * see https://phpdelusions.net/pdo for the documentation I used to get going
  * with PDO
  */

    include($_SERVER["DOCUMENT_ROOT"] . "/secure/db-constants.php");
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_DATABASE . ';charset='. DB_CHARSET;
    $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
           ];
    $pdo = new PDO($dsn, DB_USER, DB_PSWD, $opt);
?>
