<?php
/*Author: Alex Ball
 *Date: 01/02/2016
 *Geology Contact Table Website
 *
 *Filename: change-state-editor-privileges.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to update an
 *editor account to have state editor privileges.
 *
 */

//error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//start session
session_start();

//include functions
require('includes/functions.php');

//include arrays for building and validating parts of the form
require('includes/form-arrays.php');

//error array starts empty, but will contain any errors generated.
$errorArray = array();

//this page requires the user to be logged in as an editor.
//assign editor_id to a variable named $editor_id.
require('includes/check-editor-id.php');

//if user is logged in, different buttons will appear in the account links div.
if (isset($editor_id))
{
	$loggedIn = true;
}

//redirect the user if not logged in
if (!isset($editor_id))
{
	//redirect to login
	header('location: login.php');
}

//only state or super editors may edit existing instructor data. check to make sure the current
//editor is a state or super user, display an error (and disallow changes when the form is submitted) otherwise.

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
//if user is just a normal editor or state editor, redirect to login page
else
{
	header('location:login.php');
}

//close connection
mysqli_close($geologyDBConnection);

//set variables for form fields to empty strings
$updateStatePrivilegesEmailAddress = '';
$updateStatePrivilegesState = '';

//if form was submitted, validate submitted data
if (isset($_POST['submit']))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$updateStatePrivilegesEmailAddress = check_if_empty('updateStatePrivilegesEmailAddress', 'Email Address cannot be empty!');
	$updateStatePrivilegesState = check_if_empty('updateStatePrivilegesState', 'You must select a State!');

	//check to make sure State data is in the array of valid states, or "Not a State Editor". if not, show an error.
	if (empty($errorArray['updateStatePrivilegesState']))
	{
		if (!in_array($updateStatePrivilegesState, $listOfStates) && $updateStatePrivilegesState != 'Not a State Editor')
		{
			$errorArray['updateStatePrivilegesState'] = '<p class="form-error">You must select a State from the dropdown list!</p>';
		}
	}
	
	//get database connection named $geologyDBConnection
	require('../secure-includes/db-connection.php');
	
	//check to make sure the email is already being used in the database (each editor has a unique email address)
	$checkEmailSQL = "SELECT * FROM geology_instructor_editors WHERE email = '$updateStatePrivilegesEmailAddress'";
	
	$emailCheckResult = mysqli_query($geologyDBConnection, $checkEmailSQL);
	
	//only continue if an editor already exists with this email. show an error otherwise.
	if (!$emailCheckResult || ($emailCheckResult && mysqli_num_rows($emailCheckResult) != 1))
	{
		$errorArray['noEditorWithThatEmail'] = '<p class="form-error">No editor exists with that email!</p>';
	}
	
	//if everything is valid, update editor privileges and display a message saying it was successful
	if (empty($errorArray))
	{
		//escape data for mySQL database
		$updateStatePrivilegesEmailAddress = mysqli_real_escape_string($geologyDBConnection, $updateStatePrivilegesEmailAddress);
		$updateStatePrivilegesState = mysqli_real_escape_string($geologyDBConnection, $updateStatePrivilegesState);
		
		//define sql statement
		$updateStatePrivilegesSQL = "UPDATE geology_instructor_editors SET editor_state = '$updateStatePrivilegesState'";
		
		//only update the one row with the given email
		$updateStatePrivilegesSQL .= " WHERE email = '$updateStatePrivilegesEmailAddress'";
		
		//update row in database
		$result = mysqli_query($geologyDBConnection, $updateStatePrivilegesSQL);
		
		//if successful, display success message to table page.
		if ($result)
		{
			$successfulPrivilegesUpdate = '<p class="form-success">Instructor Privileges successfully granted to editor account!</p>';
		}
		//otherwise, an error is generated.
		else
		{
			$errorArray['databaseUpdateError'] = '<p><span class="form-error">Error updating database. Try again.</span></p>';
		}
	}
	
	//close connection
	mysqli_close($geologyDBConnection);
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--	Author: Alex Ball
					Date: 01/02/2016
					Geology Contact Table Website
					
					Filename: change-state-editor-privileges.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to update an
					editor account to have state editor privileges.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Grant State Editor Privileges</title>
	
		<!-- Bootstrap -->
		<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
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
						<h3>
							Grant State Editor Privileges
						</h3>
					</div>
					<?php require('includes/account-links.php'); ?>
				</div>
			</div>
			<!-- End Header Area -->
			
			<!-- Form Area -->
			<div class="row">
				<div class="col-xs-12">
					<form method="post" action="#">
							<!-- Note about required fields -->
						<div class="row">
							<div class="col-xs-12">
								<p>Required fields are marked with a red asterisk <span class="form-error">*</span></p>
								<p>Enter the editor's email address and choose a state. The editor will be able to change
											instructor data for instructors that are in the chosen state.</p>
								<?php
								//if there were any errors, give a message at the top of the screen saying so.
								print empty($errorArray) ? '' : '<p><span class="form-error">There were errors.</span></p>';
								//if there was an error with updating the database, give an error saying so.
								print empty($errorArray['databaseUpdateError']) ? '' : $errorArray['databaseUpdateError'];
								//if everything went perfectly, show message saying so.
								print empty($successfulPrivilegesUpdate) ? '' : $successfulPrivilegesUpdate;
								//if there was no editor with the given email, show an error saying so
								print empty($errorArray['noEditorWithThatEmail']) ? '' : $errorArray['noEditorWithThatEmail'];
								
								?>
							</div>
						</div>
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateStatePrivilegesEmailAddress">Email Address: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateStatePrivilegesEmailAddress'])) ? '' : '<br />' . $errorArray['updateStatePrivilegesEmailAddress'];
									?></label>
									<input type="email" class="form-control" id="updateStatePrivilegesEmailAddress" name="updateStatePrivilegesEmailAddress"
												placeholder="Email Address" value="<?php print $updateStatePrivilegesEmailAddress; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 2 -->
							<div class="row">
								<!-- State -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateStatePrivilegesState">State: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateStatePrivilegesState'])) ? '' : '<br />' . $errorArray['updateStatePrivilegesState'];
									?></label>
									<select class="form-control" id="updateStatePrivilegesState" name="updateStatePrivilegesState">
										<option value="Not a State Editor">Not a State Editor</option>
										<?php create_select_options_list($listOfStates, $updateStatePrivilegesState); ?>
									</select>
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- Submit Button -->
								<div class="col-xs-12 text-center">
									<button class="submit-button" value="submit" type="submit" name="submit">Submit</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<!-- End Form Area -->
			
			<!-- Footer area -->
			<?php include('includes/footer.php'); ?>
			<!-- End Footer area -->
			
		</div>
		<!--	End Content Area -->
	
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="../bootstrap/js/bootstrap.min.js"></script>
	</body>	
</html>