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
	if(opl)	map.removeLayer(opl);
	opl = L.geoJson(data);
	opl.addTo(map);
	map.fitBounds(opl.getBounds());
}
