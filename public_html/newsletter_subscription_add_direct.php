<?php
/** @file
  * Add a new subscriber to the newsletter
  *
  * Originally, newsletter_subscription_add.php would send a
  * confirming email but the ISP has essentially shut down the email
  * sending facility; I suppose because it has been abused but it has
  * forced this rewrite.
  *
  *
  * @todo find an email-sending facility
  */

# validate the email

    $email       = $_POST['email'];
    include("inc/email_validate.php");
    $valid_email = email_validate($email);
    if (!$valid_email)
    {
      echo "please enter a valid email address";
      exit;
    }

# Get variables from $_POST  for insertion into the database

    foreach ($_POST as $key => $value) $$key = $value;

# import the class definitions and connect to the database

    include("inc/class_definitions.php");
    include("inc/pdo_database_connection.php");

# test if the email already exists

    $test_query    = "SELECT * FROM email_legit WHERE email=?";
    $stmt = $pdo->prepare($test_query);
    $stmt->execute(array($email));
    $test_num_rows = count($stmt->fetchAll());
    if ($test_num_rows > 0)
    {
      echo "$email is already on our database";
      exit;
    }

# INSERT the CONFIRMED email address in the database

    $insert_query  = "INSERT INTO email_legit
                      SET first_name=:fname, last_name=:lname, email=:email, confirmed='yes'";
    $stmt = $pdo->prepare($insert_query);
    $stmt->execute(array('fname' => $fname, 'lname' => $lname, 'email' => $email));

# call the template

    $template_variables             = array();
    $template_variables['fname']    = $fname;
    $template_variables['lname']    = $lname;
    $template_variables['email']    = $email;

    require_once '../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('views');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('newsletter_subscription_add_direct.twig', $template_variables);
?>
