<?php

	// This is called via redirect in index.php whenever an unrecognized mobile device is detected
	//
	// Sources of information about designing for handhelds
	//         http://www.ready.mobi/
	//         http://www.w3.org/TR/mobileOK-basic10-tests/
	//         http://www.opera.com/mini/demo/

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    $keys   = [484,485,1155,1101,565]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, table_key FROM individual_reflections WHERE table_key IN ($qmarks)";

    $stmt = $pdo->prepare($select);
    $stmt->execute($keys);
    $blog_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'blog');

    $access_key = 1;

    foreach ($blog_list as &$blog) {
        $title       = $blog->title;
        $table_key   = $blog->table_key;
        $description = $blog->description;
        $description = preg_replace('/<img .*?>/sim', '', $description); // strip out images
        $description = preg_replace('/ class="firstDrop"/si', '', $description);
        $description = preg_replace('/<!-- .*? -->/si', '', $description);
        $description = preg_replace('%<table [^>]*?>.*?</table>%si', '', $description);
        $description = preg_replace('/<img [^>]*?>/i', '', $description);

        $blog->description = utf8_encode(trim($description));
        $blog->access_key  = $access_key;

        $access_key = $access_key + 1;
    }

    $template_variables['blog_list'] = $blog_list;

    # call the template
    # =================
    require_once '../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('indexhandheld.twig', $template_variables);

?>