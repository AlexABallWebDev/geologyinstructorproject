<?php
//logout script: destroys session, then redirects to the home page.

//start session so we can access the session array
session_start();

//clear the session array; with no user_id, the user is
//no longer logged in.
session_unset();

//destroy the session
session_destroy();

//redirect the homepage (index page)
header('location: index.php');
?>