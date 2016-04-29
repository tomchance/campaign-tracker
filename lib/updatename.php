<?php

// Connect to the database
include('../db.inc');
$db = connect_to_database();

// Get URL parameters
$table = $db->escape_string($_GET['table']);
$id = $db->escape_string($_GET['id']);
$name = $db->escape_string($_GET['name']);

// Prepare query
if ($table == 'l_campaigns') {
  if (!($q = $db->prepare("UPDATE l_campaigns SET name = ? WHERE id = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
} elseif ($table == 'l_wards') {
  if (!($q = $db->prepare("UPDATE l_wards SET name = ? WHERE id = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
}
if (!($q->bind_param('si', $name, $id))) {
  die("Binding failed: (" . $db->errno . ") " . $db->error); 
}

// Do it
if (!($q->execute())) {
  die("Prepare failed: (" . $db->errno . ") " . $db->error);
}

// Goodnight
$q->close();
$db->close();

?>
