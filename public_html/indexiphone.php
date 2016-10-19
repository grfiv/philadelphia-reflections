<?php
/** @file
  * PHP code to generate iPhone output of the main page
  *
  * This is called via a mobile-device redirect function in index.php
  */

    // This is called via redirect in index.php whenever an iPhone is detected
    //
    // Sources of information about designing for handhelds
    //         http://www.ready.mobi/
    //         http://www.w3.org/TR/mobileOK-basic10-tests/
    //         http://www.opera.com/mini/demo/
    //
    // See: Safari Web Content Guide for iPhone OS

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

    $template_variables = array();

    $copyright_end_year = date("Y");
    $template_variables['copyright_end_year'] = $copyright_end_year;

    function imgReSize($url, $largest_side)
        {

        //
        // this function figures out how to proportionately downsize an image
        //
        // it takes in: the URL of the image and the longest dimension
        // it returns:  an array:  $imgDimensions["width"], ["height"]
        //

        //
        // 1. find the attributes of an image
        //

        $pointer = $url;
        # if the image is local, look locally instead of across the Internet
        #$pointer    = preg_replace("/^http:[\/]{2}www.philadelphia-reflections.com\/images/i", "$_SERVER['DOCUMENT_ROOT']" . "/images", $url);
        #if ($pointer != $url) {$pointer    = preg_replace("/%20/", " ", $pointer);}

        $getimagesize                = getimagesize($pointer);
        list($old_width, $old_height, $type, $attr)    = $getimagesize;
    #    $bits                        = $getimagesize['bits'];
    #    $channels                    = $getimagesize['channels'];
    #    $mime                        = $getimagesize['mime'];

        //
        // 2. figure out the proportional reduction
        //    to make the image no larger than $largest_side
        //    on its longer side

        $new_width  = $old_width;
        $new_height = $old_height;

        if  ($old_width >= $old_height) {
            if ($old_width > $largest_side) {
                $new_width = $largest_side;
                $percentage_reduction = ($new_width / $old_width) * 100;
                $new_height = round(($old_height / 100) * $percentage_reduction);
                }
            }
        else {
            if ($old_height > $largest_side) {
                $new_height = $largest_side;
                $percentage_reduction = ($new_height / $old_height) * 100;
                $new_width = round(($old_width / 100) * $percentage_reduction);
                }
            }

        //
        // 3. output the dimensions in an array
        //

        $imgDimensions["width"]  = $new_width;
        $imgDimensions["height"] = $new_height;
        return $imgDimensions;
        }

    // .............   Select the blogs to display  ...............................

    $keys   = [484,485,1155,1101,565]; # array(...);
    $qmarks = join(',', array_fill(0, count($keys), '?'));
    $select = "SELECT title, description, table_key FROM individual_reflections WHERE table_key IN ($qmarks)";

    $stmt   = $pdo->prepare($select);
    $stmt->execute($keys);
    $blog_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'blog');

    // .............   Build one table per blog   .................................

    foreach ($blog_list as &$blog) {
        $imgTag      = NULL;
        $title       = $blog->title;
        $table_key   = $blog->table_key;
        $description = $blog->description;

        if (preg_match('/<img .*?src="([^"]*)"[^>]*>/si', $description, $matches)) {
            $imgDimensions = imgReSize($matches[1], 100);
            $newWidth      = $imgDimensions["width"];
            $newHeight     = $imgDimensions["height"];

            $imgTag        = "<img src='$matches[1]' width='$newWidth' height='$newHeight' alt='{##}' />";
            $description   = trim(preg_replace('/<img .*?[^>]*?>/si', '', $description));
        }

        if (strlen($description) > 100 ) $description = substr($description, 0, 100) . " ...";

        $blog->description = $description;
        $blog->imgTag      = $imgTag;
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

    echo $twig->render('indexiphone.twig', $template_variables);
?>
