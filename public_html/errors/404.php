<?php
    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;
    $template_variables['referring_page']     = $_SERVER['REQUEST_URI'];
    #phpinfo();exit;

    # call the template
    # =================
    require_once (dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php');
    $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('404.twig', $template_variables);
?>