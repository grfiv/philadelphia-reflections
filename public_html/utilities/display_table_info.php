<?php
/** @file
 *
 * Display the structure of a database table
 *
 */

# load class definitions and connect to the database
# ==================================================
include("../inc/class_definitions.php");
include("../inc/pdo_database_connection.php");

# '$template_variables' is an assoc array passed to the Twig template
# ===================================================================
$template_variables = array();

# find all the tables in the database
# ===================================
$sql = "SHOW TABLES";
$statement = $pdo->prepare($sql);
$statement->execute();
$tables = $statement->fetchAll(PDO::FETCH_NUM);

$template_variables['tables'] = $tables;

$template_variables['isset'] = false;

# if a table is selected, display its info
# ========================================
if (isset($_GET['table'])) {
    $template_variables['isset'] = true;

    $table = $_GET['table'];

    # get all the table's information
    # ===============================
    $decribe = "DESCRIBE $table";
    $stmt = $pdo->prepare($decribe);
    $stmt->execute();
    $table_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $template_variables['table']        = $table;
    $template_variables['table_fields'] = $table_fields;
    $template_variables['print_r']      = print_r($table_fields, true);
}

# call the template
# =================
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('utilviews');
$twig   = new Twig_Environment($loader, array(
    // Uncomment the line below to cache compiled templates
    // 'cache' => '/../cache',
));

echo $twig->render('display_table_info.twig', $template_variables);
?>