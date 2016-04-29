complete = [];
inprogress = [];
notstarted = [];
owners = {};

<?php

// Connect to database
include('../db.inc');
$db = connect_to_database();

// GET data
$ward = $db->escape_string($_GET['ward']);
$camp = $db->escape_string($_GET['campaign']);

// Prepare select to pull out roads
if (!($q = $db->prepare("SELECT l_roads.id,l_progress.status,l_progress.owner FROM l_roads LEFT JOIN l_progress ON l_roads.id=l_progress.road WHERE l_roads.ward = ? AND l_progress.campaign = ?"))) {
  die("Prepare failed: (" . $db->errno . ") " . $db->error);
}
$q->bind_param('ii', $ward, $camp);
$q->execute();
$q->bind_result($road, $status,$owner);

// Create JS to add each road to one of the three arrays
while ($q->fetch()) {
  if ($status == 'finished') {
    print "complete.push('" . $road  . "');\n";
  } elseif ($status == 'in progress') {
    print "inprogress.push('" . $road  . "');\n";
  } elseif ($status == 'not started') {
    print "notstarted.push('" . $road  . "');\n";
  }
  if ($owner) {
    print "owners['" . $road  . "'] = '" . $owner . "';\n";
  }
}
?>
