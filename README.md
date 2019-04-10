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

