<?php
function email_validate($email)
{
  // returns TRUE if the email address passed in is
  //   (a) syntactically valid
  //   (b) an mx record is found for the host/domain name
  if (is_null($email) || $email == '') return FALSE;
  $email = strtolower(trim($email));

  if (!preg_match('/[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@((?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)/', $email, $matches))
    return FALSE;

  $hostname = $matches[1];

  if ($hostname == 'hotmail.com') return TRUE;
  if ($hostname == 'yahoo.com') return TRUE;
  if ($hostname == 'gmail.com') return TRUE;
  if ($hostname == 'aol.com') return TRUE;
  if ($hostname == 'comcast.net') return TRUE;
  if ($hostname == 'verizon.net') return TRUE;
  if ($hostname == 'earthlink.net') return TRUE;
  if ($hostname == 'msn.com') return TRUE;
  if ($hostname == 'worldnet.att.net') return TRUE;
  if ($hostname == 'att.net') return TRUE;

  $result = getmxrr($hostname, $mxHosts);

  if ($mxHosts[0] == '0.0.0.0' || count($mxHosts) < 1 || $result == FALSE) return FALSE;

  return TRUE;
}


?>
