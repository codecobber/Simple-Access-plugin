<script>

function makeList(){

  var xhttp2 = new XMLHttpRequest();
  xhttp2.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById('confirmResetMessage').innerHTML = this.responseText;
    }
  };
  xhttp2.open("GET", "../plugins/simpleAccess/lastChance.php", true);
  xhttp2.send();

}
</script>



<?php
echo "<h3>CAUTION!!!</h3>
<p>Clicking the reset button will reset <strong>ALL</strong> of the user permissions back to the default setting.</p>
<p><b>The default setting is as follows:</b><br> Each user has access <b>ONLY</b> to documents created by that user. Once reset, you will have to use the 'edit perms' button to add any new permissions to each account.'</p>
";
?>
<p id="confirmResetMessage"></p>
<button onclick="makeList()">Reset <strong>ALL</strong> user permissions</button>
