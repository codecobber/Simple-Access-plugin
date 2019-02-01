# Simple-Access-plugin
Hide pages depending on the user logged in.

The plugin reads from the 'author' tag of each page to obtain the author of the page.

If the the author value does not match the logged in user then the page entry is hidden from the pages listing in pages.php.

If the user tries to access a specific page by changing the url at the address bar then the content is removed and they are informed accordingly that they don not have permission to edit the page

Change the $user values according to your required login preferences.
There are two default settings which you can delete or edit.
One is for the user name 'my_login_name' and the other is 'admings'
EXAMPLE: Change the value of $user (on line 97 below) to your own login name.
If you keep the admings then it is best to create a user account for that too.
. . . the choice is yours 
