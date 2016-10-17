<?php
    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    # call the template
    # =================
    require_once '../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('404.twig', $template_variables);
?>