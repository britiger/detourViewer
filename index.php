<?php

$xml = simplexml_load_file('detour.xml');

// Find all relations
foreach($xml->relation as $relation) {

	$values = array();
	$values['id'] = (string) $relation['id'];

	foreach($relation->tag as $tag) {
		if($tag['k'] == 'ref' || $tag['k'] == 'detour' || $tag['k'] == 'destination' || $tag['k'] == 'operator')
			$values[(string)$tag['k']] = (string) $tag['v'];
	}

	if(isset($values['ref'])) {
		// Reference available
		if(isset($values['detour']))
			$det = $values['detour'];
		else
			$det = "unknown";

		if(isset($values['operator']))
			$op = $values['operator'];
		else
			$op = "unknown";
		$list[$det][$values['id']] = $values;
		$listByOp[$op][$det][$values['id']] = $values; // Store Roads by Operator
	}

}

// Sort streets
uksort($list, "sortNumberCallback");
ksort($listByOp);

function outputByOp ($list) {
	// Output
	foreach($list as $op=>$roads) {
		print "<h2>$op</h2>";
		print '<div class="accord">';
		uksort($list, "sortNumberCallback");
		output($roads);
		print '</div>';
	}
}

function output ($list) {
	// Output List of Roads
	foreach($list as $detour=>$refs) {
		print "<h2>$detour</h2>";
		print "<ul>";
		outputRefs($refs);
		print "</ul>";
	}
}

function outputRefs(&$refs) {
	usort($refs, "sortNumberCallbackValue");

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

// Test the value for an Char+Number format, if correct split and give back an array
function convAlphaNum($value) {
	$first = substr($value,0,1);
	$last = trim(substr($value,1));

	if(is_numeric($first)) return false;
	if(empty($last)) return false;
	if(!is_numeric(substr($last,0,1))) return false;

	$number = intval($last, 10);
	if($number < 1) return false;

	return array($first, $number); // correct combination
}

function sortNumberCallbackValue($a, $b) {
	return sortNumberCallback($a['ref'], $b['ref']);
}

function sortNumberCallback($a, $b) {
	$aa = convAlphaNum($a);
	$ba = convAlphaNum($b);

	if(!$aa || !$ba)
		return strcasecmp($a, $b);

	if(strcasecmp($aa[0], $ba[0]) == 0)
		return $aa[1] - $ba[1];
	else
		return strcasecmp($aa[0], $ba[0]);
}

?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>detourViewer</title>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
<link rel="stylesheet" href="awesome-markers/leaflet.awesome-markers.css" />
<link rel="stylesheet" href="style.css" />

<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
<script src="awesome-markers/leaflet.awesome-markers.js"></script>

<script>
$(function() {
$( ".accord" ).accordion({
heightStyle: "content"
});
});
</script>
</head>
<body>
	<div id="leftside">
		<h1>List of detour relations by <?php if(isset($_GET['op'])) echo "operator and "; ?>road</h1>
		<p><a href="?<?php echo isset($_GET['op'])?"":"op";?>">change view</a></p>
		<div class="accord">
		<?php if(!isset($_GET['op']))output($list);else outputByOp($listByOp); ?>
		</div>
	</div>
	<div id="info">
	</div>
	<div id="map"></div>
	<script src="map.js"></script>
</body>
</html>

