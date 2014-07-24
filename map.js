var map = L.map('map').setView(new L.LatLng(52.265, 10.524), 14);
var opl = null;

L.tileLayer('http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png', {
	maxZoom: 18,
	attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
		'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
	id: 'deTourMap'
}).addTo(map);

function showRel(id) {
	var url = "geo.php?id="+id;

	$.ajax({
		dataType: "json",
		url: url,
		success: displayData
	});
}

function displayData(data) {
	// Remove old Layer if exists
	if(opl)	map.removeLayer(opl);
	// Adding Layer
	opl = L.geoJson(data, {style: styleRelation, pointToLayer: startEndPoint });
	opl.addTo(map);
	map.fitBounds(opl.getBounds());

	// Show Data in Info-Box
	if(!data.features[0]) {
		alert("Error loading Data.");
		return; // No Data
	}

	var keyVal = data.features[0].properties;
	var id = keyVal["relation"];

	var html = '<h3>OSM Realtion <a href="https://www.openstreetmap.org/relation/'+id+'" target="_blank">'+id+'</a></h3><ul>';

	for (var k in keyVal){
		if(k == 'id' || k == 'role' || k == 'relation' || k == 'member')
			continue;
		html += '<li>' + k + ': ' + keyVal[k] + '</li>';
	}

	html += '</ul>';
	$('#info').html(html);	
	
}

function startEndPoint(feature, latlng) {
	var color = 'blue';
	var ch = 'unk';
	if(feature.properties.role == 'start') {
		color = 'green';
		ch = 's';
	}
	if(feature.properties.role == 'end') {
		color = 'red';
		ch = 'e';
	}

	var mark = L.AwesomeMarkers.icon({
		markerColor: color, prefix: 'ch', icon: ch
	});
	return new L.Marker(latlng, { title: feature.properties.role, icon: mark });
}

function styleRelation (feature) {
	if(feature.properties.detour) {
		if(feature.properties.detour.startsWith("A"))
			return {color: "blue",  opacity: 1 };
		if(feature.properties.detour.startsWith("B"))
			return {color: "orange",  opacity: 1 };
	}
        return {color: "red",  opacity: 1 };
}
