<?php

// Connect to the database
include('../db.inc');
$db = connect_to_database();

// Get URL parameters
$road = $db->escape_string($_GET['road']);
$camp = $db->escape_string($_GET['campaign']);
$stat = $db->escape_string($_GET['status']);
$owne = $db->escape_string($_GET['owner']);
$numb = $db->escape_string($_GET['number']);

// If this is updating the status of a road...
if ($stat) {
  // Prepare query
  if (!($q = $db->prepare("UPDATE l_progress SET status = ? WHERE campaign = ? AND road = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
  if (!($q->bind_param('sii', $stat, $camp, $road))) {
    die("Binding failed: (" . $db->errno . ") " . $db->error); 
  }
  $q->execute();
// If this is about updating the owner and number of leaflets...
} elseif ($owne) {
  if (!($q = $db->prepare("UPDATE l_progress SET owner = ? WHERE campaign = ? AND road = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
  if (!($q->bind_param('sii', $owne, $camp, $road))) {
    die("Binding failed: (" . $db->errno . ") " . $db->error); 
  }
  $q->execute();
  if (!($q = $db->prepare("UPDATE l_roads SET number = ? WHERE id = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
  if (!($q->bind_param('ii', $numb, $road))) {
    die("Binding failed: (" . $db->errno . ") " . $db->error); 
  }
  $q->execute();
}

// Goodnight
$q->close();
$db->close();

?>
