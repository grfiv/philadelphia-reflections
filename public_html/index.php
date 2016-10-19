<?php
/** @file
  * PHP code to generate HTML output of the main page of the system
  *
  *
  * @todo mobile device redirect
  */


    # test if called from a mobile device
    # ===================================
    include("inc/manual_mobile_redirect.php");
    manual_mobile_redirect("http://www.philadelphia-reflections.com/indexiphone.php");

    # load empty class definitions and make database connection
    # =========================================================
    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    # '$template_variables' is an assoc array passed to Twig template
    # ===============================================================
    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    # variables for the left column ... topics
    # ========================================
    $nTopics = $pdo->query('SELECT COUNT(*) FROM topics')->fetchColumn();

    $keys   = [6,13,56,140,98,129]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, table_key FROM  topics
                                                    WHERE table_key IN ($qmarks)";

    $stmt = $pdo->prepare($select);
    $stmt->execute($keys);
    $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

    $template_variables['nTopics']    = $nTopics;
    $template_variables['topic_list'] = $topic_list;

    # variables for the middle column ... blogs
    # =========================================
    $nBlogs = $pdo->query('SELECT COUNT(*) FROM individual_reflections')->fetchColumn();

    $keys   = [484,485,1155,1101,565]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, TRIM(blog_contents) AS blog_contents, table_key
               FROM  individual_reflections
               WHERE table_key IN ($qmarks)";

    $stmt = $pdo->prepare($select);
    $stmt->execute($keys);
    $blog_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'blog');

    $template_variables['nBlogs']    = $nBlogs;
    $template_variables['blog_list'] = $blog_list;

    # variables for right column ... volumes
    # ======================================
    $nVolumes = $pdo->query('SELECT COUNT(*) FROM volumes')->fetchColumn();

    $keys   = [18,52,15,42,10]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, table_key FROM  volumes
                                                    WHERE table_key IN ($qmarks)";

    $stmt = $pdo->prepare($select);
    $stmt->execute($keys);
    $volume_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'volume');

    $template_variables['nVolumes']    = $nVolumes;
    $template_variables['volume_list'] = $volume_list;

    # call the template
    # =================
    require_once '../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('index.twig', $template_variables);
?>
