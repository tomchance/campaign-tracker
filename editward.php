<?php

// Connect to database
include('db.inc');
$db = connect_to_database();

// Get URL parameters
$ward = $db->escape_string($_GET['ward']);

// Get ward name
if (!($q = $db->prepare("SELECT name FROM l_wards WHERE id = ?"))) {
  die("Prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param('i', $ward);
$q->execute();
$q->bind_result($ward_name);
$q->fetch();
$q->free_result();

// Goodnight
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Campaigns | <?php echo $ward_name; ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="leaflet.draw.css" />
<link rel="stylesheet" href="style.css" />
<script src="lib/geojson.php?type=bounds&id=<?php echo $ward; ?>" type="text/javascript"></script>
<script src="lib/geojson.php?type=roads&id=<?php echo $ward; ?>" type="text/javascript"></script>
<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script src="js/leaflet.draw.js"></script>
</head>

<body>

    <div class="navbar navbar-map navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <h1 class="navbar-brand">Campaigns progress</h1>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
          <ul class="nav navbar-nav">
            <li>
              <a href="index.php">Home</a>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Other campaigns <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Coming soon...</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div class="container-fluid">
        <div class="navbar-infobelow">
          Ward: <?php echo $ward_name; ?>
        </div>
      </div>
    </div>
    <div id="container">
      <div id="map"></div>
    </div>

<script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="js/editward.js"></script>

</body>
</html>
