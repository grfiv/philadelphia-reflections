<?php
/** @file
  * PHP code to generate HTML output of a volume
  *
  * The basic call is volume.php$key=### but .htaccess contains a
  * rewrite of the form ^volumes?/([0-9]+)\.html?$ volume.php?key=$1
  * which allows
  *     - /volume/###.htm
  *     - /volumes/###.htm
  *     - /volume/###.html
  *     - /volumes/###.html
  *
  * This routine checks that the $_GET variable is valid and then pulls
  * the volume, all associated topics and comments and ends with a call
  * to the Twig templage generator, a file volume.twig
  *
  * @param numeric $_GET['key'] with the table_key of the volume to display
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
    $select = "SELECT MIN(table_key) as min, MAX(table_key) as max FROM volumes";
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

    # instantiate the volume as an object from the database
    # ====================================================
    $select = "SELECT * FROM volumes WHERE table_key = ?";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'volume');
    $volume = $stmt->fetch();

    # check if row with table_key exists
    # ==================================
    if( ! $volume) {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit;
    }

    # clean up the volume
    # -----------------
    $volume->plain_title       = trim(strip_tags($volume->title));
    $volume->plain_description = trim(strip_tags($volume->description));
    $volume->volume_contents    = trim($volume->volume_contents);

    if ($volume->picture == NULL) $volume->picture = "penn_hospital.jpg";

    #$pointer       = $_SERVER['DOCUMENT_ROOT'].'/images/' . $volume->picture;
    $pointer        = "http://www.philadelphia-reflections.com/images/" . $volume->picture;
    $pointer        = preg_replace("/%20/", " ", $pointer);
    $getimagesize   = getimagesize($pointer);
    list($picture_width, $picture_height, $type, $attr) = $getimagesize;
    #
    # The width of the box is 980 pixels
    # If the width of the picture is greater than 48% of that, we constrain both the img and the text to 48%, each
    # If the width of the picture is less than 48% of 980, the img gets its full width as a % and the text gets 98% minus that
    #
    $img_pct        = $picture_width / 980;

    if ($img_pct > 0.48)
        {
        $img_pct    = "48%";
        $txt_pct    = "48%";
        }
    else
        {
        $img_pct    = $img_pct * 100;
        $img_pct    = (integer) $img_pct;

        $txt_pct    = 98 - $img_pct;

        $img_pct    = $img_pct . "%";
        $txt_pct    = $txt_pct . "%";
        }

    $template_variables['volume']  = $volume;
    $template_variables['img_pct'] = $img_pct;
    $template_variables['txt_pct'] = $txt_pct;

    # find all the topics pointed to by this volume
    # ==============================================
    $select = "SELECT title, description, table_key FROM  topics
                                                    WHERE table_key IN
                                  (SELECT topic_key FROM  volumes_topics
                                                    WHERE volume_key=?
                                                    ORDER BY topic_order ASC)";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
    $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

    # clean up the topics
    # -------------------
    foreach ($topic_list as &$topic) {
        $topic->plain_title   = trim(strip_tags($topic->title));
    }

    $template_variables['topic_list'] = $topic_list;

    // make sure that all references to images
    // are absolute references, not relative

    #$html       =      str_replace("src=\"images","src=\"http://www.philadelphia-reflections.com/images",$html);
    #$html       =      str_replace("src=\"../images","src=\"http://www.philadelphia-reflections.com/images",$html);


    # retrieve the comments
    # =====================
    $select = "SELECT * FROM blog_comments
                            WHERE type='volume'
                              AND blog_key=$volume->table_key
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
    $template_variables['comment_type'] = 'volume';
    $template_variables['comment_key']  = $volume->table_key;

    # setup geotags and map
    # =====================
    # see insertMapCode.php
    # the rewrite will entail a lot of work
    # both intrinsically and because Google has moved beyond V2
    # the STUB is a placeholder until the day the full rewrite happens
    $geotags = false;
    if ($volume->geo_lat != NULL and
        $volume->geo_lon != NULL and
        $volume->geo_lat != 0    and
        $volume->geo_lon != 0) {

        $geotags     = true;
        $geo_article = $volume;
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

    echo $twig->render('volume.twig', $template_variables);
?>
