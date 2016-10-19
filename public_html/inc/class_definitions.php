<?php
/** @file
  * skeleton classes representing the basic data structures
  *
  * all the data for these structures is contained in MySQL database
  * tables and the PDO::FETCH_CLASS call instantiates the specified
  * class.
  *
  * @todo at a later time the skeleton classes may be fleshed out with methods
  * that could trigger actions (such as returning all of the blogs associated
  * with a topic).
  *
  */


    /**
     * blog instantiated via PDO::FETCH_OBJ or PDO::FETCH_CLASS
     */
    class blog    {public $obj_type = "blog";};

    /**
     * topic instantiated via PDO::FETCH_OBJ or PDO::FETCH_CLASS
     */
    class topic   {public $obj_type = "topic";};

    /**
     * volume instantiated via PDO::FETCH_OBJ or PDO::FETCH_CLASS
     */
    class volume  {public $obj_type = "volume";};

    /**
     * comment instantiated via PDO::FETCH_OBJ or PDO::FETCH_CLASS
     */
    class comment {public $obj_type = "comment";};
?>
