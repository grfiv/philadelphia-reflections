<?php

#
# I tried to rewrite this code and failed
# Plus, Google complains that I'm using V2 and I need to upgrade to V3
#

# so for the PDO rewrite I substituted this minimal stub
# until I, if ever, get around to doing a thorough job

$template_variables['geo_lat']             = $geo_article->geo_lat;
$template_variables['geo_lon']             = $geo_article->geo_lon;
$template_variables['geo_country_code']    = $geo_article->geo_country_code;
$template_variables['geo_region']          = $geo_article->geo_region;
$template_variables['geo_placename_strip'] = strip_tags($geo_article->geo_placename);
$template_variables['geo_placename']       = $blog->geo_placename;
$template_variables['geo_address']         = $blog->geo_address;
?>
