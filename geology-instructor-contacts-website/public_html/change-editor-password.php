<?php
/*Author: Alex Ball
 *Date: 01/30/2016
 *Geology Contact Table Website
 *
 *Filename: change-editor-password.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to update an
 *editor account's password.
 *
 */

//error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//start session
session_start();

//include functions
require('includes/functions.php');

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

//set variables for form fields to empty strings
$updatePassword = '';
$updatePasswordConfirm = '';
$updatePasswordNew = '';

//if form was submitted, validate submitted data
if (isset($_POST['submit']))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$updatePassword = check_if_empty('updatePassword', 'You must enter your current password.');
	$updatePasswordConfirm = check_if_empty('updatePasswordConfirm', 'You must enter your current password.');
	$updatePasswordNew = check_if_empty('updatePasswordNew', 'You must enter a new password.');
	
	//only continue if the current and confirm password fields match and are not empty.
	if ($updatePassword != $updatePasswordConfirm)
	{
		$errorArray['passwordMismatch'] = '<p class="form-error">Current password and confirm password fields do not match.</p>';
	}
	
	if (empty($errorArray))
	{
		//get database connection named $geologyDBConnection
		require('../secure-includes/db-connection.php');
		
		//check to make sure the email is already being used in the database (each editor has a unique email address)
		$checkPasswordSQL = "SELECT * FROM geology_instructor_editors WHERE";
		$checkPasswordSQL .= " editor_id = $editor_id AND password = SHA1('$updatePassword')";
		
		$passwordCheckResult = mysqli_query($geologyDBConnection, $checkPasswordSQL);
		
		//only continue if an editor is found with this password. show an error otherwise.
		if (!$passwordCheckResult || ($passwordCheckResult && mysqli_num_rows($passwordCheckResult) != 1))
		{
			$errorArray['incorrectPassword'] = '<p class="form-error">Your current password is incorrect.</p>';
		}
		
		//if everything is valid, update editor password and display a message saying it was successful
		if (empty($errorArray))
		{
			//escape data for mySQL database
			$updatePasswordNew = mysqli_real_escape_string($geologyDBConnection, $updatePasswordNew);
			$updatePassword = mysqli_real_escape_string($geologyDBConnection, $updatePassword);
			
			//define sql statement
			$updatePasswordSQL = "UPDATE geology_instructor_editors SET password = SHA1('$updatePasswordNew')";
			
			//only update the one row with the given password
			$updatePasswordSQL .= " WHERE editor_id = $editor_id AND password = SHA1('$updatePassword')";
			
			//update row in database
			$result = mysqli_query($geologyDBConnection, $updatePasswordSQL);
			
			//if successful, display success message to table page.
			if ($result)
			{
				$successfulPasswordUpdate = '<p class="form-success">Your password has been successfully changed!</p>';
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
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--	Author: Alex Ball
					Date: 01/30/2016
					Geology Contact Table Website
					
					Filename: change-editor-password.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to update an
					editor account's password.
					
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Change Password</title>
	
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
							Change Password
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
								print empty($successfulPasswordUpdate) ? '' : $successfulPasswordUpdate;
								//if there was an error with updating the database, give an error saying so.
								print empty($errorArray['passwordMismatch']) ? '' : $errorArray['passwordMismatch'];
								//if the given current password does not match this editor's password, show an error saying so
								print empty($errorArray['incorrectPassword']) ? '' : $errorArray['incorrectPassword'];
								
								?>
							</div>
						</div>
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- State -->
								<div class="col-xs-12 col-sm-4">
									<label for="updatePassword">Current Password: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updatePassword'])) ? '' : '<br />' . $errorArray['updatePassword'];
									?></label>
									<input type="password" class="form-control" id="updatePassword" name="updatePassword"
												placeholder="Current Password" value="<?php print $updatePassword; ?>" >
								</div>
							</div>
							
							
							<!-- Row 2 -->
							<div class="row">
								<!-- State -->
								<div class="col-xs-12 col-sm-4">
									<label for="updatePasswordConfirm">Confirm Password: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updatePasswordConfirm'])) ? '' : '<br />' . $errorArray['updatePasswordConfirm'];
									?></label>
									<input type="password" class="form-control" id="updatePasswordConfirm" name="updatePasswordConfirm"
												placeholder="Enter your current password again" value="<?php print $updatePasswordConfirm; ?>" >
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- New Password -->
								<div class="col-xs-12 col-sm-4">
									<label for="updatePasswordNew">New Password: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updatePasswordNew'])) ? '' : '<br />' . $errorArray['updatePasswordNew'];
									?></label>
									<input type="password" class="form-control" id="updatePasswordNew" name="updatePasswordNew"
												placeholder="New Password" value="<?php print $updatePasswordNew; ?>" >
								</div>
							</div>
							
							<!-- Row 4 -->
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