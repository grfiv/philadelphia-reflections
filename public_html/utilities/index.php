<?php

# load class definitions and connect to the database
# ==================================================
include("../inc/class_definitions.php");
include("../inc/pdo_database_connection.php");

# Call the authorization module
# =============================
$path_to_auth =  dirname($_SERVER['DOCUMENT_ROOT']) .
    "/philadelphia-reflections-php_constants/authenticationPDO.php";

require_once($path_to_auth);


# '$template_variables' is an assoc array passed to Twig template
# ===============================================================
$template_variables = array();

$copyright_end_year = date("Y");
$ip_addr	        = $_SERVER['REMOTE_ADDR'];
$template_variables['copyright_end_year'] = $copyright_end_year;
$template_variables['ip_addr']            = $ip_addr;

$limit_blog       = 5;
$limit_topic      = 5;
$limit_volume     = 5;
$limit_collection = 5;

# pull the blogs
# ==============
$nBlogs = $pdo->query('SELECT COUNT(*) FROM individual_reflections')->fetchColumn();

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

$template_variables['nBlogs']    = $nBlogs;
$template_variables['blog_list'] = $blog_list;

# pull the topics
# ===============
$nTopics = $pdo->query('SELECT COUNT(*) FROM topics')->fetchColumn();

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

$template_variables['nTopics']    = $nTopics;
$template_variables['topic_list'] = $topic_list;

# pull the volumes
# ================
$nVolumes = $pdo->query('SELECT COUNT(*) FROM volumes')->fetchColumn();

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

$template_variables['nVolumes']    = $nVolumes;
$template_variables['volume_list'] = $volume_list;

# pull the collections
# ====================
$nCollections = $pdo->query('SELECT COUNT(*) FROM collections')->fetchColumn();

$select = "SELECT table_key, moddate, title, description, center_order
           FROM collections
           ORDER BY moddate DESC
           LIMIT 0 , $limit_collection";
$stmt = $pdo->prepare($select);
$stmt->execute(array());
$stmt->setFetchMode(PDO::FETCH_CLASS, 'volume');
$collection_list = $stmt->fetchAll();

# format the date, find invisibility
# ----------------------------------
foreach ($collection_list as &$collection) {
    $dt = new DateTime($collection->moddate);
    $collection->moddate_fmt = date_format($dt, "D M d, Y");

    if ($collection->center_order == 0) {
        $collection->invisible = "<br><span style='color:red;font-weight:bold;'>INVISIBLE</span>";
    }
}

$template_variables['nCollections']    = $nCollections;
$template_variables['collection_list'] = $collection_list;

$template_variables['nTotal'] = $nBlogs + $nTopics + $nVolumes + $nCollections;

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
