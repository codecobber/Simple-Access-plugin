<?php


if(!empty($_POST)){
//Get the string value from XHR then create an array
if(isset($_POST['d'])){
  $opt = htmlentities($_POST['d']);
}
if(isset($_POST['m'])){
  $mainUser = htmlentities($_POST['m']);
}

$arr = explode(",",$opt);
array_pop($arr); //remove trailing comma

$usersDat = file_get_contents("../data/other/perms.json") or die("what! - no perms file");
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
file_put_contents("../data/other/perms.json",$juserDat);

    echo "<p id='changesSavedMsg'>Changes Saved</p>";

}


$filesPerms = "../data/other/";
$userDataGrab = file_get_contents($filesPerms."perms.json");
$jsonGrab = json_decode($userDataGrab);
$countItems = count($jsonGrab);

?>

<script>

var mainUser = "";

function getList(name){
  //grab all inputs from the checkboxes
  var chk = document.getElementsByClassName('checks');
  var lbl = document.getElementsByClassName('lbl');
  var nameLabel = document.getElementById(name+"3");
  var selectedUser = document.getElementById(name+"2");

  mainUser = name;

  //clear all inputs
  for (i = 0; i < chk.length; i++) {
    chk[i].checked = false;
    chk[i].style.visibility = 'visible';
  }

  //reset all labels
  for (j = 0; j < lbl.length; j++) {
    var cname = lbl[j].getAttribute('data-name');
    lbl[j].innerHTML = cname;
  }
  //set the selected user label to the following
  nameLabel.innerHTML = "<i class='far fa-check-square'></i> " + name + "<span> (auto selected)</span>";

  //style the labels
  $(document).ready(function(){
    $(".lbl").removeClass('nameLabel');
    $("#"+name+"3").addClass('nameLabel');
  });


  //check this specific checkbox for the user
  selectedUser.checked = true;
  selectedUser.style.visibility = 'hidden';

}


</script>


<?php
echo "
<table id='editsimpleAccess'>
  <tr>
    <th colspan='2'>
    <h3>Changing permissions</h3>
    </th>
  </tr>
  <tr>
    <td colspan='2'>
      <p>Select a user from the list of users in left hand column. The list also shows the current permissions for each user.<br>
      To add permissions for the selected user, select the desired checkboxes in the right hand column and then click the submit button.</p>
      <p>The permissions work by resetting the selected user to what ever is selected in the checkbox.<br>This means that if you want to remove specific permissions then you leave the checkbox unchecked.</p>
    </td>
  </tr>

  <tr>
    <td class='permsRow'>

            <p><br><i class='fas fa-user-circle'></i> <b>Select a user:</b></p>";

            for($i=0;$i<$countItems;$i++){
            ?>
              <p>
              <input onclick="getList(this.id)" id='<?php echo $jsonGrab[$i]->id; ?>' type='radio' name='opt1'/>
              <label><?php echo $jsonGrab[$i]->id; ?> </label>
              <br><b>Perms: </b> <?php echo $jsonGrab[$i]->category; ?>
              <br>. . . . .
              </p>
            <?php
            }
    echo "
    </td>
    <td class='permsRow'>
          <p><br><i class='fas fa-compass'></i> <b>Select permissions:</b></p>";

          for($i=0;$i<$countItems;$i++){
            //output all the checkboxes
            echo "<p>
            <input id='".$jsonGrab[$i]->id."2' class='checks' type='checkbox' name='opt' value='".$jsonGrab[$i]->id."' />
            <label class='lbl' id='".$jsonGrab[$i]->id."3' data-name = '".$jsonGrab[$i]->id."'>".$jsonGrab[$i]->id. "</label>
            </p>";
          }
          //output for the home page (index)
          echo "
          <p>
          <input id='index2' class='checks' type='checkbox' name='opt' value='index' />
          <label class='lbl' data-name ='index (Home page)'>index (Home page)</label>
          </p>

          <p>
          <input id='adminPlugPerms' class='checks' type='checkbox' name='opt' value='adminPlugPerms' />
          <label class='lbl' data-name ='adminPlugPerms'>Plugin admins</label>
          </p>
          <button onclick='grabChoices()'>Submit</button>
          <p id='confirmMessage'></p>
          <form id='updateForm' method = 'POST' action ='./load.php?id=simpleAccess&editperms'>
            <input id='updateInfom' type='hidden' value ='' name='m' />
            <input id='updateInfod' type='hidden' value ='' name='d' />
          </form>
    </td>
  </tr>
</table>";



?>

<script>

function grabChoices(){

  var choices = document.getElementsByName("opt");
  var checkboxesChecked = "";

  // loop over them
  for (var i=0; i<choices.length; i++) {
     // And the checked ones onto a string...
     if (choices[i].checked) {
        checkboxesChecked += choices[i].value + ",";
     }
  }

  document.getElementById('updateInfom').value = mainUser;
  document.getElementById('updateInfod').value = checkboxesChecked;
  document.getElementById('updateForm').submit();
  document.getElementById('confirmMessage').innerHTML = "Update Complete";

}

</script>
