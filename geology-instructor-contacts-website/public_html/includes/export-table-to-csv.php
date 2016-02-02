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
	
	//write the column header row to the file
	fputcsv($targetExportFile, $columnHeaderArray);
	
	
	//for each entry, add it to the csv file
	foreach($result as $row)
	{
		
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