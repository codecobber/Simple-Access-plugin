<?php

//Get the string value from XHR then create an array
$opt = $_GET['d'];
$mainUser = $_GET['m'];
$arr = explode(",",$opt);
array_pop($arr); //remove trailing comma

$usersDat = file_get_contents("../../data/other/perms.json") or die("what!");
$juserDat = json_decode($usersDat);
$arrUpdate = array();



foreach($juserDat as $user_item){

    //match logged in user to the id within json object
    if($user_item->id == $mainUser){

      $cats = "";

      foreach($arr as $x){
          $cats .= " ".$x;
      }

      $cats = ltrim($cats);
      $mainUserArr = array("id" => $user_item->id, "category" => $cats);
      array_push($arrUpdate,$mainUserArr);
    }
    else{

      $normData = array("id" => $user_item->id, "category" => $user_item->category);
      array_push($arrUpdate,$normData);
    }
}

$juserDat = json_encode($arrUpdate,JSON_PRETTY_PRINT);
$juserDat = strtolower($juserDat);
file_put_contents("../../data/other/perms.json",$juserDat);

echo "<strong style='position:relative; top:3em; background-color:green;color:#fff;padding:10px;font-size:1.6em'>Update Complete.</strong>";

?>
