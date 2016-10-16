<?php

	// This is called via redirect in index.php whenever an unrecognized mobile device is detected
	//
	// Sources of information about designing for handhelds
	//         http://www.ready.mobi/
	//         http://www.w3.org/TR/mobileOK-basic10-tests/
	//         http://www.opera.com/mini/demo/

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");
?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">

        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
        <link rel="icon"          href="images/favicon.ico" type="image/x-icon" />

        <!--                                      -->
        <!-- PHILADELPHIA REFLECTIONS             -->
        <!-- George R. Fisher III MD              -->
        <!--                                      -->
        <!-- (c) 2004 - <?php echo date("Y"); ?>                      -->
        <!--                                      -->

        <title>Philadelphia Reflections</title>

        <style>
            body {color:#000;background-color:#FFF;font-size:medium;margin:0;padding:0;}
            #top > p           {font-family:garamond, times, serif;background-color:#22007D;color:#ea9f20;text-transform:uppercase;margin:0;padding:5px;}
            #wrapper{width:100%;margin:0;padding:0;}#content{font-size:medium;color:#000;background-color:#FFF;margin:0;padding:10px;}
            #content > p{text-align:left;}
            .footerhandheld{font-family:garamond, times, serif;background-color:#22007D;}
            .footerhandheld > p{color:#ea9f20;font-weight:700;text-align:center;margin:0 auto;padding:25px 10px;}
            .links{font-size:2em;}.copyright{font-size:.90em;font-weight:400;color:#FFF;}#top,#center{margin:0;padding:0;}
            .home,.email{color:#ea9f20;}
        </style>
    </head>

    <body>

      <div id="top">

        <p>Philadelphia Reflections</p>

      </div>

      <div id="content">
          <?php
            $keys   = [484,485,1155,1101,565]; # array(...);
            $qmarks = join(',', array_fill(0, count($keys), '?'));
            $select = "SELECT * FROM individual_reflections WHERE table_key IN ($qmarks)";

            $stmt = $pdo->prepare($select);
            $stmt->execute($keys);

            $access_key = 1;

            while ($blog = $stmt->fetchObject('blog')) {
                $title       = $blog->title;
                $table_key   = $blog->table_key;
                $description = $blog->description;
                $description = preg_replace('/<img .*?>/sim', '', $description); // strip out images
                $description = preg_replace('/ class="firstDrop"/si', '', $description);
                $description = preg_replace('/<!-- .*? -->/si', '', $description);
                $description = preg_replace('%<table [^>]*?>.*?</table>%si', '', $description);
                $description = preg_replace('/<img [^>]*?>/i', '', $description);

                // access keys suggested by http://www.ready.mobi
                echo "<p><a accesskey='$access_key' href='$filename'>$title</a><br>\n";
                $access_key = $access_key + 1;

                echo utf8_encode(trim($description)) . "</p>\n\n";
            };
    ?>

      </div>

      <div class='footerhandheld'>
        <p>
            <a class='home' href='http://www.philadelphia-reflections.com'>HOME</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a class='email' href='mailto:grfisheriii@gmail.com?subject=Message%20from%20a%20mobile%20device'>EMAIL</a><br>
            <span style='color:white';>Copyright Dr. George Fisher 2004 - <?php echo date("Y"); ?>. All rights reserved.</span>
        </p>
      </div>

    </body>
</html>
