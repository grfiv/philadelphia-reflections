# Internet-accessible files

### Example of PDO and template engine use:

```index.php```, called from the Internet, contains PHP code that retrieves data from the MySQL database using PDO to instantiate objects; the last step invokes the ```twig->render``` method to call the template

```views/index.twig``` is the Twig template containing the HTML to be rendered after variable substitution from an array passed in from ```index.php```
