<?php

function makeList(){
	$jdata = array();

	$files = "../../data/users/";
	$userFiles = scandir($files);

	foreach($userFiles as $ausr){
		if($ausr == "." || $ausr == ".."){
			continue;
		}
			//
			$name = str_ireplace(".xml","",$ausr);
			$user = array("id" => $name, "category" => $name);
			array_push($jdata,$user);
			echo "<b>".$user['id']."</b> -- Reset<br>";
	}


	$jdata = json_encode($jdata,JSON_PRETTY_PRINT);
 	file_put_contents("../../data/other/perms.json",$jdata) or die("Bummer!!!");
}

makeList();

 ?>
