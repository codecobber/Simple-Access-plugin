<?php

function makeList(){
	$jdata = array();

	$files = "../../data/users/";
	$userFiles = scandir($files);

  echo "<h3 style='margin-top:2em;'>The following have been reset to their default setting:</h3>";

  foreach($userFiles as $ausr){

		$ausr = strtolower($ausr);

		if($ausr == "." || $ausr == ".."){
			continue;
		}
			//
			$name = str_ireplace(".xml","",$ausr);
			$name = strtolower($name);
			$user = array("id" => $name, "category" => $name);
			array_push($jdata,$user);
			echo "<b><i class='fas fa-user-circle'></i> ".$user['id']."</b><br>";
	}



	$jdata = json_encode($jdata,JSON_PRETTY_PRINT);
 	file_put_contents("../../data/other/perms.json",$jdata) or die("Bummer!!!");
}

makeList();

 ?>
