<?php
/*Author: Alex Ball
 *Date: 12/27/2015
 *Geology Contact Table Website
 *
 *Filename: login.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to log in as an editor,
 *allowing changes to be made to the instructor data in the table.
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
$loginEmail = '';
$loginPassword = '';

//if form was submitted, validate submitted data
if (isset($_POST['submit']))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$loginEmail = check_if_empty('loginEmail', 'Email Address cannot be empty!');
	$loginPassword = check_if_empty('loginPassword', 'Password cannot be empty!');
	
	//if everything is valid, log the editor in by setting a session variable.
	if (empty($errorArray))
	{
		//get database connection named $geologyDBConnection
		require('../secure-includes/db-connection.php');
		
		//escape data for mySQL database
		$loginEmail = mysqli_real_escape_string($geologyDBConnection, $loginEmail);
		$loginPassword = mysqli_real_escape_string($geologyDBConnection, $loginPassword);
		
		//check for an editor with the given email and password
		$loginSQL = "SELECT * FROM geology_instructor_editors WHERE email = '$loginEmail' AND password = SHA1('$loginPassword')";
		
		$loginResult = mysqli_query($geologyDBConnection, $loginSQL);
		
		//if the editor exists, set session variable. otherwise, display error about invalid email/password.
		if ($loginResult && mysqli_num_rows($loginResult) == 1)
		{
			//get editor id from result
			$row = mysqli_fetch_array($loginResult);
			$editor_id = $row['editor_id'];
			
			//if successful, set a session variable so the user is logged in and redirect to table page.
			$_SESSION['editor_id'] = $editor_id;
			
			//redirect to table page. the new editor will be logged in due to above session variable.
			header('location: index.php');
		}
		//otherwise, an error is generated.
		else
		{
			$errorArray['invalidEmailPasswordPair'] = '<p><span class="form-error">There is no editor with the given email and password combination. Check to make sure your information is correct.</span></p>';
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
					Date: 12/27/2015
					Geology Contact Table Website
					
					Filename: login.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to log in as an editor,
					allowing changes to be made to the instructor data in the table.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Editor Log In</title>
	
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
							Editor Log In
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
								//if the email and password combo was invalid, give an error saying so.
								print empty($errorArray['invalidEmailPasswordPair']) ? '' : $errorArray['invalidEmailPasswordPair'];
								
								?>
							</div>
						</div>
						
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="loginEmail">Email Address:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['loginEmail'])) ? '' : '<br />' . $errorArray['loginEmail'];
									?></label>
									<input type="email" class="form-control" id="loginEmail" name="loginEmail"
												placeholder="Email Address" value="<?php print $loginEmail; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 2 -->
							<div class="row">
								<!-- Password -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="loginPassword">Password:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['loginPassword'])) ? '' : '<br />' . $errorArray['loginPassword'];
									?></label>
									<input type="password" class="form-control" id="loginPassword" name="loginPassword"
												placeholder="Password" value="" required="required">
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- Password -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<a href="forgot-password.php">Forgot Password</a>
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