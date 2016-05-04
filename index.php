<?php

// Connect to database
include('db.inc');
$db = connect_to_database();

// Get campaigns
$campaigns = array();
$wards = array();
if (!($q = $db->prepare("SELECT l_campaigns.id,l_campaigns.name,l_campaigns.year,l_campaigns.ward,l_wards.name FROM l_campaigns LEFT JOIN l_wards ON l_campaigns.ward=l_wards.id"))) {
  die("Prepare 1 failed: (" . $db->errno . ") " . $db->error);
}
$q->execute();
$q->bind_result($cid,$cname,$cyear,$wid,$wname);
while ($q->fetch()) {
  if (!array_key_exists($cid,$campaigns)) {
    $campaigns[$cid] = array('name' => $cname, 'year' => $cyear, 'id' => $cid, 'wards' => array());
  }
  if (!array_key_exists($wid,$wards)) {
    $wards[$wid] = array('name' => $wname, 'id' => $wid, 'campaigns' => array());
  }
  $campaigns[$cid]['wards'][] = array('wid' => $wid, 'wname' => $wname);
  $wards[$wid]['campaigns'][] = array('cid' => $cid, 'cname' => $cname, 'year' => $cyear);
}
$q->free_result();

// Goodnight
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Campaigns | Home</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="style.css" />
</head>

<body>

  <div class="navbar navbar-home navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <h1 class="navbar-brand">Campaigns tracker</h1>
      </div>
    </div>
  </div>
  <div id="container">
    <div class="row">
      <div class="col-md-6">
<!--
        <h2>Campaigns</h2>
        <?php foreach ($campaigns as $camp): ?>
        <h3><?php print($camp['name']); ?> (<?php print($camp['year']); ?>)</h3>
        <ul id="list-campaigns-<?php print($camp['id']); ?>">
          <?php foreach ($camp['wards'] as $ward): ?>
          <li><a href="progress.php?campaign=<?php print($camp['id']);?>&ward=<?php print($ward['wid']); ?>"><?php print($ward['wname']); ?></a></li>
          <?php endforeach; ?>
          <li><button type="button" class="btn btn-info btn-xs" style="cursor:pointer">Add a new ward</button></li>
        </ul>
        <?php endforeach; ?>
      </div>
-->
      <div class="col-md-12">
        <h2>Green Party campaign tracker</h2>
        <?php foreach ($wards as $ward): ?>
        <h3><?php print($ward['name']); ?></h3>
        <p><a href="editward.php?ward=<?php print($ward['id']); ?>">Edit ward roads</a></p>
        <h4>Campaigns in <?php print($ward['name']); ?></h4>
        <ul id="list-wards-<?php print($ward['id']); ?>">
          <?php foreach ($ward['campaigns'] as $camp): ?>
          <li>
            <form id="addcampaign-<?php print($ward['id']); ?>" class="form-inline">
            <div class="form-group-xs">
              <a href="progress.php?campaign=<?php print($camp['cid']);?>&ward=<?php print($ward['id']); ?>" class="cedit-<?php print($camp['cid']);?>" id="clink-<?php print($camp['cid']);?>"><?php print($camp['year']); ?> - <?php print($camp['cname']); ?></a>
              <button id="editcamp-<?php print($camp['cid']); ?>" type="button" class="btn btn-info btn-xs cedit-<?php print($camp['cid']);?>" style="cursor:pointer">edit</button>
              <input type="text" size="14" class="form-control input-xs start-hidden csave-<?php print($camp['cid']);?>" value="<?php print($camp['cname']); ?>" id="name-<?php print($camp['cid']);?>">
              <button id="savecamp-<?php print($camp['cid']); ?>" type="button" class="btn btn-success btn-xs start-hidden csave-<?php print($camp['cid']);?>" style="cursor:pointer">save</button>
              <button id="cancelcamp-<?php print($camp['cid']); ?>" type="button" class="btn btn-info btn-xs start-hidden csave-<?php print($camp['cid']);?>" style="cursor:pointer">cancel</button>
              <button id="delecamp-<?php print($camp['cid']); ?>" type="button" class="btn btn-danger btn-xs start-hidden csave-<?php print($camp['cid']);?> confirm" style="cursor:pointer">delete</button>
            </div>
            </form>
          </li>
          <?php endforeach; ?>
        </ul>
        <form id="addcampaign-<?php print($ward['id']); ?>" class="form-inline">
        <div class="form-group-sm">
          <label for="newname-<?php print($ward['id']); ?>">Add a new campaign:</label> 
          <input id="newname-<?php print($ward['id']); ?>" type="text" size="14" placeholder="Campaign name" class="form-control input-xs"> 
          <button id="ac-<?php print($ward['id']); ?>" type="button" class="btn btn-success btn-sm" style="cursor:pointer">Go</button>
        </div>
        </form>
        <?php endforeach; ?>        
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="js/jquery.confirm.min.js"></script>
<script src="js/index.js"></script>

</body>
</html>
