<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require "credentials.inc";
    
    if(!isset($_GET['q'])) {
        echo "Please provide a query string via the \"q\" parameter.";
        exit;
    }
    $key = pg_escape_string($_GET['q']);
    
    $dbconn = pg_connect("host=$dbhost port=5432 dbname=$dbname user=$dbuser password=$dbpass");

  
    // GET SURVEY DETAILS
    $query = "SELECT * FROM tweets WHERE id = '".$key."'";
    $results = pg_query($dbconn, $query) or die(pg_last_error());
    $row = pg_fetch_object($results);

    // STORE WHEN THE USER FIRST ACCESSED THE SURVEY
    if (strlen($row->accessedsurvey) < 1) {
        $q = "UPDATE tweets SET accessedsurvey = now() where id = '".$key."'";
        pg_query($dbconn, $q) or die(pg_last_error());
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>UTRC TwitterTracks</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bootstrap.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <meta name="twitter:widgets:theme" content="light">
  <meta name="twitter:widgets:link-color" content="#55acee">
  <meta name="twitter:widgets:border-color" content="#55acee">

</head>
<body>

<div class="container">
  <div class="jumbotron">
    <h1>UTRC</h1>
    <p>Twitter, for good</p>
  </div>
  <h3>You said...</h3>
  <blockquote class="twitter-tweet" lang="en"><p><?php echo $row->tweet; ?></p>&mdash; <?php echo $row->username; ?>&nbsp;(<a style='font-size:12px;' href="https://twitter.com/<?php echo $row->username; ?>/status/<?php echo $row->tweetid; ?>"><?php echo date('F d',strtotime($row->ts)); ?></a>)</blockquote>
  <!-- <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script> -->

  <!-- <a class="twitter-timeline" href="https://twitter.com/mario_giampieri" data-widget-id="579642852617097216">Tweets by @mario_giampieri</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script> -->

<div id="selcont" class="container">
    <h2> Tell us more about your trip</h2>
      <br>
      <h4>Thanks for agreeing to participate. Please choose a description that best fits your reason for travel from the list below:</h4>
        <br>

<div class="form-group">
    <div id="hsel">Please selected an answer from the list below</div>
  <label for="sel1">What best describes your reason for travelling?</label>
    <select class="form-control" id="sel1">
    <option>Select an answer...</option>
    <option>Work or school at regular location</option>
    <option>Work or school activities at other places</option>
    <option>Shop</option>
    <option>Drop off or pick up someone</option>
    <option>Eat meal out at a restaurant or diner</option>
    <option>Recreation / entertainment</option>
    <option>Social / visit friends or relatives</option>
    <option>Other family or personal business (health care, religious activities, etc.)</option>
    <option>None of the above</option>
  </select>
</div>

    <br>

<p>Thanks again for your participation!</p>
    <button id="submitbutton" type="button" class="btn btn-default">Submit >></button>
</div>
    <br>
  <div class="footer">
    <h5>PROJECT PURPOSE</h5>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec tincidunt justo sed erat finibus, quis sollicitudin sapien efficitur. Cras consectetur tortor vitae metus fringilla, in tincidunt nisl porta. Proin lacus elit, ullamcorper non iaculis sed, ullamcorper a nunc. Aliquam fermentum, ipsum eu pharetra porta, lectus neque iaculis turpis, id consequat magna diam vitae urna. Suspendisse mollis hendrerit metus ut dictum. Mauris porta lectus id diam varius fringilla.</p>
    <h5>WHO WE ARE</h5>
      <p>Duis sit amet accumsan tortor, eu condimentum nulla. Cras pulvinar nibh vitae elit varius, eget lacinia felis molestie. Aliquam commodo ipsum convallis purus condimentum interdum. Etiam suscipit accumsan sodales.</p>
    <h5>PRIVACY & LEGAL</h5>
      <p>Morbi tellus mi, blandit sed lacinia ut, luctus et arcu. Sed venenatis at ex in aliquet. Vestibulum scelerisque rutrum enim sollicitudin dictum. Vivamus laoreet eleifend lacus. Duis in egestas massa. Aenean mollis sed lacus id venenatis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vestibulum dignissim risus sit amet lacus molestie, facilisis aliquam enim scelerisque. Aenean id nunc ut est elementum scelerisque non non magna. Nunc congue augue sed hendrerit pulvinar. Ut a sapien sapien. Aliquam lobortis erat in magna varius condimentum. Aliquam lobortis lacinia augue id efficitur. Suspendisse efficitur et justo a lobortis. Phasellus mi diam, pharetra eu arcu quis, efficitur laoreet nunc.</p>
</div>
  <script>
    var key = "<?php echo $row->id; ?>";
    
    $('#submitbutton').on('click', function() {
        if ($('#sel1').val() == "Select an answer...") {
            $('#hsel').show();
        } else {
            $('#hsel').hide();
            var output = {key:key, class: $('#sel1').val()};
            $.ajax({   
                url: "addclassification.php", 
                dataType: "json",
                data: output,
                success: function(result){
                    if(result.status == 200)
                        $('#selcont').html("<div style='font-size:15px;color:#0066ff'>Thank you for your participation.</div>");
                }
            });
        }
    });
  </script>
</body>
</html>
