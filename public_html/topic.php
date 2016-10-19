<?php
/** @file
  * PHP code to generate HTML output of a topic
  *
  * The basic call is topic.php$key=### but .htaccess contains a
  * rewrite of the form ^topics?/([0-9]+)\.html?$ topic.php?key=$1
  * which allows
  *     - /topic/###.htm
  *     - /topics/###.htm
  *     - /topic/###.html
  *     - /topics/###.html
  *
  * This routine checks that the $_GET variable is valid and then pulls
  * the topic, all associated blogs & volumes and comments and ends with a call
  * to the Twig templage generator, a file topic.twig
  *
  * @param numeric $_GET['key'] with the table_key of the topic to display
  *
  * @return call to $twig->render
  */



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
    $select = "SELECT MIN(table_key) as min, MAX(table_key) as max FROM topics";
    $stmt = $pdo->prepare($select);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($table_key < $result['min'] || $table_key > $result['max']) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }

    # =================================== #
    # test if called from a mobile device #
    # =================================== #

    # '$template_variables' is an assoc array passed to the Twig template
    # ===================================================================
    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    # instantiate the topic as an object from the database
    # ====================================================
    $select = "SELECT * FROM topics WHERE table_key = ?";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
    $topic = $stmt->fetch();

    # clean up the topic
    # -----------------
    $topic->plain_title       = trim(strip_tags($topic->title));
    $topic->plain_description = trim(strip_tags($topic->description));
    $topic->topic_contents    = trim($topic->topic_contents);

    $template_variables['topic'] = $topic;

    # find all the volumes pointing to this topic
    # ===========================================
    #
    # first SELECT  three fields from volumes found in
    # second SELECT   a list of volume keys
    #                   found in table 'volumes_topics'
    #                      with this topic's key
    $select = "SELECT title, description, table_key FROM volumes
                                                    WHERE table_key IN
                                 (SELECT volume_key FROM volumes_topics
                                                    WHERE topic_key=?)";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
    $volume_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

    # clean up the volumes
    # --------------------
    foreach ($volume_list as &$volume) {
        $volume->plain_title = trim(strip_tags($volume->title));
    }

    $template_variables['volume_list'] = $volume_list;

    # find all the blogs pointed to by this topic
    # ===========================================
    $select = "SELECT title, description, blog_contents, table_key FROM individual_reflections
                                                                   WHERE table_key IN
                                                  (SELECT blog_key FROM topics_blogs
                                                                   WHERE topic_key=?
                                                                   ORDER BY blog_order ASC)";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'blog');
    $blog_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'blog');

    # clean up the blogs
    # ------------------
    foreach ($blog_list as &$blog) {
        $blog->plain_title   = trim(strip_tags($blog->title));
        $blog->blog_contents = trim($blog->blog_contents);
    }

    $template_variables['blog_list'] = $blog_list;

    # retrieve the comments
    # =====================
    $select = "SELECT * FROM blog_comments
                        WHERE type='topic'
                          AND blog_key=$topic->table_key
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
    $template_variables['comment_type'] = 'topic';
    $template_variables['comment_key']  = $topic->table_key;

    # setup geotags and map
    # =====================
    # see insertMapCode.php
    # the rewrite will entail a lot of work
    # both intrinsically and because Google has moved beyond V2
    # the STUB is a placeholder until the day the full rewrite happens
    $geotags = false;
    if ($topic->geo_lat != NULL and
        $topic->geo_lon != NULL and
        $topic->geo_lat != 0    and
        $topic->geo_lon != 0) {

        $geotags     = true;
        $geo_article = $topic;
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

    echo $twig->render('topic.twig', $template_variables);
?>
