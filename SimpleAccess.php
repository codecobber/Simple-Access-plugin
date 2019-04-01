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
	'1.0',
	'Code Cobber',
	'https://www.codecobber.co.uk/',
	'Restrict user access for certain pages',
	'simple_access',
	'simple_access_show'
);

//$GLOBALS
$pageEdited = "";
$user = get_cookie('GS_ADMIN_USERNAME');
$userFlag = 0;


add_action('header-body','hideAll');
add_action('footer','checkPerms');
add_action('edit-extras','editTest');
add_action('changedata-aftersave','aftersave');
add_action('header','setUser');

# add a link in the admin tab 'simple_access'
//@Params(within the plugins sidebar, create a side menu, (link to this file, use this text as title))
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-tag" aria-hidden="true"></i> About', 'aboutsa'));
add_action('nav-tab', 'createNavTab', array( 'simple_access', $thisfile, 'Simple Access','overview' ) );
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-eye" aria-hidden="true"></i> Overview', 'overview'));
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-users" aria-hidden="true"></i> Edit perms', 'editperms'));
add_action('simple_access-sidebar', 'createSideMenu', array($thisfile, '<i class="fa fa-tag" aria-hidden="true"></i> Reset users perms', 'reset'));


function makeList(){
	$jdata = array();

	$files = "../data/users/";
	$userFiles = scandir($files);

	foreach($userFiles as $ausr){
		if($ausr == "." || $ausr == ".."){
			continue;
		}
			//
			$name = str_ireplace(".xml","",$ausr);
			$user = array("id" => $name, "category" => $name);
			array_push($jdata,$user);
			echo $user['id']."<br>";
	}


	$jdata = json_encode($jdata,JSON_PRETTY_PRINT);
 	file_put_contents(GSDATAOTHERPATH."perms.json",$jdata);
}


function simple_access_show() {

	if(isset($_GET['overview'])){
		include(GSPLUGINPATH.'simpleAccess/overview.php');
	}
	elseif(isset($_GET['reset'])){
		makeList();
	}
	elseif(isset($_GET['editperms'])){
		include(GSPLUGINPATH.'simpleAccess/editperms.php');
	}
	elseif(isset($_GET['aboutsa'])){
		include(GSPLUGINPATH.'simpleAccess/about.php');
	}
}

function setUser(){
	$GLOBALS['user'] = get_cookie('GS_ADMIN_USERNAME');
}

function hideAll(){
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


function getUserPerms(){
	$user = $GLOBALS['user'];
	//get user perms
	$user_perms = file_get_contents(GSDATAOTHERPATH."perms.json");
	$json_perms = json_decode($user_perms);
	$user_permsarray = "";

	foreach($json_perms as $perms_item){

		  //match logged in user to the id within json object
			if($perms_item->id == $user){
					//now get the $perms as a string
					$user_permsarray = $perms_item->category;
					//pass back the array
					return $user_permsarray;
			}
	}
}

function afterSave(){
  $pagePath = GSDATAPAGESPATH.$_SESSION['pageName'];
	$xmlDoc = new DOMDocument();
  $xmlDoc->load($pagePath);

	$oldNode = $xmlDoc->getElementsByTagName("author")->item(0);
	$authorTxt = $_SESSION['fileAuth'];

	$oldNode->nodeValue = "";
	$oldNode->appendChild($xmlDoc->createCDATASection($authorTxt));
	$xmlDoc->save($pagePath);
}


function protectPage($pageAuthor){
	// A little output to show the file author
	echo "<b>File author: </b>".$pageAuthor;

	// call function to get user permissions
	$user_permsarray = getUserPerms();
	if(stripos($user_permsarray,$pageAuthor)!==false){
			echo " - Access granted.";
	}
	else{
		// check author against logged in user and replace content with message
		echo "<script>
		document.getElementsByClassName('main')[0].innerHTML = '<h1 style=\'color:#d43b3b;font-size:30px\'><i class=\"fas fa-ban\"></i> Access Denied!</h1><p>You do not have permission to view or edit this page</p>';
			</script>";
	}
}


function getFileData($fname,$flag=0){
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


function editTest(){

	$user = $GLOBALS['user'];
	$queryString = $_SERVER['QUERY_STRING'];

	// Check if the query string holds an ampersand '&' -
	// meaning it's an save edit page

  $ampSearch = stripos($queryString,"&");

  // generate the page name from query string
	if($ampSearch != false){

		$queryString = str_replace("id=", "", $queryString);
		$queryString = substr($queryString,0,$ampSearch-3).".xml";

		$pa = getFileData($queryString);
		protectPage($pa);
	}
	elseif($queryString == "" || is_null($queryString)){
		echo "<b>File author</b>: ".$user;
	}
	else{
		$queryString = str_replace("id=", "", $queryString.".xml");
		getFileData($queryString,1);
		// check page access
		protectPage($_SESSION['fileAuth']);
	}
}

function showMe($pg){
	//display the row within pages.php allowing the user to see the page name and edit button
		echo "<script>
			document.getElementById('tr-".$pg."').style.display = 'table-row';
		</script>";
}


function hideMe($pg){
	//remove the listing (row) from pages.php
		echo "<script>
			document.getElementById('tr-".$pg."').style.display = 'none';
		</script>";
}


function checkPerms(){

	// Get user logged include '
	$userFlag = 0;

	$PA_current_user = $GLOBALS['user'];

	$dir_handle = @opendir(GSDATAPAGESPATH) or exit('Unable to open ...getsimple/data/pages folder');
	$PA_filenames = array(); // holds the pages list from the pages folder

	//read file from directory
	while (false !== ($PA_filename = readdir($dir_handle))) {
			$PA_filenames[] = $PA_filename;
	}
    // call function to get user permissions
		$user_permsstring = getUserPerms();

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
						$GLOBALS['userFlag'] = 0;
					}
					else{
						//if page not registered then set error flag to 1
						$GLOBALS['userFlag'] = 1;
					}


					//Check the flag setting- if 1 then hide current page file
					if($GLOBALS['userFlag'] == 1){
						hideMe($PA_url);
						$GLOBALS['userFlag'] == 0; //reset flag for next page
					}
					else{
						showMe($PA_url);
						$GLOBALS['userFlag'] == 0;
					}
				}

			}
		}
}




?>
