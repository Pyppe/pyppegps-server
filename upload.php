<?php

include "functions.php";

$fileArray = $_FILES['data'];
$description = $_POST['description'];

if (empty($description) || empty($fileArray['name'])) {
  echo "Error with upload. Please provide file description.";
} else {
  $resource = fopen("log.txt","a");
  fwrite($resource, getDateString()." Received file ".$fileArray['name']." ($description)\n");
  fclose($resource);
  echo do_upload("./kml", $description, $fileArray);
}

/* START OF FUNCTIONS */

function getDateString() {
  return date("Y-m-d\Th:i:s");
}

function do_upload($upload_dir, $fileDescription, $fileArray) {
  $temp_name = $fileArray['tmp_name'];
  $filename = $fileArray['name'];
  $fileDescription = str_replace("\\","",$fileDescription);
  $fileDescription = str_replace("'","",$fileDescription);
  $fileDescription = str_replace("/","",$fileDescription);
  $fileDescription = str_replace("|","",$fileDescription);
  $fileDescription = str_replace(" ","_",$fileDescription);
  $savedFilename = str_replace(".kml","",$filename)."_".$fileDescription.".kml";
  $file_path = $upload_dir."/".$savedFilename;
  
  if (file_exists($file_path)) {
    return "Error uploading. File already exists.";
  }
  
  if (!isValidFilename($filename)) {
    return "Error uploading. $filename is not a valid filename."  ;
  }
  
  $success = move_uploaded_file($temp_name, $file_path);
  if (!chmod($file_path,0777)) {
    $message = "Error setting permissions.";
  } else {
    if ($success) {
      addKMLToIndex($savedFilename);
      $message = "File successfully uploaded.";
    } else {
      $message = "Error uploading.";
    }
  }
  return $message;
}

function isValidFilename($filename) {
  if (strlen($filename) != 18) {
    return false;
  }
  $date = substr($filename, 0, 14);
  if (preg_match('/^[0-9]+$/', $date) === 1) {
    return true;
  } else {
    return false;
  }
}


?>
