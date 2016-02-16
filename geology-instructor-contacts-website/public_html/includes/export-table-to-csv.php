<?php
//get database connection named $geologyDBConnection
require "../secure-includes/db-connection.php";

$sql = 'SELECT * FROM geology_instructor_contacts ORDER BY last_name';

if ($result = @mysqli_query($geologyDBConnection, $sql))
{
	//open or create the file to be exported to. The previous file, if it exists,
	//will be overwritten with the current data from the database.
	$targetExportFile = fopen('downloads/geology-instructor-contact-table.csv', 'w');
	
	$columnHeaderArray = array();
	$columnHeaderArray[] = 'First Name';
	$columnHeaderArray[] = 'Last Name';
	$columnHeaderArray[] = 'Email';
	$columnHeaderArray[] = 'Phone Number';
	$columnHeaderArray[] = 'State';
	$columnHeaderArray[] = 'City';
	$columnHeaderArray[] = 'Zip';
	$columnHeaderArray[] = 'Address Line 1';
	$columnHeaderArray[] = 'Address Line 2';
	$columnHeaderArray[] = 'Institution';
	$columnHeaderArray[] = 'Department';
	$columnHeaderArray[] = 'Department Website';
	$columnHeaderArray[] = 'Individual Website';
	$columnHeaderArray[] = 'Instructor Primary Title';
	$columnHeaderArray[] = 'Campus';
	$columnHeaderArray[] = 'State Editor Privileges';
	
	//write the column header row to the file
	fputcsv($targetExportFile, $columnHeaderArray);
	
	//for each entry, add it to the csv file
	foreach($result as $row)
	{
		//check the editor table for an editor that has the same email as this instructor.
		//if such an editor exists, show that editor's state privilege (super or a state)
		$getEditorPrivilegeSQL = 'SELECT editor_state, super_user FROM
					geology_instructor_editors WHERE email = \'' . $row['email'] . '\'';
		
		$editorPrivilegesResults = mysqli_query($geologyDBConnection, $getEditorPrivilegeSQL);
		
		//if there is no result, this instructor's privileges column will say "none".
		$showPrivileges = 'None';
		
		//since email is a unique field in the editor database, there will only be 1 or 0 results.
		//if there is a result, check what type of privileges the editor has.
		if ($editorPrivilegesResults && mysqli_num_rows($editorPrivilegesResults) == 1)
		{
			//get the one row
			$privRow = mysqli_fetch_assoc($editorPrivilegesResults);
			
			//if editor is super editor, then the instructor's editor
			//privilege will be shown as "super".
			if ($privRow['super_user'] == 1)
			{
				$showPrivileges = 'Super';
			}
			//if editor is state editor, then the instructor's editor
			//privilege will be shown as the state that this editor has
			//privileges for.
			else if ($privRow['editor_state'] != 'Not a State Editor')
			{
				$showPrivileges = $privRow['editor_state'];
			}
		}
									
		//put elements into an array
		$exportArray = array();
		$exportArray[] = $row['first_name'];
		$exportArray[] = $row['last_name'];
		$exportArray[] = $row['email'];
		$exportArray[] = $row['phone_number'];
		$exportArray[] = $row['state'];
		$exportArray[] = $row['city'];
		$exportArray[] = $row['zip'];
		$exportArray[] = $row['address_line_1'];
		$exportArray[] = $row['address_line_2'];
		$exportArray[] = $row['institution'];
		$exportArray[] = $row['department'];
		$exportArray[] = $row['department_website'];
		$exportArray[] = $row['personal_website'];
		$exportArray[] = $row['instructor_primary_title'];
		$exportArray[] = $row['campus'];
		$exportArray[] = $showPrivileges;
		
		//write this row to the file
		fputcsv($targetExportFile, $exportArray);
	}
	
	//close the export file
	fclose($targetExportFile);
	
	//used for testing
	//print '<h3>success!</h3>';
}
else
{
	//used for testing; no error message should be displayed normally, as this would
	//break the header redirect in the submit form script.
	//print "<h3>Failed to export data to csv file: error getting data from database</h3>";
}

//close the database connection
@mysqli_close($geologyDBConnection);

?>