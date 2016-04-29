<?php

// Connect to database
include('../db.inc');
$db = connect_to_database();

// Get URL parameters
$type = $db->escape_string($_GET['type']);
$id = $db->escape_string($_GET['id']);

// Print the GeoJSON heading
print "var ward_{$type} = { \"type\": \"FeatureCollection\", \"features\": [";

// Prepare the statements and set the geometry type
if ($type == 'bounds') {
  if (!($q = $db->prepare("SELECT id,name,geometry FROM l_wards WHERE id = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
  $geotype = "Polygon";
} else {
  if (!($q = $db->prepare("SELECT id,name,geometry,number FROM l_roads WHERE ward = ?"))) {
    die("Prepare failed: (" . $db->errno . ") " . $db->error);
  }
  $geotype = "LineString";
}
$q->bind_param('i', $id);

// Execute...
$q->execute();
if ($type == 'bounds') {
  $q->bind_result($id, $name, $geom);
} else {
  $q->bind_result($id, $name, $geom, $number);
}

// Go through the features printing the GeoJSON
$features = '';
while ($q->fetch()) {
  $features = $features . '{ "type": "Feature", "properties": { "name": "' . $name . '", "id": "' . $id . '", "number": "' . $number . '" }, "geometry": { "type": "' . $geotype . '", "coordinates": ' . json_encode(unserialize($geom)) . " } },";
}
$features = rtrim($features, ',');
print $features;

// Goodnight
$q->free_result();
$db->close();

?>
] }
