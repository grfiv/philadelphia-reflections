<?php
    # this routine is called by
    # blog.php?key=####
    #

    # check that we were sent a key
    # =============================
    if (!isset($_GET['key'])) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    $table_key = (integer) $_GET['key']; // casting a non-integer should generate zero

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

    # ===============================================
    # ======> test for mobile-device redirect <======
    # ===============================================

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
    $blog->blog_contents     = trim($blog->blog_contents);

    $template_variables['blog'] = $blog;

    # find all the topics pointing to this blog
    # =========================================
    $select = "SELECT topic_key FROM topics_blogs WHERE blog_key=?";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_NUM);
    $topic_keys = array();
    while($result = $stmt->fetch()){
        $topic_keys[] = $result[0];
    }

    $topic_list = NULL;
    if ($topic_keys) {
        $qmarks = join(',', array_fill(0, count($topic_keys), '?'));
        $select = "SELECT title, description, table_key FROM topics WHERE table_key IN ($qmarks)";
        $stmt = $pdo->prepare($select);
        $stmt->execute($topic_keys);
        $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');
    }

    # clean up the topics
    # -------------------
    foreach ($topic_list as &$topic) {
        $topic->plain_title = trim(strip_tags($topic->title));
    }

    $template_variables['topic_list'] = $topic_list;

    # retrieve the comments
    # =====================
    $select = "SELECT * FROM blog_comments
                        WHERE type='blog'
                          AND blog_key=$blog->table_key
                          AND confirmed='yes'
						ORDER BY date DESC";
    $stmt = $pdo->prepare($select);
    $stmt->execute();
    $comment_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'comment');

    # clean up the comments
    # ---------------------
    foreach ($comment_list as &$comment) {
        $comment->fmt_date = date("M j, Y  g:i A",strtotime ($comment->date));
    }

    $template_variables['comment_list'] = $comment_list;
    $template_variables['comment_type'] = 'blog';
    $template_variables['comment_key']  = $blog->table_key;

    # setup geotags and map
    # =====================
    # see insertMapCode.php
    # the rewrite will entail a lot of work
    # both intrinsically and because Google has moved beyond V2
    $geotags = false;
    if ($blog->geo_lat != NULL and
        $blog->geo_lon != NULL and
        $blog->geo_lat != 0    and
        $blog->geo_lon != 0) {

        $geotags     = true;
        $geo_article = $blog;
        include('inc/insertMapCode_STUB.php');
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

    echo $twig->render('blog.twig', $template_variables);
?>