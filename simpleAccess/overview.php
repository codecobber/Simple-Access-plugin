<?php

$filesPerms = "../data/other/perms.json";
$userDataGrab = file_get_contents($filesPerms);

$jsonGrab = json_decode($userDataGrab);
$countItems = count($jsonGrab);
echo "<div class='permsRow'><h2>Current user permissions</h2>";

for($i=0;$i<$countItems;$i++){
  echo "<p style='border-bottom:dotted #ccc 1px;padding-bottom:7px;'><b>User: </b>".$jsonGrab[$i]->id. "<br><b>Permissions: </b>".$jsonGrab[$i]->category."</p>";
}
echo "</div>";

?>
