<?php
    function print_database_table($table, $pdo)
    {
        /** print the schema for a database table
         *
         * @param $table (string) the name of the table
         * @param $pdo (PDO::CONNECTION)
         * @returns (null) prints a table
         *
         */
        $decribe = "DESCRIBE $table";
        $stmt = $pdo->prepare($decribe);
        $stmt->execute();
        $table_fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo '
        <style type="text/css">
        table.db-table 		{ border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
            table.db-table th	{ background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
            table.db-table td	{ padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
        </style>' . "\n";

        echo "<h3>Database table name: $table</h3>\n";
        echo '<table cellpadding="0" cellspacing="0" class="db-table">' . "\n";
        echo '<tr>';
        foreach ($table_fields[1] as $key => $value) {
            echo '<th>' . $key . '</th>';
        }
        echo '</tr>' . "\n";

        foreach ($table_fields as $field) {
            echo '<tr>';
            foreach ($field as $key => $value) {
                echo '<td>', $value, '</td>';
            }
            echo '</tr>' . "\n";
        }
        echo '</table><br />' ."\n";
    }

    # '$template_variables' is an assoc array passed to the Twig template
    # ===================================================================
    $template_variables = array();

    # load class definitions and connect to the database
    # ==================================================
    include("../inc/class_definitions.php");
    include("../inc/pdo_database_connection.php");

    $template_variables['isset'] = false;

    if (isset($_POST['submit'])) {
        $template_variables['isset'] = true;
        $table_key = (int)$_POST['table_key'];


        # instantiate the blog as an object from the database
        # ===================================================
        $select = "SELECT * FROM individual_reflections WHERE table_key = ?";
        $stmt = $pdo->prepare($select);
        $stmt->execute(array($table_key));
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'blog');
        $blog = $stmt->fetch();

        $template_variables['blog'] = $blog;
        #print_database_table('individual_reflections', $pdo);


        # find all the topics pointing to this blog
        # =========================================
        #
        # first SELECT  three fields from topics found in
        # second SELECT   a list of topic keys
        #                   found in table 'topics_blogs'
        #                      with this blog's key
        $select = "SELECT title, description, table_key FROM topics
                                                    WHERE table_key IN
                                  (SELECT topic_key FROM topics_blogs
                                                    WHERE blog_key=?)";
        $stmt = $pdo->prepare($select);
        $stmt->execute(array($table_key));
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
        $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

        # clean up the topics
        # -------------------
        foreach ($topic_list as &$topic) {
            $topic->plain_title = trim(strip_tags($topic->title));
        }

        $template_variables['topic_list'] = $topic_list;


        }




    # call the template
    # =================
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('utilviews');
    $twig   = new Twig_Environment($loader, array(
        // Uncomment the line below to cache compiled templates
        // 'cache' => '/../cache',
    ));

    echo $twig->render('post.twig', $template_variables);
?>