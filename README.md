# Simple Access Plugin
An updated version from the original and much simpler.

Depending on the permissions set for each user, users can access and edit all pages created by another user.
The plugin does NOT allow access the targeting of specific pages per se but rather whole collections created by another user. 

**A good example would be:**

User 'John' has created three pages.
User 'Sally' is granted access to pages created by user 'John'.

So, everything John created is available to Sally.

The 'Simple Access' plugin hide pages depending on the user logged in. The plugin obtains the author of each page and if the author value does not match the logged in user or does not possess the correct permissions, then the page entry is removed from the pages listing in pages.php.

The same priciple is also applied to pages in edit.php too

PLEASE NOTE: To add users to the GetSimple cms, I suggest installing the multi user plugin available from GetSimple addons Get Multi User


There are four sidebar menu items within the plugin:
 - About
 - Overview
 - Edit perms
 - Reset users perms
 
 
 About:
 ======
 
 Provides basic info about the plugin.
 
 
 Overview:
 ======
 
 Shows users and their current permissions.
 
 
 Edit perms:
 ======
 
 Allows the admin to add or remove permissions for each user.
 
 When selecting a user from the list of users on the left hand side,
 the same user is also highlighted on the permissions section 
 (located on the right). Below each user on the left is a list of 
 current permissions for that user.
 
 All other check boxes are unticked. 
 
 Once saved (by clicking the save button), Whatever permission is ticked 
 (checked) on the right will be saved to file for that user. 
 Any previous settings will be ignored and overwritten.
 
 
 Reset users perms:
 ======
 
 Resetting the users will set each user back to the default.
 Each user will only have access to each page they created.
 The current loggedin admin will be reset as the only author for the
 plugin. More can be added later though with the Edit menu item.
