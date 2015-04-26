<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if(!isset($_GET['q'])) {
        echo "Please provide a query string via the \"q\" parameter.";
        exit;
    }
    $key = pg_escape_string($_GET['q']);
    
    $dbconn = pg_connect("host=localhost port=5432 dbname=carsitweets user=carsitweets password=carsitweets");
    $query = "SELECT * FROM tweets WHERE id = '".$key."'";
  
    $results = pg_query($dbconn, $query) or die(pg_last_error());
?>
<!DOCTYPE html>
<html>
<head>
	<title>CARSI Lab: Twitter Survey</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='http://fonts.googleapis.com/css?family=Noto+Sans' rel='stylesheet' type='text/css'>
        <link href='css/main.css' rel='stylesheet' type='text/css'>
</head>
<body>
    <div id="wrapper">
        <div style="float:left;width:136px"><img src="http://www.carsilab.org/images/carsilogo.gif"/></div>
        <div style="float:left;width:164px"><h2>CARSI</h2><span style='font-size:12px;'>Center for Advanced Research of Spatial Information</span></div>
        
        <div style="clear:both;">Welcome to the x project.  You recently published the following tweet<br/><br/></div>
        <div id="tweet">
<?php
    $row = pg_fetch_object($results);
    echo $row->tweet . "<br/>";
    $time = strtotime($row->ts);
    echo "<div style='float:right;font-size:10px;'>".date('H:i M d',$time)."</div>";
?>
        </div>
        <p>How would you classify the topic of this tweet?</p>
        <select>
            <option>Select a topic</option>
            <option>Personal</option>
            <option>Work / School</option>
            <option>Social / Recreational</option>
            <option>Shopping</option>
            <option>Other</option>
        </select>
        
        <div class="button">Submit</div>
        <div style="float:right;font-size:11px;"><a href="http://www.carsilab.org/" target="_blank">More information on this project</a></div>
    </div>
</body>
</html>