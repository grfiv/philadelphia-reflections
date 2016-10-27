<?php
/** @file
  * Add a comment to the database
  *
  * Originally, comment_insert.php would send a
  * confirming email but the ISP has essentially shut down the email
  * sending facility; I suppose because it has been abused but it has
  * forced this rewrite.
  *
  * @todo find an email-sending facility
  */

# First we check that the POSTed input is valid and acceptable
# ============================================================

// retrieve the POSTed variables
foreach ($_POST as $key => $value) $$key = $value;

// validate the input (in case the javascript sieve was bypassed)
if (is_null($name) || $name == '')
{
  echo "Please fill in your name";
  exit;
}
if (is_null($email) || $email == '')
{
  echo "Please enter an email address";
  exit;
}

include("inc/email_validate.php");
$valid_email = email_validate($email);
if (!$valid_email)
{
  echo "please enter a valid email address";
  exit;
}
if (is_null($comment) || $comment == '')
{
  echo "Please enter a comment";
  exit;
}
$ip_addr = $_SERVER['REMOTE_ADDR'];

// check for known bad actors
include("inc/bad_actor_function.php");
if (bad_ip($ip_addr)) {
    echo "you appear to be a known spammer";
    exit;
}

// additional check for bad words
$whole_string = $name . $email . $comment;
if (preg_match('/fuck|cunt|porn|penis|horny|prick|pussy|damn|asshole|sex|boob|shit/i', $whole_string))
{
  echo "Please, no profanity";
  exit;
}
if (preg_match('/buy.*?(?:online|cheap)|purchase|seo|SEO|Hello!|levitra|generic|underage|prescription|pay[ |-]*day|zoloft|prozac|clomid|prilosec|zyrt|tamox|zithro|zovir|neuront|cipro|motrin|flomax|cymbalta|breast|effex|casino|kasino|Pills|powder|valium|drug|locksmith|Lotter|EuroMillion|pharma|Preteen|viagra|Loli|nude|nymph|cock|erotic|anus|milf|pedo|hentai/i', $whole_string))
{
  echo "because spammers use certain words and phrases we have to block all comments that use them";
  exit;
}

// check that the blog/topic/volume key passed in is valid
switch ($type)
{
    case "blog":
        $table = "individual_reflections";
        break;
    case "topic":
        $table = "topics";
        break;
    case "volume":
        $table = "volumes";
        break;
    default:
        echo "error: invalid type";
        exit;
}

if (!is_numeric($key)) exit;
$key = (int)$key;

if (preg_match('%(?<!href=[\'|"]|src=[\'|"])(https?://([-\w.]+)+(:\d+)?(/([\w/_.]*(\?\S+)?)?)?)%', $comment) ||
    preg_match('/<a[ ]*?.*href/i', $comment))
{
    echo "because spammers use links extensively we cannot permit them in comments";
    exit;
}

if (!$hostname  = @gethostbyaddr($ip_addr)) $hostname = "failed: $ip_addr";

include("inc/class_definitions.php");
include("inc/pdo_database_connection.php");

$select = "SELECT COUNT(*) AS total FROM $table WHERE table_key=?";
$stmt = $pdo->prepare($select);
$stmt->execute(array($key));
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result['total'] == 0) {echo "error: key not found"; exit;}


# convert emails to links
# .......................
# the last suggestion on the page: http://www.regular-expressions.info/email.html
$comment = preg_replace('/([a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b)/i', '<a href="mailto:$1">$1</a>', $comment);

$date = date('Y/m/d G:i:s');

# Get the article title
# =====================
$select = "SELECT title FROM $table WHERE table_key=?";
$stmt = $pdo->prepare($select);
$stmt->execute(array($key));
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$template_variables             = array();
$template_variables['title']    = $result['title'];
$template_variables['name']     = $name;
$template_variables['comment']  = $comment;



# Next we we save a confirmed comment to the database (used to do email confirmation but it's failing)
# ====================================================================================================

$insert = "INSERT INTO blog_comments (date, name, email, comments, ipaddr, hostname, type, blog_key, confirmed)
                               VALUES(?,    ?,    ?,     ?,        ?,      ?,        ?,    ?,        ?)";
$stmt = $pdo->prepare($insert);
$stmt->execute(array($date, $name, $email, $comment, $ip_addr, $hostname, $type, $key, 'yes'));



# call the template
# =================
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('views');
$twig   = new Twig_Environment($loader, array(
    // Uncomment the line below to cache compiled templates
    // 'cache' => '/../cache',
));

echo $twig->render('comment_insert_direct.twig', $template_variables);

?>
