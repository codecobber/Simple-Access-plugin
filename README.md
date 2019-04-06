# Simple-Access-plugin
Hide pages depending on the user logged in.

The plugin reads from the 'author' tag of each page to obtain the author of the page.

If the the author value does not match the logged in user or does not possess the correct permissions then the page entry is hidden from the pages listing in pages.php.

If the user tries to access a specific page by changing the url at the address bar then the content is removed and they are informed accordingly that they don not have permission to edit the page.
