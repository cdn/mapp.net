<?php

if(empty($_GET) && empty($_POST)) {
// do effectively nothing
// return -1
echo -1; die;
}

// if geo query (GET?) get JSON latitude/longitude from Yahoo! Placefinder

if(!empty($_GET) && isset($_GET['q']) && $_GET['q'] !== '') {

$q = urlencode($_GET['q']);
//$q = htmlentities($_GET['q'],ENT_QUOTES,"UTF-8");
$a = simplexml_load_file("http://where.yahooapis.com/geocode?flags=E&q=$q", 'SimpleXMLElement', LIBXML_NOCDATA);

//error check?

echo '[{"la":';
echo $a->Result->latitude;
echo ', "lo":';
echo $a->Result->longitude;
echo '}]';

die;
} // else { echo -1; }

// if map placement (POST) add ADN user and their geo data to database

if(!empty($_POST) && isset($_POST['lat']) && isset($_POST['lon'])) {

$la = (double)$_POST['lat'];
$lo = (double)$_POST['lon'];

if(abs($la) > 90 || abs($lo) > 180) {
// latitude and/or longitude out of range
// return -2
 echo -2; die;
}

// session for user data, rather than complicating things by passing it
require_once '/path/to/EZAppDotNet.php';

$app = new EZAppDotNet();

// check that the user is signed in
if ($app->getSession()) {

	try {
		$denied = $app->getUser();
	}
	catch (AppDotNetException $e) { // catch revoked access and existing session // Safari 6 doesn't like
		if ($e->getCode()==401) {
			print " success (could not get access)\n";
		}
		else {
			throw $e;
		}
		$app->deleteSession();
	//	header('Location: ./index.php');
		// return something useful as an error code [-3]
		echo -3;
		die;
	}

	// get the current user as JSON
	$data = $app->getUser();

	$data['latitude']  = 50.0;
	$data['longitude'] = -0.0;

	$data['latitude']  = $la;
	$data['longitude'] = $lo;


	// db machinations | PDO php >= 5.1.0

	try {

	$pod = new PDO('mysql:host=localhost;dbname=database_name', $user, $pass);

	} catch (PDOException $e) {

	 // echo 'alert("Sorry no database for you; ' . $e->getMessage() . '"); </script>'; die;
	 // return -4
	 echo -4; die;
	}

	// reduce object to url reference
	$data['avatar_image'] = $data['avatar_image']['url'].'?h=48&amp;w=48';

	$qi  = 'insert into user_geo_data ';
	$qi .= '(id, username, name, avatar_image, latitude, longitude) values ';
        $qi .= '(:id, :username, :name, :avatar_image, :latitude, :longitude)';

	try {
	  $pod->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	  $pod->beginTransaction();

	  $r = $pod->prepare($qi);

	  $r->bindParam(':id', $data['id'], PDO::PARAM_STR);
	  $r->bindParam(':username', $data['username'], PDO::PARAM_STR, 20);
	  $r->bindParam(':name', $data['name'], PDO::PARAM_STR);
	  $r->bindParam(':avatar_image', $data['avatar_image'], PDO::PARAM_STR);
	  $r->bindParam(':latitude', $data['latitude'], PDO::PARAM_STR);
	  $r->bindParam(':longitude', $data['longitude'], PDO::PARAM_STR);

	  $r->execute();

	  $pod->commit();

	} catch (Exception $e) {
	  $pod->rollBack();

	  // suitable error return [-5]
	  echo -5; die;
	}

	echo 0;

} // fi

} // _POST
