<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if(!isset($_GET['q'])) {
        echo "{\"response:\":\"Please provide a query string via the q parameter.\"}";
        exit;
    }
    
    require "twitteroauth/autoload.php";
    require 'credentials.inc';

    use Abraham\TwitterOAuth\TwitterOAuth;
    
    $count = 0;
    
    $connection = new TwitterOAuth($client_token, $client_secret, $access_token, $access_token_secret);
    $content = $connection->get("account/verify_credentials");

    // $statuses = $connection->get("statuses/home_timeline", array("count" => 25, "exclude_replies" => true));
    $statuses = $connection->get("search/tweets", array("count" => 25, "q" => pg_escape_string($_GET['q']), "exclude_replies" => true));
    parseStatuses($statuses);
    
    $response = (Object)array();
    $response->status = 200;
    $response->count = $count;
    $response->message = $count . " replies sent to users.";
    
    echo json_encode($response);    
    
    function parseStatuses($statuses) {
        foreach($statuses->statuses as $tweet) {
            var_dump($tweet);
            $lang = $tweet->metadata->iso_language_code;
            $created = $tweet->created_at;
            $id = $tweet->id;
            $txt = $tweet->text;
            $src = $tweet->source;
            $user = $tweet->user->id;
            $username = $tweet->user->screen_name;
            $userloc = $tweet->user->location;
            $lat = ($tweet->coordinates) ? $tweet->coordinates->coordinates[0] : -99;
            $lng = ($tweet->coordinates) ? $tweet->coordinates->coordinates[1] : -99;
            // echo $lang . "\t" . $username . "\t" . $txt . "\n";
            reply2user($username, $id, $txt, $created, $lat, $lng);
        }
    }

    function reply2user($username, $id, $txt, $created, $lat, $lng) {
        global $connection, $count, $dbhost, $dbname, $dbuser, $dbpass;
        

        $dbconn = pg_connect("host=$dbhost port=5432 dbname=$dbname user=$dbuser password=$dbpass");

        // Make sure that we don't tweet people if we alreayd tweeted them (for a single tweet)
        // This checks to make sure that the tweetid does not already exist in the database
        $q = "SELECT count(*) FROM tweets where tweetid = ".$id;
        $res = pg_query($dbconn, $q);
        $row = pg_fetch_object($res);
        pg_free_result($res);

        if ($row->count == 0) {
            // Create a new base32 unique key
            $key = base_convert(time()+rand(),10,32);
            $query = "INSERT INTO tweets (id, username, tweetid, tweet, ts, lat, lng) VALUES('".$key."','".pg_escape_string($username)."',".$id.",'".pg_escape_string($txt)."','".$created."',".$lat.",".$lng.")";
            pg_query($dbconn, $query) or die(pg_last_error());
            
            $url = buildURL($key);
            $tweet = "@".$username." We noticed that you ... Would you mind answering a quick question for an academic study? ".$url;
            //echo $tweet;
            $statues = $connection->post("statuses/update", array("status" => $tweet));
            //var_dump($statues);
            $count++;
        }
    }
    
    function buildURL($key) {
        $url = "http://spatialdeviant.com/tc/?q=".$key;
        //$end = "u=".$username."&id=".$id."&t=".urlencode($txt)."&c=".strtotime($created);
        return $url;
    }
    function url(){
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    // create table tweets (id varchar(32), username varchar(500), tweetid bigint, tweet varchar(1000), ts timestamp with time zone);
?>