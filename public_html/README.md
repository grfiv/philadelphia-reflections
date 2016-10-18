# Internet-accessible files

### Example of PDO and template engine use:

```index.php```, called from the Internet, contains PHP code that retrieves data from the MySQL database using PDO to instantiate objects; the last step invokes the ```twig->render``` method to call the template

```views/index.twig``` is the Twig template containing the HTML to be rendered after variable substitution from an array passed in from ```index.php```   

--------------------------------

### The database structure is a linked hierarchy:   

* ```blog```s constitute articles, they are the main content,   
  contained in the table ```individual_reflections```  
  
* ```topic```s are collections of ```blog```s   
  contained in table ```topics```, they are connected to the ```blog```s they contain via the table ```topics_blogs```   
  
* ```volume```s are collections of ```topic```s   
contained in table ```volumes```, they are connected to the ```topic```s they contain via the table ```volumes_topics```   

There is also a facility for readers to leave comments which are in table ```comments```; newsletter subscription information is in table ```email_legit```.

-----------------------------

Dr. Fisher wrote and published several books using this system. A ```volume``` was the book and ```topic```s were the chapters with ```blog```s being chapter sections. A macro was written to download a ```volume``` and all of its constituents into Microsoft Word, adding a TOC and modifying the pictures, for final editing.
