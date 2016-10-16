<?php

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
?>
<!DOCTYPE html>

<html lang="en">
  <head>
    <meta name="viewport"                              content="width=device-width, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable"          content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <title>Philadelphia Reflections</title>

    <link rel="apple-touch-icon" href="images/apple-touch-icon.png" type="image/png">
    <link rel="shortcut icon"    href="images/favicon.ico"          type="image/x-icon">
    <link rel="icon"             href="images/favicon.ico"          type="image/x-icon">

    <link rel="stylesheet"       href="css/indexiphone.css">

    <style>

    </style>
  </head>

  <body onload="setTimeout(function() { window.scrollTo(0, 1) }, 100);">

    <div id="top">
      <p>Philadelphia Reflections</p>
    </div>

    <div id="content">

<?php

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
$select = "SELECT * FROM individual_reflections WHERE table_key IN ($qmarks)";

$stmt   = $pdo->prepare($select);
$stmt->execute($keys);

// .............   Build one table per blog   .................................

while ($blog = $stmt->fetchObject('blog')) {
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

  echo "<table>\n  <tr>\n    <td class='image'>$imgTag</td>\n    <td class='desc'><a href='http://www.philadelphia-reflections.com/reflectionsiphone.php?type=blog&amp;key=$table_key'>\n      <span class='title'>$title</span><br />\n      <span class='descr'>$description</span></a></td>\n    <td class='arrow'><a href='http://www.philadelphia-reflections.com/reflectionsiphone.php?type=blog&amp;key=$table_key'>\n      <img src='images/right-arrow-1.png' alt='{iphone arrow}' width='30' height='30' /></a>\n    </td>\n  </tr>\n</table>\n\n";
  }
?>
    </div>

    <div id="footerhandheld">
      <p>
        <span class="links">
          <a class="home" href="http://www.philadelphia-reflections.com">HOME</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
          <a class="email" href="mailto:grfisheriii@gmail.com?subject=Message%20from%20an%20iPhone%20visitor">EMAIL</a></span><br />
        <span class="copyright">Copyright Dr. George Fisher 2004 - <?php echo date("Y"); ?><br />All rights reserved</span>
      </p>
    </div>

  </body>
</html>
