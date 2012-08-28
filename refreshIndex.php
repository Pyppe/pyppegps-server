<?php

include "functions.php";

// First truncate the index-file
$indexFile = fopen($INDEX_FILE, "w");
fclose($indexFile);

if ($handle = opendir($KML_DIR)) {
  while (false !== ($file = readdir($handle))) {
    if (endsWith($file,".kml")) {
      echo "Indexing <b>".$file."</b><br />";
      addKMLToIndex($file);
	  }
  }
  closedir($handle);
}

?>
