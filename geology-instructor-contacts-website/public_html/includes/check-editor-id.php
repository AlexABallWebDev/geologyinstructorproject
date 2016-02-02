<?php
//assign editor_id to a variable named $editor_id. if user is not logged in, then $editor_id will not be set.
if (isset($_SESSION['editor_id']))
{
	//get database connection named $geologyDBConnection
	require('../secure-includes/db-connection.php');
	
	//define sql statement
	$checkEditorSQL = 'SELECT * FROM geology_instructor_editors WHERE editor_id = ' . $_SESSION['editor_id'];
	
	$checkEditorResult = mysqli_query($geologyDBConnection, $checkEditorSQL);
	
	//if the editor with this id exists, assign the id to a variable $editor_id.
	//otherwise, redirect to the login page.
	if ($checkEditorResult && mysqli_num_rows($checkEditorResult) == 1)
	{
		//assign editor id variable
		$editor_id = $_SESSION['editor_id'];
	}
	//close connection
	mysqli_close($geologyDBConnection);
}