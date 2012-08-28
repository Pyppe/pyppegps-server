<?php

$INDEX_FILE = "index.txt";
$KML_DIR = "./kml";

function addKMLToIndex($filename) {
  global $INDEX_FILE, $KML_DIR;
  $kml = simplexml_load_file($KML_DIR."/".$filename);
  $namespaces = $kml->getDocNamespaces(); 
  $kml->registerXPathNamespace('__empty_ns', $namespaces['']);
  
  $distance = getTotalDistanceInKilometers($kml);
  $duration = getDuration($kml);
  
  $indexFile = fopen($INDEX_FILE, "a");
  fwrite($indexFile, $filename."|".$duration."|".$distance."\n");
  fclose($indexFile);
}

function endsWith($string, $char) {
  $length = strlen($char);
  $start =  $length *-1; //negative
  return (substr($string, $start, $length) === $char);
}

function getTotalDistanceInKilometers($kml) {
  $coordinates = $kml->xpath('//__empty_ns:LineString/__empty_ns:coordinates'); 
  $coordinates = $coordinates[0][0];
  $coordinatePairs = explode(" ", $coordinates);

  $previousLongitude = null;
  $previousLatitude = null;
  $distance = 0;

  foreach($coordinatePairs as $key=>$value) {
    $coords = explode(",",$value);
    $longitude = $coords[0];
    $latitude = $coords[1];
    if (!empty($longitude) && !empty($latitude) && !empty($previousLongitude) && !empty($previousLatitude)) {
      //echo $longitude.",".$latitude."  -  ".$previousLongitude.",".$previousLatitude."<br/>";
      //echo calculateDistance($latitude, $longitude, $previousLatitude, $previousLongitude) . "<br />";
      $distance = $distance + calculateDistance($latitude, $longitude, $previousLatitude, $previousLongitude);
    }
    $previousLongitude = $longitude;
    $previousLatitude = $latitude;
  }
  
  return round($distance,2);
}

function getDuration($kml) {
  $duration = $kml->xpath('//__empty_ns:Data[@name="PyppeGPSduration"]/__empty_ns:value');
  if (!empty($duration)) {
    return $duration[0][0];
  } else {
    return "";
  }
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $km = $miles * 1.609344;
  return (is_nan($km)) ? 0 : $km;
}

?>
