<?php

$path_to_auth =  dirname($_SERVER['DOCUMENT_ROOT']) . "/philadelphia-reflections-php_constants/authentication.php";

require_once($path_to_auth);

# load class definitions and connect to the database
# ==================================================
include("../inc/class_definitions.php");
include("../inc/pdo_database_connection.php");

# '$template_variables' is an assoc array passed to Twig template
# ===============================================================
$template_variables = array();

$copyright_end_year = date("Y");
$ip_addr	        = $_SERVER['REMOTE_ADDR'];
$template_variables['copyright_end_year'] = $copyright_end_year;
$template_variables['ip_addr']            = $ip_addr;

$limit_blog   = 10;
$limit_topic  = 10;
$limit_volume = 10;

# pull the blogs
# ==============
$select = "SELECT table_key, moddate, title, description, center_order
           FROM individual_reflections
           ORDER BY moddate DESC
           LIMIT 0 , $limit_blog";
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
           ORDER BY moddate DESC
           LIMIT 0 , $limit_topic";
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
           ORDER BY moddate DESC
           LIMIT 0 , $limit_volume";
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

# ====================================================

// find date of last comment

$select = "SELECT DATE_FORMAT(date, '%a %b %e, %l:%i %p') AS datef, DATEDIFF(CURDATE(),date) as nodays 
           FROM `blog_comments` 
           ORDER BY date DESC 
           LIMIT 1";
$stmt = $pdo->prepare($select);
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$rowcomment = $stmt->fetch(); # Array ( [datef] => Tue Sep 13, 1:52 AM [nodays] => 53 )

$datef  = $rowcomment["datef"];
$nodays = $rowcomment["nodays"];
if ($nodays == 0) {$nodays = "today";} elseif ($nodays == 1) {$nodays = "yesterday";} else {$nodays = $nodays . " days ago";}

$template_variables['nodays'] = $nodays;
$template_variables['datef']  = $datef;



# call the template
# =================
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('utilviews');
$twig   = new Twig_Environment($loader, array(
    // Uncomment the line below to cache compiled templates
    // 'cache' => '/../cache',
));

echo $twig->render('indextwig.twig', $template_variables);

exit;

?>
