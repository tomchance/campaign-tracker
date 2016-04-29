<?php

// Connect to the database
include('../db.inc');
$db = connect_to_database();

// Get the variable ready
$camp = $db->escape_string($_GET['campaign']);

// Delete the progress information
if (!($q = $db->prepare("DELETE FROM l_progress WHERE campaign = ?"))) {
  die("Campaign insert prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param("i", $camp);
$q->execute();

// Delete the campaign itself
if (!($q = $db->prepare("DELETE FROM l_campaigns WHERE id = ?"))) {
  die("Campaign insert prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param("i", $camp);
$q->execute();

$db->close();

?>
