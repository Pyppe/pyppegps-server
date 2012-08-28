<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fi" xml:lang="fi">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PyppeGPS - KML repository</title>
    <link rel="stylesheet" type="text/css" href="assets/css/page.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/demo_table_jui.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/colorbox.css"/>
    <link rel="stylesheet" type="text/css" href="assets/themes/smoothness/jquery-ui-1.7.2.custom.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />
    <script type="text/javascript" language="javascript" src="assets/js/jquery.js"></script>
    <script type="text/javascript" language="javascript" src="assets/js/jquery.dataTables.js"></script>
    <script type="text/javascript" language="javascript" src="assets/js/jquery.colorbox-min.js"></script>
    <script type="text/javascript">
    //<!--
      $(document).ready(function() {
        $(".colorbox-link").colorbox({width:"80%", height:"80%", iframe:true});
        $(".embedded-kml").click(function () {
          $(this).parent().next(".colorbox-link").click();
        });
        
        var dataTable = $('#gps-table').dataTable({
          "bJQueryUI": true,
          "sPaginationType": "full_numbers",
          "bAutoWidth": false,
          "aoColumns" : [
            { sWidth: '140px', sType: 'string' },
            { sWidth: '230px', sType: 'html' },
            { sWidth: '180px', sType: 'string' },
            { sWidth: '130px', sType: 'numeric' },
            { sWidth: '170px', sType: 'numeric' }
          ]
        });
        dataTable.fnSort( [ [0,'desc'] ] );
        $("#gps-table thead th:first").addClass("selected");
        
        $("#gps-table thead th").click(function() {
          $("#gps-table thead th").removeClass("selected");
          $(this).addClass("selected");
        });
	  });
    // -->
	  </script>
  </head>
  <body>
    <div id="container">
      <h1><img src="assets/images/logo25.png" /> PyppeGPS - KML repository</h1>
      <table id="gps-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Description</th>
            <th>Duration (hh:mm:ss)</th>
            <th>Distance (km)</th>
            <th>Avg. speed (km/h)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $file = file("index.txt");
          foreach ($file as $lineNumber => $line) {
            $fileRow = explode("|", $line);
            $kmlUrl = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/kml/".$fileRow[0];
            $encodedKmlUrl = urlencode($kmlUrl);
            $description = getDescription($fileRow[0]);
            $time = getTimeString($fileRow[0]);
            $duration = $fileRow[1];
            $distance = $fileRow[2];
            $speed = (!empty($duration) && !empty($distance)) ? round($distance / ($duration/60/60),1) : "";
            echo "<tr>";
            echo "<td>$time</td>";
            echo "<td>";
            echo "  <span style='float: right;'>";
            echo "    <img class='embedded-kml' src='assets/images/action-view-fullscreen.png' title='View map in modal' />";
            echo "    <a href='http://maps.google.com/?q=$encodedKmlUrl' target='_blank' title='View map in new window'><img src='assets/images/action-window-new.png' /></a>";
            echo "    <a href='$kmlUrl' title='Download KML'><img src='assets/images/action-document-save.png' /></a>";
            echo "  </span>";
            echo "  <a title='$description ($time)' class='colorbox-link' href='http://maps.google.com/?q=$encodedKmlUrl&output=embed'>".$description."</a>";
            echo "</td>";
            echo "<td>".secondsToHMS($duration)."</td>"; // duration
            echo "<td>$distance</td>";
            echo "<td>$speed</td>";
            echo "</tr>";
          }
        ?>
        </tbody>
      </table>
    </div>
  </body>
</html>

<?php

function secondsToHMS($sec, $padHours = true) {
  if (empty($sec)) {
    return "";
  }
  $hours_min_secs = "";
  $hours = intval(intval($sec) / 3600); 
  $hours_min_secs .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':' : $hours. ':';
  $minutes = intval(($sec / 60) % 60); 
  $hours_min_secs .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
  $seconds = intval($sec % 60); 
  $hours_min_secs .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
  return $hours_min_secs;
}

function getTimeString($filename) {
  $year = substr($filename,0,4);
  $month = substr($filename,4,2);
  $day = substr($filename,6,2);
  $hour = substr($filename,8,2);
  $minute = substr($filename,10,2);
  $second = substr($filename,12,2);
  return $year."-".$month."-".$day." ".$hour.":".$minute;
}

function getDescription($filename) {
  if (strlen($filename) > 21) {
    $description = str_replace(".kml","",substr($filename,15));
    return str_replace("_", " ", $description);
  } else {
    return "";
  }
}

?>
