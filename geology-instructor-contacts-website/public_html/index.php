<?php
/*Author: Alex Ball
 *Date: 12/12/2015
 *Geology Contact Table Website
 *
 *Filename: index.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page acts as a homepage, showing a table of
 *instructor contact information.
 *
 */

//error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//start session
session_start();

//assign editor_id to a variable named $editor_id.
require('includes/check-editor-id.php');

//if user is logged in, different buttons will appear in the account links div.
if (isset($editor_id))
{
	$loggedIn = true;
	
	//if user is super user, then they get access to a button that links to the
	//change state-editor privileges page.
	
	//get database connection named $geologyDBConnection
	require('../secure-includes/db-connection.php');
	
	//define sql statement
	$checkPrivilegesSQL = 'SELECT * FROM geology_instructor_editors WHERE editor_id = ' . $editor_id;
	
	//get this editor's data
	$checkPrivilegesResult = mysqli_query($geologyDBConnection, $checkPrivilegesSQL);
	
	//put data into an array
	$editorPrivileges = mysqli_fetch_array($checkPrivilegesResult);
	
	//if user is super editor, allow any change to be made
	if ($editorPrivileges['super_user'] == 1)
	{
		$superEditor = true;
	}

	//close connection
	mysqli_close($geologyDBConnection);
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--	Author: Alex Ball
					Date: 12/12/2015
					Geology Contact Table Website
					
					Filename: index.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page acts as a homepage, showing a table of
					instructor contact information.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table</title>
	
		<!-- Bootstrap -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<!-- Datatable -->
		<link href="http://cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css">
		
		<!-- CSS used for every page of this website -->
		<link href="includes/general-style.css" rel="stylesheet">
		
	</head>
	<body>
		<!--	Content Area	-->
		<div class="container">
			<!-- Header Area -->
			<div class="row" id="page-header">
				<div class="col-xs-12">
					<div class="pull-left">
						<h2>Geology Instructor Contact Information Table</h2>
						<p>
							This website is meant to provide geology instructors with a place to find other
							geology instructors' contact information.
						</p>
					</div>
					<?php require('includes/account-links.php'); ?>
				</div>
			</div>
			<!-- End Header Area -->
			
			<!-- Navbar Area -->
			<div class="row">
				<div class="col-xs-12">
					<ul class="list-unstyled" id="navbar">
						<?php
						if (isset($loggedIn))
						{
							print '<li><a class="btn btn-default pull-left" href="downloads/geology-instructor-contact-table.csv">Download Table as Excel CSV file</a></li>';
							print '<li><a class="btn btn-default pull-left" href="add-contact.php">Add an Instructor Contact</a></li>';
						}
						if (isset($superEditor))
						{
							print '<li><a class="btn btn-default pull-left" href="change-state-editor-privileges.php">Grant State Editor Privileges</a></li>';
						}
						?>
					</ul>
				</div>
			</div>
			<!-- End Navbar Area -->
			
			<!-- Table Area -->
			<div class="row">
				<div class="col-xs-12">
					<table class="table table-bordered" id="instructor-contact-table">
						<thead>
							<tr>
								<th>Edit</th>
								<th>First Name</th>
								<th>Last name</th>
								<th>Email</th>
								<th>Phone number</th>
								<th>State</th>
								<th>City</th>
								<th>ZIP</th>
								<th>Address line 1</th>
								<th>Address line 2</th>
								<th>Institution</th>
								<th>Department</th>
								<th>Department Website</th>
								<th>Individual Website</th>
								<th>Primary Title/Position</th>
								<th>Campus</th>
							</tr>
						</thead>
	
						<tfoot>
							<tr>
								<th>Edit</th>
								<th>First Name</th>
								<th>Last name</th>
								<th>Email</th>
								<th>Phone number</th>
								<th>State</th>
								<th>City</th>
								<th>ZIP</th>
								<th>Address line 1</th>
								<th>Address line 2</th>
								<th>Institution</th>
								<th>Department</th>
								<th>Department Website</th>
								<th>Individual Website</th>
								<th>Primary Title/Position</th>
								<th>Campus</th>
							</tr>
						</tfoot>
	
						<tbody>
							<?php
							//get database connection named $geologyDBConnection
							require('../secure-includes/db-connection.php');
							
							//create sql statement
							$getTableDataSQL = 'SELECT * FROM geology_instructor_contacts';
							
							//query database for contact information
							$results = mysqli_query($geologyDBConnection, $getTableDataSQL);
							
							//do a foreach on the results, displaying each database table row as a table row here
							if ($results)
							{
								foreach ($results as $row)
								{
									//build edit button that will link to the update page with this instructor's data
									$editButton = '<a class="btn btn-default" href="update-contact.php?contact_id=';
									$editButton = $editButton . $row['contact_id'] . '">Edit Data</a>';
									
									print '<tr>';
									print '<td>' . $editButton . '</td>';
									print '<td>' . $row['first_name'] . '</td>';
									print '<td>' . $row['last_name'] . '</td>';
									print '<td>' . $row['email'] . '</td>';
									print '<td>' . $row['phone_number'] . '</td>';
									print '<td>' . $row['state'] . '</td>';
									print '<td>' . $row['city'] . '</td>';
									print '<td>' . $row['zip'] . '</td>';
									print '<td>' . $row['address_line_1'] . '</td>';
									print empty($row['address_line_2']) ? '<td></td>' : '<td>' . $row['address_line_2'] . '</td>'; //optional field
									print '<td>' . $row['institution'] . '</td>';
									print '<td>' . $row['department'] . '</td>';
									print empty($row['department_website']) ? '<td></td>' : '<td>' . $row['department_website'] . '</td>'; //optional field
									print empty($row['personal_website']) ? '<td></td>' : '<td>' . $row['personal_website'] . '</td>'; //optional field
									print '<td>' . $row['instructor_primary_title'] . '</td>';
									print empty($row['campus']) ? '<td></td>' : '<td>' . $row['campus'] . '</td>'; //optional field
									print '</tr>';
								}
							}
							//if no results, or results was false, display an error cell
							else
							{
								print '<tr><td>Error getting data from database</td></tr>';
							}
							
							//close connection
							mysqli_close($geologyDBConnection);
							?>
						</tbody>
					</table>
				</div>
			</div>
			<!-- End Table Area -->
			
			<!-- Footer area -->
			<?php include('includes/footer.php'); ?>
			<!-- End Footer area -->
			
		</div>
		<!--	End Content Area -->
	
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="../bootstrap/js/bootstrap.min.js"></script>
		
		<!-- Datatable -->
		<script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"
					type="text/javascript"></script>
		
		<script>
			$(document).ready(function(){
				$('#instructor-contact-table').DataTable({
					scrollX: true, //adds horizontal scrollbar
					stateSave: true, //so that table remembers which column was sorted/search terms
					"order": [[ 1, "asc" ]] //default sort; 1, asc is alphabetical order by first name
				});
			});
		</script>
	</body>	
</html>