<?php

$filesPerms = "../data/other/perms.json";
$userDataGrab = file_get_contents($filesPerms);

$jsonGrab = json_decode($userDataGrab);
$countItems = count($jsonGrab);
echo "<div class='overView permsRow'><h2>Current user permissions</h2>";

for($i=0;$i<$countItems;$i++){
  echo "
  <p class='overViewStyle'><i class='fas fa-user-circle'></i><b> User: </b>".$jsonGrab[$i]->id. "</p>
  <p><i class='fas fa-compass'></i><b> Permissions: </b>".$jsonGrab[$i]->category."</p>";
}
echo "</div>";

?>
