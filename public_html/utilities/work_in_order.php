<?php
/** @file
 *
 * Display blogs, topics and volumes in moddate order
 *
 */
# load class definitions and connect to the database
# ==================================================
include("../inc/class_definitions.php");
include("../inc/pdo_database_connection.php");

# '$template_variables' is an assoc array passed to the Twig template
# ===================================================================
$template_variables = array();

$limit = 250; # how far to go back?

# pull the blogs
# ==============
$select = "SELECT table_key, moddate, title, description, center_order
           FROM individual_reflections
           ORDER BY moddate DESC";
           #LIMIT 0 , $limit";
$stmt = $pdo->prepare($select);
$stmt->execute(array());
$stmt->setFetchMode(PDO::FETCH_CLASS, 'blog');
$blog_list = $stmt->fetchAll();

# format the date, find invisibility
# ----------------------------------
foreach ($blog_list as &$blog) {
    $dt = new DateTime($blog->moddate);
    $blog->moddate_fmt = date_format($dt, "D M d, Y");

    if ($blog->center_order == 0) {
        $blog->invisible = "<br><span style='color:red;font-weight:bold;'>INVISIBLE</span>";
    }
}

$template_variables['blog_list'] = $blog_list;

# pull the topics
# ===============
$select = "SELECT table_key, moddate, title, description, center_order
           FROM topics
           ORDER BY moddate DESC";
           #LIMIT 0 , $limit";
$stmt = $pdo->prepare($select);
$stmt->execute(array());
$stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
$topic_list = $stmt->fetchAll();

# format the date, find invisibility
# ----------------------------------
foreach ($topic_list as &$topic) {
    $dt = new DateTime($topic->moddate);
    $topic->moddate_fmt = date_format($dt, "D M d, Y");

    if ($topic->center_order == 0) {
        $topic->invisible = "<br><span style='color:red;font-weight:bold;'>INVISIBLE</span>";
    }
}

$template_variables['topic_list'] = $topic_list;

# pull the volumes
# ================
$select = "SELECT table_key, moddate, title, description, center_order
           FROM volumes
           ORDER BY moddate DESC";
           #LIMIT 0 , $limit";
$stmt = $pdo->prepare($select);
$stmt->execute(array());
$stmt->setFetchMode(PDO::FETCH_CLASS, 'volume');
$volume_list = $stmt->fetchAll();

# format the date, find invisibility
# ----------------------------------
foreach ($volume_list as &$volume) {
    $dt = new DateTime($volume->moddate);
    $volume->moddate_fmt = date_format($dt, "D M d, Y");

    if ($volume->center_order == 0) {
        $volume->invisible = "<br><span style='color:red;font-weight:bold;'>INVISIBLE</span>";
    }
}

$template_variables['volume_list'] = $volume_list;

# call the template
# =================
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('utilviews');
$twig   = new Twig_Environment($loader, array(
    // Uncomment the line below to cache compiled templates
    // 'cache' => '/../cache',
));

echo $twig->render('work_in_order.twig', $template_variables);


?>