<?php

session_start();

/*
Plugin Name: Simple Access
Description: Restrict user access for certain pages
Version: 1.0
Author: Code Cobber
Author URI: https://www.codecobber.co.uk/
*/




# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,
	'Simple Access',
	'1.2',
	'Code Cobber',
	'https://www.codecobber.co.uk/',
	'Restrict user access for certain pages',
	'simple_access',
	'simple_access_show'
);



//$GLOBALS

$SA_user = get_cookie('GS_ADMIN_USERNAME');
$SA_userFlag = 0;
$SA_showHidePath = basename($_SERVER['SCRIPT_NAME']);

add_action('header-body','SA_hideAll');
add_action('footer','SA_checkPerms');
add_action('edit-extras','SA_editTest');
add_action('changedata-aftersave','SA_aftersave');
add_action('header','SA_setUser');

# add a link in the admin tab 'simple_access'
//@Params(within the plugins sidebar, create a side menu, (link to this file, use this text as title))
add_action('nav-tab', 'createNavTab', array( 'simple_access', $thisfile, 'Simple Access','overview' ) );
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-tag" aria-hidden="true"></i> About', 'aboutsa'));
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-eye" aria-hidden="true"></i> Overview', 'overview'));
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-users" aria-hidden="true"></i> Edit perms', 'editperms'));
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fas fa-skull" aria-hidden="true"></i> Reset users perms', 'reset'));

register_style('simpleAccess_style', $SITEURL.'plugins/simpleAccess/css/simpleAccessCSS.css', '0.1', 'all');
queue_style( 'simpleAccess_style' , GSBACK ) ;

// setting the access interface (Below) --------------------------


function simple_access_show() {

	if(isset($_GET['overview'])){

				//check for perms.json
				if (file_exists(GSDATAOTHERPATH . "perms.json")) {
		    echo "<p id='permsCreated'>Reading from perms file</p>";
				}
				else {
					// if json not found then create the file
					$installData = array();

					$install_files = "../data/users/";
					$install_userFiles = scandir($install_files) or die('Problem scannig user file.');


				  foreach($install_userFiles as $install_ausr){

						$install_ausr = strtolower($install_ausr);

						if($install_ausr == "." || $install_ausr == ".."){
							continue;
						}

							$install_name = str_ireplace(".xml","",$install_ausr);
							$install_name = strtolower($install_name);
							$install_user = array("id" => $install_name, "category" => $install_name);
							array_push($installData,$install_user);
					}

					$installData = json_encode($installData,JSON_PRETTY_PRINT);
					file_put_contents('../data/other/perms.json',$installData)or die('Write problem to json');
					echo "<p id='permsCreated'>Perms file created</p>";
				}
				include(GSPLUGINPATH.'simpleAccess/overview.php');
	}
	elseif(isset($_GET['reset'])){
		include(GSPLUGINPATH.'simpleAccess/reset.php');
	}
	elseif(isset($_GET['editperms'])){
		include(GSPLUGINPATH.'simpleAccess/editPerms.php');
	}
	elseif(isset($_GET['aboutsa'])){
		include(GSPLUGINPATH.'simpleAccess/about.php');
	}
}


// setting the access rights (Below) --------------------------

function SA_setUser(){
	$GLOBALS['SA_user'] = get_cookie('GS_ADMIN_USERNAME');
	echo '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">';
}

function SA_hideAll(){
	//Search for attribute that starts with tr- within the pages.php page
	//Hide all relevant table rows from the start.
	$uri = $_SERVER['REQUEST_URI'];
	$slash = strripos($uri, "/");
	$pagename = substr($uri, $slash+1);


	if ($pagename == "pages.php"){
		echo "<style>
			[id^='tr-']{
				display:none;
			}
		</style>";
	}
}


function SA_getUserPerms(){
	$user = $GLOBALS['SA_user'];
	//get user perms
	$user_perms = file_get_contents(GSDATAOTHERPATH."perms.json");
	$json_perms = json_decode($user_perms);
	$user_permsstring = "";

	foreach($json_perms as $perms_item){

		  //match logged in user to the id within json object
			if($perms_item->id == $user){
					//now get the $perms as a string
					$user_permsstring = $perms_item->category;
					//pass back the array
					return strtolower($user_permsstring);
			}
	}
}

function SA_afterSave(){
  $pagePath = GSDATAPAGESPATH.$_SESSION['pageName'];
	$xmlDoc = new DOMDocument();
  $xmlDoc->load($pagePath);

	$oldNode = $xmlDoc->getElementsByTagName("author")->item(0);
	$authorTxt = $_SESSION['fileAuth'];

	$oldNode->nodeValue = "";
	$oldNode->appendChild($xmlDoc->createCDATASection($authorTxt));
	$xmlDoc->save($pagePath);
}


function SA_protectPage($pageAuthor,$url=""){
	// A little output to show the file author
	echo "<b>File author: </b>".$pageAuthor;

	// call function to get user permissions
	$user_permsString = SA_getUserPerms();
	if(stripos($user_permsString,$pageAuthor)!==false){
			echo " - Access granted.";
	}
	elseif(stripos($user_permsString,'index') !== false && $url == 'index.xml'){
		echo " - Access granted.";
	}
	else{
		// check author against logged in user and replace content with message
		echo "<script>
		document.getElementsByClassName('main')[0].innerHTML = '<h1 style=\'color:#d43b3b;font-size:30px\'><i class=\"fas fa-ban\"></i> Access Denied!</h1><p>You do not have permission to view or edit this page</p>';
			</script>";
	}
}


