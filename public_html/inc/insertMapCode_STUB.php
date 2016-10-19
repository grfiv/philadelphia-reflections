<?php
/** @file
  * Stub of a PHP file that created a Google map in blogs based on geo tags
  *
  * This is a stub because I attempted a rewrite and failed. It's a nice feature
  * but it will take more work than I am currently willing to invest, compounded by
  * the fact that Google complains that I'm using V2 and I need to upgrade to V3.
  *
  * @todo Fix Google Map code
  */

$template_variables['geo_lat']             = $geo_article->geo_lat;
$template_variables['geo_lon']             = $geo_article->geo_lon;
$template_variables['geo_country_code']    = $geo_article->geo_country_code;
$template_variables['geo_region']          = $geo_article->geo_region;
$template_variables['geo_placename_strip'] = strip_tags($geo_article->geo_placename);
$template_variables['geo_placename']       = $blog->geo_placename;
$template_variables['geo_address']         = $blog->geo_address;
?>
