<?php
/*Author: Alex Ball
 *Date: 12/24/2015
 *Geology Contact Table Website
 *
 *Filename: add-editor.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to create an
 *editor account, which can edit information in the table.
 *
 */

//error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//start session so that a newly created editor can be logged in
session_start();

//include functions
require('includes/functions.php');

//include arrays for building and validating parts of the form
require('includes/form-arrays.php');

//check if editor is logged in
require('includes/check-editor-id.php');

//if user is logged in, different buttons will appear in the account links div.
if (isset($editor_id))
{
	$loggedIn = true;
}

//error array starts empty, but will contain any errors generated.
$errorArray = array();

//set variables for form fields to empty strings
$addEditorEmail = '';
$addEditorPassword = '';
$addEditorConfirmPassword = '';

//if form was submitted, validate submitted data
if (isset($_POST['submit']))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$addEditorEmail = check_if_empty('addEditorEmail', 'Email Address cannot be empty!');
	$addEditorPassword = check_if_empty('addEditorPassword', 'Password cannot be empty!');
	$addEditorConfirmPassword = check_if_empty('addEditorConfirmPassword', 'Repeat Password cannot be empty!');
	
	//both passwords must match to continue. only show this error if both fields were entered.
	if (!empty($errorArray['addEditorPassword']) && !empty($errorArray['addEditorConfirmPassword']) && ($addEditorPassword != $addEditorConfirmPassword))
	{
		$errorArray['passwordsDoNotMatch'] = '<p><span class="form-error">Passwords do not match. Try again.</span></p>';
	}
	
	//if everything is valid, add instructor contact data to database and redirect to table page.
	if (empty($errorArray))
	{
		//get database connection named $geologyDBConnection
		require('../secure-includes/db-connection.php');
		
		//escape data for mySQL database
		$addEditorEmail = mysqli_real_escape_string($geologyDBConnection, $addEditorEmail);
		$addEditorPassword = mysqli_real_escape_string($geologyDBConnection, $addEditorPassword);
		
		//check to make sure the email is not already being used in the database (each editor has a unique email address)
		$checkEmailSQL = "SELECT * FROM geology_instructor_editors WHERE email = '$addEditorEmail'";
		
		$emailCheckResult = mysqli_query($geologyDBConnection, $checkEmailSQL);
		
		//only continue if no editor already exists with this email. show an error otherwise.
		if ($emailCheckResult && mysqli_num_rows($emailCheckResult) == 0)
		{
			//define sql statement, starting with the column names
			$addEditorSQL = 'INSERT INTO geology_instructor_editors (email, password)';
			//and then the values
			$addEditorSQL = $addEditorSQL . " VALUES ('$addEditorEmail', SHA1('$addEditorPassword'))";
			
			//insert row into database
			$result = mysqli_query($geologyDBConnection, $addEditorSQL);
			
			//if successful, set a session variable so the user is logged in and redirect to table page.
			if ($result)
			{
				//get generated editor id and set session editor id to that generated id.
				$_SESSION['editor_id'] = mysqli_insert_id($geologyDBConnection);
				
				//redirect to table page. the new editor will be logged in due to above session variable.
				header('location: index.php');
			}
			//otherwise, an error is generated.
			else
			{
				$errorArray['databaseUpdateError'] = '<p><span class="form-error">Error updating database. Try again.</span></p>';
			}
		}
		//if an editor with the given email exists, show an error.
		else
		{
			$errorArray['emailAlreadyUsed'] = '<p><span class="form-error">An editor with that email address already exists.</span></p>';
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
					Date: 12/24/2015
					Geology Contact Table Website
					
					Filename: add-editor.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to create an
					editor account, which can edit information in the table.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Create Editor Account</title>
	
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
							Create Editor Account
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
							<!-- Area for errors, if any -->
						<div class="row">
							<div class="col-xs-12">
								<?php
								//if there were any errors, give a message at the top of the screen saying so.
								print empty($errorArray) ? '' : '<p><span class="form-error">There were errors.</span></p>';
								//if there was an error with updating the database, give an error saying so.
								print empty($errorArray['databaseUpdateError']) ? '' : $errorArray['databaseUpdateError'];
								//if there was an error where the passwords did not match, give an error saying so.
								print empty($errorArray['passwordsDoNotMatch']) ? '' : $errorArray['passwordsDoNotMatch'];
								//if the email given was already in use, give an error saying so.
								print empty($errorArray['emailAlreadyUsed']) ? '' : $errorArray['emailAlreadyUsed'];
								?>
							</div>
						</div>
						
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="addEditorEmail">Email Address:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addEditorEmail'])) ? '' : '<br />' . $errorArray['addEditorEmail'];
									?></label>
									<input type="email" class="form-control" id="addEditorEmail" name="addEditorEmail"
												placeholder="Email Address" value="<?php print $addEditorEmail; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 2 -->
							<div class="row">
								<!-- Password -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="addEditorPassword">Password:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addEditorPassword'])) ? '' : '<br />' . $errorArray['addEditorPassword'];
									?></label>
									<input type="password" class="form-control" id="addEditorPassword" name="addEditorPassword"
												placeholder="Password" value="" required="required">
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- Confirm Password -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="addEditorConfirmPassword">Repeat Password:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addEditorConfirmPassword'])) ? '' : '<br />' . $errorArray['addEditorConfirmPassword'];
									?></label>
									<input type="password" class="form-control" id="addEditorConfirmPassword" name="addEditorConfirmPassword"
												placeholder="Password" value="" required="required">
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