<?php
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
	'plugins',
	'sidetab_show'
);

add_action('header-body','hideAll');
# activate filter
add_action('footer','checkPerms');

# add a link in the admin tab 'plugins'
add_action('plugins-sidebar','createSideMenu',array($thisfile,'Simple Access'));

add_action('edit-extras','editTest');


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




function editTest(){

	$user = get_cookie('GS_ADMIN_USERNAME');
	$queryString = $_SERVER['QUERY_STRING'];
	$queryString = str_replace("id=", "", $queryString.".xml");


	//open the current file
	$thisCurrentFile = file_get_contents(GSDATAPAGESPATH.$queryString);
	$file_XMLdata = simplexml_load_string($thisCurrentFile);
	$file_author = (string)$file_XMLdata->author;


    //check for edit.php by ascertaining if a question mark is used
	$uri = $_SERVER['REQUEST_URI'];
	$slash = strripos($uri, "/");
	$question = strripos($uri, "?");
	$pagename = "";


	if($question != false){

		/* ============================================================
			used for edit.php with a query string (an actual xml page)
		 	we add 4 to discount chars ?id= and give us the page name
		 ==============================================================*/

		$pagename = substr($uri, $question+4);

		//the index page does not have a author tag so we need to specify the users allowed to edit this page

		/*===================================================================================================
		* CHANGE BELOW!!!!
		* ----------------
		* Change the $user values below according to your required login preferences.
		* There are two default settings which you can delete or edit.
		* One is for the user name 'my_login_name' and the other is 'admings'
		* EXAMPLE: Change the value of $user (on line 98 below) to your own login name.
		* If you keep the admings then it is best to create a user account for that too.
		*. . . the choice is yours
		* ===================================================================== */

		//this checks for the index page as the index page does not possess an author tag in xml

		if($pagename == 'index' && $user == 'cobber' || $pagename == 'index' && $user == 'another_admin'){
			echo "<small>".$user . "- Access allowed.</small>";
		}
		elseif($user != $file_author){
			// check author against logged in user and replace content with message

			echo "<script>
			document.getElementsByClassName('main')[0].innerHTML = '<h1 style=\'color:#d43b3b;font-size:30px\'><i class=\"fas fa-ban\"></i> Access Denied!</h1><p>You do not have permission to view or edit this page</p>';
		    </script>";
		}

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

	$PA_current_user = get_cookie('GS_ADMIN_USERNAME');

	$dir_handle = @opendir(GSDATAPAGESPATH) or exit('Unable to open ...getsimple/data/pages folder');
	$PA_filenames = array(); // holds the pages list from the pages folder

	//read file from directory
	while (false !== ($PA_filename = readdir($dir_handle))) {
			$PA_filenames[] = $PA_filename;
	}

	//get user perms
	$user_perms = file_get_contents(GSDATAOTHERPATH."perms.json");
	$json_perms = json_decode($user_perms);
	$user_permsarray = "";

	foreach($json_perms as $perms_item){

		  if($perms_item->id == $PA_current_user){
					//now get the $perms
					$user_permsarray = $perms_item->category;
			}
  }







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

		      if(in_array($PA_author,$user_permsarray)){
						echo "Oh yeah!";
					}

					if($PA_url == "index" && $PA_current_user  == "cobber" || $PA_author == $PA_current_user || $PA_current_user == 'your_login_name' || $PA_current_user == 'another_admin'){
						// check if the page author matches cobber or other
						// user specified or the current logged in user
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
		//return $PA_pages;


}

function sidetab_show() {
	echo "<h3>Hide pages depending on the user logged in. </h3>
	<p>The plugin reads from the 'author' tag of each page to obtain the author of the page.</p>
	<p>If the the author value does not match the logged in user then the page entry is hidden from the pages listing in pages.php.</p>
	<p>If the user tries to access a specific page by changing the url at the address bar then the content is removed and they are informed
	accordingly that they don not have permission to edit the page";
}


?>
