<?php

$xml = simplexml_load_file('detour.xml');

// Find all relations
foreach($xml->relation as $relation) {

	$values = array();
	$values['id'] = (string) $relation['id'];

	foreach($relation->tag as $tag) {
		if($tag['k'] == 'ref' || $tag['k'] == 'detour' || $tag['k'] == 'destination')
			$values[(string)$tag['k']] = (string) $tag['v'];
	}

	if(isset($values['ref'])) {
		// Reference available
		if(isset($values['detour']))
			$det = $values['detour'];
		else
			$det = "unknown";

		$list[$det][$values['ref']] = $values;
	}

}

// Sort streets
ksort($list);

function output () {
	global $list;
	// Output
	foreach($list as $detour=>$refs) {
		print "<h2>$detour</h2>";
		print "<ul>";
		outputRefs($refs);
		print "</ul>";
	}
}

function outputRefs(&$refs) {
	ksort($refs);

	foreach($refs as $ref) {
		print "<li>";
		print '<a href="javascript:showRel('.$ref['id'].')">';
		print $ref['ref'];
		if(isset($ref['destination']))
			print " (".$ref['destination'].")";
		print "</a>";
		print "</li>";
	}
}

?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>detourViewer</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />

<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
<script src="overpasslayer.js"></script>

<style>
body {
	font-size: 0.7em;
}
#roadlist {
	width: 150px;
	float:left;
}
#map { margin-left: 155px; height: 500px; width: 800px;}
</style>
<script>
$(function() {
$( "#roadlist" ).accordion({
heightStyle: "content"
});
});
</script>
</head>
<body>
	<h1>List of detours by road</h1>
	<div>
		<div id="roadlist">
			<?php output(); ?>
		</div>
		<div id="map"></div>
		<script src="map.js"></script>
	</div>
</body>
</html>
