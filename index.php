<!DOCTYPE html>
<head>

<meta charset=utf-8>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<title>mapp.net &sect; lab.cdn.cx : lab &middot; /see/ /dee/ /en/ -dot- /see/ /eks/ :</title>

	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.3.1/leaflet.css" />
	<!--[if lte IE 8]><link rel="stylesheet" href="../dist/leaflet.ie.css" /><![endif]-->

	<script src="http://cdn.leafletjs.com/leaflet-0.3.1/leaflet.js"></script>
	<script src=https://raw.github.com/ideak/leafclusterer/master/leafclusterer.js></script>

	<style>
		body {
			padding: 0;
			margin: 0;
		}
		html, body, #map {
			height: 100%;
		}
		#map {  height: 40em }

		#userblk { background: rgba(255,255,255, .7); position: absolute; right: 0; top: 0; z-index: 53 }
	</style>
</head>
<body>
<?php

// checking if the 'Remember me' checkbox was clicked
if (isset($_GET['rem'])) {
	session_start();
	if ($_GET['rem']=='1') {
		$_SESSION['rem']=1;
	} else {
		unset($_SESSION['rem']);
	}
	header('Location: .');
}

require_once '/path/to/EZAppDotNet.php';
$app = new EZAppDotNet();

$_SESSION['path'] = 'mapp.net/';

/*
echo '<pre>';

//
echo "print_r(\$app)\n\n";
//
print_r($app);

//
echo "print_r(\$_SESSION)\n\n";
//
print_r($_SESSION);

echo "</pre>\n";
*/

echo '<div id=userblk>';

// check that the user is signed in
if ($app->getSession()) {

	try {
		$denied = $app->getUser();
	//	print " error - we were granted access without a token?!?\n";
	//	exit;
	}
	catch (AppDotNetException $e) { // catch revoked access and existing session // Safari 6 doesn't like
		if ($e->getCode()==401) {
			print " success (could not get access)\n";
		}
		else {
			throw $e;
		}
		$app->deleteSession();
		header('Location: .'); die;
	}

	// get the current user as JSON
	$data = $app->getUser();

	// accessing the user's name
	echo '<h3>'.$data['name'].'</h3>';

	// accessing the user's avatar image
	echo '<img style="border:2px solid #000;" src="'.$data['avatar_image']['url'].'?h=48&amp;w=48" /><br>';

	echo '<h2><a href="/signout.php">Sign out</a></h2>';

// otherwise prompt to sign in
} else {

	$url = $app->getAuthUrl(); //'http://lab.cdn.cx/callback.php' ?_=mapp.net'); 500 with older lib / exception with current
	echo '<a href="'.$url.'"><h2>Sign in using App.net</h2></a>';
	if (isset($_SESSION['rem'])) {
		echo 'Remember me <input type="checkbox" id="rem" value="1" checked/>';
	} else {
		echo 'Remember me <input type="checkbox" id="rem" value="2" />';
	}
	?>
	<script>
	document.getElementById('rem').onclick = function(e){
		if (document.getElementById('rem').value=='1') {
			window.location='?rem=2';
		} else {
			window.location='?rem=1';
		};
	}
	</script>
	<?php
}

?>
</div>

	<div id="map"></div>

	<script>
		var map = new L.Map('map');

		map.addLayer(new L.TileLayer('http://{s}.tile.stamen.com/watercolor/{z}/{x}/{y}.png', {
			maxZoom: 7,
			attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a>. Data by <a href="http://openstreetmap.org">OpenStreetMap</a>, under <a href="http://creativecommons.org/licenses/by-sa/3.0">CC BY SA</a>.'
		}));

<?php

include('/path/to/geoipcity.inc');

$gi = geoip_open('/path/to/geoip/GeoLiteCity.dat', GEOIP_STANDARD);

$ip = $_SERVER['REMOTE_ADDR']; // IPv4 nnn.nnn.nnn.nnn
$record = geoip_record_by_addr($gi, $ip);

geoip_close($gi);

