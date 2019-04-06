
<div class="resetStyle"><h3><i class='fas fa-exclamation-triangle'></i> CAUTION!!!</h3>
<p>Clicking the reset button will reset <strong>ALL</strong> of the user permissions back to the default setting.</p>
<p><b>The default setting is as follows:</b><br> Each user has access <b>ONLY</b> to documents created by that user.</p>
<p>Once reset, use the 'edit perms' button to add any new permissions to each account.'</p>
<hr>
</div>

<?php
if(!empty($_POST) && htmlentities($_POST['m']) == '1'){
  include('../plugins/simpleAccess/lastChance.php');
}
?>


<p id="confirmResetMessage"></p>

<form id="resetForm" action="./load.php?id=SimpleAccess&reset" method="POST">
  <input type='hidden' value ='1' name='m' />
  <button type = 'submit'>Reset <strong>ALL</strong> user permissions</button>
</form>
