<?php
/** @file
  * Test if the call came from a mobile device and redirect appropriately
  *
  *     - http://detectmobilebrowsers.mobi/download/
  *     - http://mobiledetect.net/
  * both look promising but didn't work in my testing
  *
  * @todo Find a suitable mobile-device redirect facility
  */


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
    function manual_mobile_redirect($redirect_url) {
        # Ubuntu 16.04 chrome
        # Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36

        # iOS 10 iPhone 6 safari
        # Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1

        # iOS  10 iPhone 6 chrome
        # Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/53.0.2785.109 Mobile/14A403 Safari/601.1.46

        # citation: https://stackoverflow.com/questions/186734/how-do-i-detect-mobile-safari-server-side-using-php
        $browserAsString = $_SERVER['HTTP_USER_AGENT'];
        if (strstr($browserAsString, " AppleWebKit/") && strstr($browserAsString, " Mobile/")) {
        redirect("$redirect_url");
        }
    }
?>
