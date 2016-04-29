<?php

// Connect to the database
include('../db.inc');
$db = connect_to_database();

// Get the variables ready
$status = "not started";
$name = $db->escape_string($_GET['name']);
$ward = $db->escape_string($_GET['ward']);
$year = date("Y");

// Insert the campaign
if (!($q = $db->prepare("INSERT INTO l_campaigns (name,year,ward) VALUES (?, ?, ?)"))) {
  die("Campaign insert prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param("ssi", $name, $year, $ward);
$q->execute();

// Get the ID of the new campaign
$camp = $db->insert_id;

// Get the list of roads in the ward
if (!($q = $db->prepare("SELECT id FROM l_roads WHERE ward = ?"))) {
  die("Road fetch prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param('i', $ward);
$q->execute();
$q->bind_result($road);

$roads = array();
// Insert new progress records for all the roads
while ($q->fetch()) {
  $roads[] = $road;
}

foreach ($roads as $id) {
  // Prepare the insert query
  if (!($qi = $db->prepare("INSERT INTO l_progress (campaign,road,status) VALUES (?, ?, ?)"))) {
    die("Road insert prepare failed: (" . $db->errno . ") " . $db->error);
  }
  $qi->bind_param("iis", $camp, $id, $status);
  $qi->execute();
}

$db->close();

print $camp;

?>
