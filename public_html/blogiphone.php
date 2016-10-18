<?php
    # this routine is called by
    # blogiphone.php?key=####
    #

    # check that we were sent a key
    # =============================
    if (!isset($_GET['key'])) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }

    $table_key = (integer) $_GET['key']; // casting a non-integer should generate zero

    # load class definitions and connect to the database
    # ==================================================
    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    # check that the key we were sent is within bounds
    # ================================================
    $select = "SELECT MIN(table_key) as min, MAX(table_key) as max FROM individual_reflections";
    $stmt = $pdo->prepare($select);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($table_key < $result['min'] || $table_key > $result['max']) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }

    # '$template_variables' is an assoc array passed to the Twig template
    # ===================================================================
    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    # instantiate the blog as an object from the database
    # ===================================================
    $select = "SELECT * FROM individual_reflections WHERE table_key = ?";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'blog');
    $blog = $stmt->fetch();

    # clean up the blog
    # -----------------
    $blog->plain_title       = trim(strip_tags($blog->title));
    $blog->plain_description = trim(strip_tags($blog->description));

    $blog->blog_contents = preg_replace('/ class="firstDrop"/si', '', $blog->blog_contents);
    $blog->blog_contents = preg_replace('%<a href="[^>]*?>([^>]*?(?=<))</a>%si', '$1', $blog->blog_contents);

    $blog->blog_contents = trim($blog->blog_contents);

    $template_variables['blog'] = $blog;

    # setup geotags and map
    # =====================
    # see insertMapCode.php
    # the rewrite will entail a lot of work
    # both intrinsically and because Google has moved beyond V2
    # the STUB is a placeholder until the day the full rewrite happens
    $geotags = false;
    if ($blog->geo_lat != NULL and
        $blog->geo_lon != NULL and
        $blog->geo_lat != 0    and
        $blog->geo_lon != 0) {

        $geotags     = true;
        $geo_article = $blog;
        include('inc/insertMapCode_STUB.php');

        $blog->geo_placename_strip = NULL;
        $blog->geo_placename_strip = strip_tags($blog->geo_placename);
        $blog->geo_placename_strip = str_replace(" " , "+", $blog->geo_placename_strip);
        $template_variables['geo_placename_strip'] = $blog->geo_placename_strip;
    }

    $template_variables['geotags'] = $geotags;


    # call the template
    # =================
    require_once '../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('blogiphone.twig', $template_variables);

?>

