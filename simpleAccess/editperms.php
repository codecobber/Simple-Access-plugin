<?php

$filesPerms = "../data/other/";

$userDataGrab = file_get_contents($filesPerms."perms.json");

$jsonGrab = json_decode($userDataGrab);
$countItems = count($jsonGrab);

?>

<script>

var mainUser = "";

function getList(name){
  //grab all inputs
  var chk = document.getElementsByClassName('checks');
  //clear all inputs
  for (i = 0; i < chk.length; i++) {
    chk[i].checked = false;
  }

  //check this specific checkbox
  document.getElementById(name+"2").checked = true;
  mainUser = name;
}

function grabChoices(){

  var choices = document.getElementsByName("opt");
  var checkboxesChecked = "";

  // loop over them
  for (var i=0; i<choices.length; i++) {
     // And stick the checked ones onto an array...
     if (choices[i].checked) {
        checkboxesChecked += choices[i].value + ",";
     }
  }


  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById('confirmMessage').innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "../plugins/simpleAccess/updatePerms.php?m="+mainUser+"&d="+checkboxesChecked, true);
  xhttp.send();

}

</script>

<?php
echo "
<table>
  <tr>
    <th style='padding-bottom:20px; text-align:center' colspan='2'>
    <h3>Changing permissions</h3>
    </th>
  </tr>

  <tr style='padding-top:20px;'>
    <td class='permsRow' style='padding-right:20px;'>


            <p><br><b>Select a user:</b></p>";

            for($i=0;$i<$countItems;$i++){
            ?>
              <p style="border-bottom:dotted 1px #e0dcdc;padding-bottom:10px;">
              <input onclick="getList(this.id)" id='<?php echo $jsonGrab[$i]->id; ?>' type='radio' name='opt1'/>
              <label style='margin-left:10px;top:-4px; position: relative; display:inline;'><?php echo $jsonGrab[$i]->id; ?> </label>
              <br><b>Perms: </b> <?php echo $jsonGrab[$i]->category; ?>
              </p>
            <?php
            }
    echo "
    </td>
    <td class='permsRow'>


          <p><br><b>Select permissions:</b></p>";

          for($i=0;$i<$countItems;$i++){

            echo "<p>
            <input id='".$jsonGrab[$i]->id."2' class='checks' type='checkbox' name='opt' value='".$jsonGrab[$i]->id."' />
            <label style='margin-left:10px;top:-4px; position: relative; display:inline;'>".$jsonGrab[$i]->id. "</label>
            </p>";
          }
          echo "
          <p>
          <input id='index2' class='checks' type='checkbox' name='opt' value='index' />
          <label style='margin-left:10px;top:-4px; position: relative; display:inline;'>index (Home page)</label>
          </p>
          <button onclick='grabChoices()'>Submit</button>
          <p id='confirmMessage'></p>
    </td>
  </tr>
</table>";

?>
