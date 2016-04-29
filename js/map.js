/*
 * Show a map of the ward with roads
 */

// Initialise map
var map = L.map("map");

// Add baselayer
L.tileLayer("https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpandmbXliNDBjZWd2M2x6bDk3c2ZtOTkifQ._QA7i5Mpkd_m30IGElHziw", {
  maxZoom: 18,
  attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
  id: "mapbox.light"
}).addTo(map);

// Add ward boundary and zoom to bounds
var boundsLayer = L.geoJson(ward_bounds, { style: { color: "black", opacity: 0, dashArray: "30,15", fillColor: "blue", fillOpacity: 0.15} }).addTo(map);
map.fitBounds(boundsLayer.getBounds());

// Add roads
var roadsLayer = L.geoJson(ward_roads, {
  onEachFeature: onEachFeature
}).addTo(map);

// Add labels overlay
L.tileLayer("http://tile.stamen.com/toner-labels/{z}/{x}/{y}.png", { minZoom: 16 }).addTo(map);

// Refresh the roads so they colour correctly
updateStatus();


/*
 * FUNCTIONS
 */

// Add status to each road and create popup
function onEachFeature(feature, layer) {
  if ($.inArray(feature.properties.id, complete) > -1) {
    feature.properties.status = "finished";
  } else if ($.inArray(feature.properties.id, inprogress) > -1) {
    feature.properties.status = "in progress";
  } else {
    feature.properties.status = "not started";
  }
  if (owners[feature.properties.id]) {
    feature.properties.owner = owners[feature.properties.id];
  }
  if (feature.properties.owner) {
    owner = 'value="' + feature.properties.owner;
  } else {
    owner = 'placeholder="Enter name';
  }
  var popupContent = '<h3>' + feature.properties.name +'</h3>'
    + '<div class="btn-group btn-group-xs" role="group" aria-label="...">'
    + '<button type="button" class="btn btn-danger" style="cursor:pointer" onClick="" id ="ns-' 
    + feature.properties.id 
    + '">Not started</button><button type="button" class="btn btn-warning" style="cursor:pointer" onClick="" id ="ip-' 
    + feature.properties.id 
    + '">In progress</button><button type="button" class="btn btn-success" style="cursor:pointer" onClick="" id ="fi-' 
    + feature.properties.id 
    + '">Finished</button></div>'
    + '<h4>Update street info</h4>'
    + '<form class="form-inline"><div class="form-group">'
    + '<label for="number">Done by:</label><input type="text" size="8" id="ow-'
    + feature.properties.id
    + '" '
    + owner
    + '" class="form-control  input-xs" aria-describedby="sizing-addon3">'
    + '<label for="number">Leaflets:</label><input type="text" size="1" id="no-'
    + feature.properties.id
    + '" value="'
    + feature.properties.number
    + '" class="form-control  input-xs" aria-describedby="sizing-addon3">'
    + '<input type="submit" class="btn btn-info .btn-xs" value="Save" id="up-'
    + feature.properties.id
    + '" /></div></form>';
  if (feature.properties && feature.properties.popupContent) {
    popupContent += feature.properties.popupContent;
  }
  var popupOptions = {
    'minWidth': '200',
    'maxWidth': '200',
  }
  layer.bindPopup(popupContent, popupOptions);
}


// Return the value of an URL parameter
function GetURLParameter(sParam) {
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split('&');
  for (var i = 0; i < sURLVariables.length; i++) {
    var sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] == sParam) {
      return sParameterName[1];
    }
  }
} 

// Update the database record for a road's status if necessary and re-colour
function updateStatus(id, newstatus) {
  for (var roadfeature in roadsLayer._layers) {
    if (id && id == roadsLayer._layers[roadfeature].feature.properties.id) {
      roadsLayer._layers[roadfeature].feature.properties.status = newstatus;
      campaign = GetURLParameter('campaign');
      console.log("Updating db records for road " + id + " to " + newstatus);
      var q = "lib/updatestatus.php?status=" + newstatus + "&road=" + id + "&campaign=" + campaign
      console.log("(" + q + ")");
      $.get(q, function(d) { if (d) { alert(d); } });
    }
    if (roadsLayer._layers[roadfeature].feature.properties.status == 'finished') {
      roadsLayer._layers[roadfeature].setStyle({color: "#66cc00", weight: "8"});
    }
    else if (roadsLayer._layers[roadfeature].feature.properties.status == 'in progress') {
      roadsLayer._layers[roadfeature].setStyle({color: "#ff6600", weight: "8"});
    }
    else {
      roadsLayer._layers[roadfeature].setStyle({color: "#ff0000", weight: "8"});
    }
  }
}

// Bind buttons in the popup to the update status function
map.on("popupopen", function() {
  $(':submit').prop('disabled', true);
  $('input[type="text"]').keyup(function() {
    if($(this).val() != '') {
      $('input[type="submit"]').prop('disabled', false);
    }
  });
  $(":button").click(function () {
    var btn = $(this).attr("id");
    var specs = btn.split("-");
    var newstatus = "";
    if (specs[0] == "ns") { newstatus = "not started"; }
    else if (specs[0] == "ip") { newstatus = "in progress"; }
    else if (specs[0] == "fi") { newstatus = "finished"; }
    updateStatus(specs[1], newstatus);
  });
  $(":submit").click(function (e) {
    e.preventDefault();
    var btn = $(this).attr("id");
    var specs = btn.split("-");
    var newnumber = $("#no-" + specs[1]).val();
    var newowner = $("#ow-" + specs[1]).val();
    var campaign = GetURLParameter('campaign');
    console.log("Updating db records for road " + specs[1] + " to show owners is " + newowner + " and number of leaflets is " + newnumber);
    var q = "lib/updatestatus.php?owner=" + newowner + "&number=" + newnumber + "&road=" + specs[1] + "&campaign=" + campaign
    console.log("(" + q + ")");
    $.get(q, function(d) { if (d) { alert(d); } });
    $(this).prop('disabled', true);
  });
});
