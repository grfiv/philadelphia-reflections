<?php
    $nBlogs = $pdo->query('SELECT COUNT(*) FROM individual_reflections')->fetchColumn();
?>
<header class=middle_header>
                  <p style="margin:0;padding:0;font-size:125%;"><span style="font-weight:bold">Philadelphia Reflections</span> is a history of the area around Philadelphia, PA
                     ... William Penn's Quaker Colonies
                     <br />&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-style:italic;'>plus medicine, economics and politics</span> ... <?php echo $nBlogs ?> articles in all <br><br>
                     Try the search box to the left if you don't see what you're looking for.</p>
                </header>
