<?php
    # http://detectmobilebrowsers.mobi/download/
    # http://mobiledetect.net/
    # both look promising but didn't work in my testing

    function redirect($url, $statusCode = 303) {
        # redirect to another URL

        # send nothing to the browser prior to
        # calling this function

        # citation: https://stackoverflow.com/questions/768431/how-to-make-a-redirect-in-php
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    # Ubuntu 16.04 chrome
    # Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36

    # iOS 10 iPhone 6 safari
    # Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1

    # iOS  10 iPhone 6 chrome
    # Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/53.0.2785.109 Mobile/14A403 Safari/601.1.46

    # citation: https://stackoverflow.com/questions/186734/how-do-i-detect-mobile-safari-server-side-using-php
    $browserAsString = $_SERVER['HTTP_USER_AGENT'];
    if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/")) {
        redirect('http://www.philadelphia-reflections.com/indexiphone-PDO.php');
    }

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    # variables for the left column ... topics
    # ========================================
    $nTopics = $pdo->query('SELECT COUNT(*) FROM topics')->fetchColumn();

    $keys   = [6,13,56,140,98,129]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, table_key FROM topics WHERE table_key IN ($qmarks)";

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
    $select = "SELECT title, description, TRIM(blog_contents) AS blog_contents, table_key FROM individual_reflections WHERE table_key IN ($qmarks)";

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
    $select = "SELECT title, description, table_key FROM volumes WHERE table_key IN ($qmarks)";

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