function SA_getFileData($fname,$flag=0){
	//open the current file and set session variables

	$thisCurrentFile = file_get_contents(GSDATAPAGESPATH.$fname) or die("bummer! - No File man.");
	$file_XMLdata = simplexml_load_string($thisCurrentFile);
	$file_author = (string)$file_XMLdata->author;

	if($flag == 1){
		$_SESSION['fileAuth'] = $file_author;
		$_SESSION['pageName'] = $fname;
	}

	return $file_author;
}


function SA_editTest(){

	$user = $GLOBALS['SA_user'];
	$queryString = $_SERVER['QUERY_STRING'];

	// Check if the query string holds an ampersand '&' -
	// meaning it's an save edit page

  $ampSearch = stripos($queryString,"&");

  // generate the page name from query string
	if($ampSearch !== false){
    //if ampersand is found in querystring
		$queryString = str_replace("id=", "", $queryString);
		$queryString = substr($queryString,0,$ampSearch-3).".xml";

		$pa = SA_getFileData($queryString); //get the page author
		SA_protectPage($pa,$queryString);
	}
	elseif($queryString == "" || is_null($queryString)){
		//checking if the page is create new page
		echo "<b>File author</b>: ".$user;
	}
	else{
		$queryString = str_replace("id=", "", $queryString.".xml");
		$check = SA_getFileData($queryString,1);
		// check page access
		SA_protectPage($_SESSION['fileAuth'],$queryString);
	}
}

function SA_showMe($pg){
	if($GLOBALS['SA_showHidePath']=="pages.php"){
	//display the row within pages.php allowing the user to see the page name and edit button
		echo "<script>
			document.getElementById('tr-".$pg."').style.display = 'table-row';
		</script>";
	}
}


function SA_hideMe($pg){

	//check for i18N
	$loadPath = "id=i18n_base";
	$loadPathNav = "id=i18n_navigation";


	if($GLOBALS['SA_showHidePath']=="pages.php" || $SERVER['QUERY_STRING'] == $loadPath || $SERVER['QUERY_STRING'] ==$loadPath2){

		//remove the listing (row) from pages.php
			echo "<script>
				document.getElementById('tr-".$pg."').style.display = 'none';
			</script>";
	}
}

function SA_updateUsers(){
	$updateUsers = scandir(GSDATAPATH."users") or die('No users file found');
	$updateJSON_Users = file_get_contents(GSDATAOTHERPATH."perms.json") or die('No perms file found');

  $perms_users = json_decode($updateJSON_Users);
	$pcount = count($perms_users);

		foreach ($updateUsers as $ukey => $uvalue) {
			$fname = str_replace(".xml","",$uvalue);
			$fname = strtolower($fname);

			if($fname == '.' || $fname == '..' || $fname == '.htaccess'){
				continue;
			}
			else{
				for($i=0;$i<$pcount;$i++) {
					$pname = strtolower($perms_users[$i]->id);

					if($fname == $pname){
						$flag = 0;
						break;
					}
					else{
						$flag = 1;
					}
				}
				if($flag == 1){
					$new_user = array("id" => $fname, "category" => $fname);
					array_push($perms_users,$new_user);
				}
			}
			$flag = 0;
		}

		$jdata = json_encode($perms_users,JSON_PRETTY_PRINT);
	 	file_put_contents("../data/other/perms.json",$jdata) or die("Bummer!!!");
}




function SA_checkPerms(){

	SA_updateUsers();

	// Get user logged include '
	$SA_userFlag = 0;

	$PA_current_user = $GLOBALS['SA_user'];

	$dir_handle = @opendir(GSDATAPAGESPATH) or exit('Unable to open ...getsimple/data/pages folder');
	$PA_filenames = array(); // holds the pages list from the pages folder

	//read file from directory
	while (false !== ($PA_filename = readdir($dir_handle))) {
			$PA_filenames[] = $PA_filename;
	}
    // call function to get user permissions
		$user_permsstring = SA_getUserPerms();


		if (count($PA_filenames) != 0)
		{
			sort($PA_filenames);

			//Get data from each file
			foreach ($PA_filenames as $PA_file)
			{
				if (!($PA_file == '.' || $PA_file == '..' || is_dir(GSDATAPAGESPATH.$PA_file) || $PA_file == '.htaccess'))
				{
					$thisfile = file_get_contents(GSDATAPAGESPATH.$PA_file);
					$PA_XMLdata = simplexml_load_string($thisfile);
					$PA_url = (string)$PA_XMLdata->url;
					$PA_title = (string)$PA_XMLdata->title;
					$PA_author = (string)$PA_XMLdata->author;


					// Check the array and see if the page author is present
		      if(stripos($user_permsstring,$PA_author)!==false){
						$GLOBALS['SA_userFlag'] = 0;
					}
					elseif(stripos($user_permsstring,'index') !== false && $PA_url == 'index'){
						//this is the home page (index.xml)
							$GLOBALS['SA_userFlag'] = 0;
					}
					else{
						//if page not registered then set error flag to 1
						$GLOBALS['SA_userFlag'] = 1;
					}


					//Check the flag setting- if 1 then hide current page file
					if($GLOBALS['userFlag'] == 1){
						SA_hideMe($PA_url);
						$GLOBALS['SA_userFlag'] == 0; //reset flag for next page
					}
					else{
						SA_showMe($PA_url);
						$GLOBALS['userFlag'] == 0;
					}
				}

			}
		}
}




?>
