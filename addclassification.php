<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "credentials.inc";
    
    if(!isset($_GET['key']) || !isset($_GET['class'])) {
        echo "Please provide KEY and CLASS parameters";
        exit;
    }
    $key = pg_escape_string($_GET['key']);
    $class = pg_escape_string($_GET['class']);
    
    $dbconn = pg_connect("host=localhost port=5432 dbname=$db user=$user password=$pass");
    $query = "UPDATE tweets SET class = '".$class."' WHERE id = '".$key."' RETURNING *";
  
    $results = pg_query($dbconn, $query) or die(pg_last_error());
    $row = pg_fetch_object($results);
    if ($row->id = $key)
        echo json_encode((Object)array("status"=>200,"message"=>"success"));
    else
        echo json_encode((Object)array("status"=>500,"message"=>"error"));
?>