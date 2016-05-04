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
  //onEachFeature: onEachFeature,
  style: style
}).addTo(map);

// Add labels overlay
L.tileLayer("http://tile.stamen.com/toner-labels/{z}/{x}/{y}.png", { minZoom: 16 }).addTo(map);

// Add draw controls
var drawControl = new L.Control.Draw({
  draw: false,
  edit: {
    featureGroup: roadsLayer
  }
});
map.addControl(drawControl);

// Functions to update features
map.on('draw:edited', function (e) {
  e.layers.eachLayer(function (layer) {
    var shape = JSON.stringify(layer.toGeoJSON());
    var q = "lib/updategeojson.php?ward=" + GetURLParameter('ward') + "&data=" + shape + "&action=edit";
    console.log("(" + q + ")");
    $.get(q, function(d) { if (d) { alert(d); } });
  });
});
map.on('draw:deleted', function (e) {
  e.layers.eachLayer(function (layer) {
    var q = "lib/updategeojson.php?ward=" + GetURLParameter('ward') + "&data=" + layer.feature.properties.id + "&action=delete";
    console.log("(" + q + ")");
    $.get(q, function(d) { if (d) { alert(d); } });
  });
});

/*
 * FUNCTIONS
 */

// Randomise the colour of the roads
function style(feature) {
  return {
    color: getRandomColour()
  };
}

// Add status to each road and create popup
function onEachFeature(feature, layer) {
  //
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

// Give lines a random colour
function getRandomColour() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
