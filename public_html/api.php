<?php
/** @file
 *
 * Send a blog, topic or volume as json
 *
 */

$json_msg = array();
$json_msg['status']  = 'error';
$json_msg['message'] = "correct format is api.php?type=[blog|topic|volume]&key=[numeric key of desired article]; or api.php?list=[blogs|topics|volumes]";

# test if request is properly formed
# ==================================
if (isset($_GET['type']) &&  isset($_GET['key'])) {
    $type      = strtolower ( $_GET['type']);
    $table_key = (int) $_GET['key'];
    $call_type = "GET";

} elseif (isset($_POST['type']) &&  isset($_POST['key'])) {
    $type = strtolower($_POST['type']);
    $table_key = (int)$_POST['key'];
    $call_type = "POST";

} elseif (isset($_GET['list'])) {
    $list = strtolower ( $_GET['list']);
    $call_type = "GET";

} elseif (isset($_POST['list'])) {
    $list = strtolower ( $_POST['list']);
    $call_type = "POST";


} else {
    header('Content-type: application/json');
    $json_msg['error_type'] = 'request incorrectly formatted';
    echo json_encode($json_msg);
    exit;
}

# validate request type
# =====================
if (isset($type)) {
    switch ($type) {
        case "blog":
            $db_table = "individual_reflections";
            break;
        case "topic":
            $db_table = "topics";
            break;
        case "volume":
            $db_table = "volumes";
            break;
        default:
            header('Content-type: application/json');
            $json_msg['error_type'] = 'incorrect type';
            echo json_encode($json_msg);
            exit;
    }
} elseif (isset($list)) {
    switch ($list) {
        case "blogs":
            $db_table = "individual_reflections";
            break;
        case "topics":
            $db_table = "topics";
            break;
        case "volumes":
            $db_table = "volumes";
            break;
        default:
            $json_msg['error_type'] = 'incorrect list';
            header('Content-type: application/json');
            echo json_encode($json_msg);
            exit;
    }
} else {
    $json_msg['error_type'] = 'incorrect type/list';
    header('Content-type: application/json');
    echo json_encode($json_msg);
    exit;
}

# load class definitions and connect to the database
# ==================================================
include("inc/class_definitions.php");
include("inc/pdo_database_connection.php");

# request was for a specific blog/topic/volume
# ============================================
if (isset($type)) {
    # check that the key we were sent is within bounds
    # ================================================
    $select = "SELECT MIN(table_key) as min, MAX(table_key) as max FROM $db_table";
    $stmt = $pdo->prepare($select);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($table_key < $result['min'] || $table_key > $result['max']) {
        header('Content-type: application/json');
        $json_msg['error_type'] = "$table_key is an incorrect key for type=$type";
        echo json_encode($json_msg);
        exit;
    }

    # instantiate the articls as an object from the database
    # ======================================================
    $select = "SELECT * FROM $db_table WHERE table_key = ?";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array($table_key));
    $stmt->setFetchMode(PDO::FETCH_CLASS, $type);
    $article = $stmt->fetch();

    # check if row with table_key exists
    # ==================================
    if( ! $article) {
        header('Content-type: application/json');
        $json_msg['error_type'] = "$table_key is an incorrect key for type=$type";
        echo json_encode($json_msg);
        exit;
    }

    # clean up the article
    # --------------------
    $article->plain_title       = trim(strip_tags($article->title));
    $article->plain_description = trim(strip_tags($article->description));

    $json_msg['status']  = 'success';
    $json_msg['message'] = "$type $table_key returned";
    $json_msg['article'] = $article;

    # find all the articles higher/lower in the hierarchy from this one
    # =================================================================
    switch ($type) {
        case "blog":
            $select = "SELECT title, table_key FROM topics
                                                    WHERE table_key IN
                                  (SELECT topic_key FROM topics_blogs
                                                    WHERE blog_key=?)";
            $stmt = $pdo->prepare($select);
            $stmt->execute(array($table_key));
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
            $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

            $json_msg['topic_list'] = $topic_list;
            break;

        case "topic":
            # find all the volumes pointing to this topic
            # ===========================================
            $select = "SELECT title, table_key FROM volumes
                                                    WHERE table_key IN
                                 (SELECT volume_key FROM volumes_topics
                                                    WHERE topic_key=?)";
            $stmt = $pdo->prepare($select);
            $stmt->execute(array($table_key));
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'volume');
            $volume_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'volume');

            $json_msg['volume_list'] = $volume_list;

            # find all the blogs pointed to by this topic
            # ===========================================
            $select = "SELECT title, table_key FROM individual_reflections
                                                                   WHERE table_key IN
                                                  (SELECT blog_key FROM topics_blogs
                                                                   WHERE topic_key=?
                                                                   ORDER BY blog_order ASC)";
            $stmt = $pdo->prepare($select);
            $stmt->execute(array($table_key));
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'blog');
            $blog_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'blog');

            $json_msg['blog_list'] = $blog_list;
            break;

        case "volume":
            # find all the topics pointed to by this volume
            # ==============================================
            $select = "SELECT title, table_key FROM  topics
                                                    WHERE table_key IN
                                  (SELECT topic_key FROM  volumes_topics
                                                    WHERE volume_key=?
                                                    ORDER BY topic_order ASC)";
            $stmt = $pdo->prepare($select);
            $stmt->execute(array($table_key));
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'topic');
            $topic_list = $stmt->fetchAll(PDO::FETCH_CLASS, 'topic');

            $json_msg['topic_list'] = $topic_list;
            break;
    }

    # send out the payload
    # ====================
    header('Content-type: application/json');
    echo json_encode($json_msg);
    exit;

# request was for the list of all bolgs/topics/volumes
# ====================================================
} elseif (isset($list)) {
    $type = substr($list, 0, -1);

    # pull all the table_keys and titles for the class of article requested
    # =====================================================================
    $select = "SELECT title, table_key FROM $db_table";
    $stmt = $pdo->prepare($select);
    $stmt->execute(array());
    $stmt->setFetchMode(PDO::FETCH_CLASS, $type);
    $keys_titles = $stmt->fetchAll(PDO::FETCH_CLASS, $type);

    $json_msg['status']  = 'success';
    $json_msg['message'] = "titles & table_keys for list=$list returned";
    $json_msg[$type."_list"] = $keys_titles;

    header('Content-type: application/json');
    echo json_encode($json_msg);
    exit;

} else {
    header('Content-type: application/json');
    $json_msg['error_type'] = "unknown error";
    echo json_encode($json_msg);
    exit;

}

?>
