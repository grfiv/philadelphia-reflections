function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,""); }
/* http://www.w3schools.com/js/js_form_validation.asp */
function validateForm()
{
var x=document.forms["comment_form"]["name"].value;
if (x==null || x=="")
  {
  alert("Please fill in your name");
  return false;
  }
var x=trim(document.forms["comment_form"]["email"].value);
if (x==null || x=="")
  {
  alert("Please enter an email address");
  return false;
  }
var atpos=x.indexOf("@");
var dotpos=x.lastIndexOf(".");
if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
  {
  alert("Please enter a valid email address");
  return false;
  }
var x=document.forms["comment_form"]["comment"].value;
if (x==null || x=="")
  {
  alert("Please enter a comment");
  return false;
  }
}