?>
	//	map.setView(new L.LatLng(52.460193,-1.92647), 7);
		map.setView(new L.LatLng(<?php echo $record->latitude . ',' . $record->longitude ?>), 7);
			map.addLayer(new L.Marker(map.getCenter())
				.bindPopup("GeoIP thinks you're here (?)").openPopup());
		var clst = new LeafClusterer(map, null, {});

		var appAvatar = L.Icon.extend({
			iconUrl: 'https://d2c01jv13s9if1.cloudfront.net/i/z/G/o/zGoMjhhKTKxI5cCeJlAkvFXy2L4.png?h=48&w=48',
			iconSize: new L.Point(48, 48),
			shadowUrl: null
		});

                var egAppNetters = [
{"la": 53.2, "lo": -0.2, "n": 2726, "u": 'cdn', "iconUrl": "https://d1f0fplrc06slp.cloudfront.net/assets/user/0a/71/00/0a71000000000000.jpg?h=48&w=48"},
{"la": 52, "lo": -0.64, "n": 194, "u": 'documentally', "iconUrl": "https://d1f0fplrc06slp.cloudfront.net/assets/user/24/20/00/2420000000000000.png?h=48&w=48"},
{"la": 51.3, "lo": -0.09, "n": 350, "u": 'buddhamagnet', "iconUrl": "https://d2rfichhc2fb9n.cloudfront.net/image/4/xb3qTp_Dv88nJHv1VUwJ72-Bk96XVgoM2SYkzLmJgaXReuyBPAgdwl2oHY0ev1IF9kqwJZt88c33BwfqZ6325xJvdX5xBTnQfpy7z8p5CnIoQxSdfeGs6rbXBSKvnX5wS4Bh-cJPCSmxB3iKxlC3kAKAXbk?h=48&w=48"},
{"la": 51.22, "lo": 6.8, "n": 1694, "u": 'ralf', "iconUrl": "https://d1f0fplrc06slp.cloudfront.net/assets/user/1d/01/00/1d01000000000000.jpg?h=48&w=48"},
/*{"la": 0, "lo": 0, "iconUrl": "https://d2c01jv13s9if1.cloudfront.net/i/z/G/o/zGoMjhhKTKxI5cCeJlAkvFXy2L4.png?h=48&w=48"},*/
                ];

		var o = egAppNetters, icon;

		if (o && o.length && o.length > 0) {
			for (var i = 0; i < o.length; i++) {

			if(o[i].iconUrl.length > 0)
				icon = new appAvatar(o[i].iconUrl);
			else
				icon = new appAvatar();

			clst.addMarker(new L.Marker(new L.LatLng(o[i].la, o[i].lo), {icon: icon}).bindPopup(o[i].u + '<br>' + o[i].n))

			}
		}

		function onLocationFound(e) {
			var radius = e.accuracy / 2;

			map.addLayer(new L.Marker(e.latlng)
				.bindPopup("You are within " + radius + " metres of this point").openPopup());

			map.addLayer(new L.Circle(e.latlng, radius));
		}

		function onLocationError(e) {
			alert(e.message);
		}

		map.on('locationfound', onLocationFound);
		map.on('locationerror', onLocationError);

		map.locate({setView: true, maxZoom: 16});

		function getRandomLatLng() {
			var bounds = map.getBounds();
			var sW = bounds.getSouthWest(), nE = bounds.getNorthEast(), lngS, latS, latLng;

			sW = new L.LatLng(-70, -175); nE = new L.LatLng(70, 175);

			lngS = nE.lng - sW.lng;
			latS = nE.lat - sW.lat;

			latLng = new L.LatLng(
			    sW.lat + latS * Math.random(),
			    sW.lng + lngS * Math.random()
			);

			return latLng;
		}

/*
		for(i = 0; i < 1498; i++) { // 13 [ 9998 is too many :) ]
			clst.addMarker(new L.Marker(getRandomLatLng(map), {icon: new appAvatar()}).bindPopup('rnd' + '<br>' + i))
		}
*/

	</script>
</body>
</html>
