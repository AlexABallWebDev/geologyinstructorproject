<?php
/*Author: Alex Ball
 *Date: 02/08/2015
 *Geology Contact Table Website
 *
 *Filename: forgot-password.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page allows users to reset their password by entering the email
 *address associated with their account. A randomly generated new
 *password will be sent to the user's email address.
 *
 */

//get database connection named $geologyDBConnection
require('../secure-includes/db-connection.php');

$emailAddress = '';

//user is assumed to have not been found.
$userFound = false;

//if the form has been submitted
if (isset($_POST['submit']))
{
  $emailAddress = $_POST['emailAddress'];

	//make sure that the email address is not empty
  if(empty($emailAddress))
  {
    $errorArray['emailAddress'] = '<span class="form-error">Please enter your email.</span>';
  }
  else
  {
		//save a sanitized version of the email address.
		//this copy is made so that the form can be sticky.
    $e = mysqli_real_escape_string($geologyDBConnection, trim($emailAddress));
  }
	
  if(empty($errorArray))
  {
    //check if a user exists with the given email
    $q = "SELECT editor_id FROM geology_instructor_editors WHERE email='" . $e . "'";
    $r = @mysqli_query ($geologyDBConnection, $q);
		
    //check if a editor_id with that email address exists.
		//if it exists, then generate a random password and email it to the editor.
    if (mysqli_num_rows($r)	== 1)
    {
			//Generate a random password.
			$newPassword = substr(md5(uniqid(rand(),1)), 3, 10);
			
			//update the database with the new password. note that email is a
			//unique field in the table, so this query will not harm any other editors.
			$q = "UPDATE geology_instructor_editors SET password = SHA1('$newPassword') WHERE email='" . $e . "'";
			$r = @mysqli_query ($geologyDBConnection, $q);
			
			//if the password was successfully updated, email the editor the new password
			if (mysqli_affected_rows($geologyDBConnection) == 1)
			{
				//send an email with the new password. 
				$successfulEmail = false;
				$emailBody = 'Your editor account password has been reset.' . "\n\n" .
							'You can log in using the new password included below. You can then reset' . "\n" .
							'your pasword by clicking the "change password" button in the upper-right' . "\n" .
							'corner of the page.' . "\n\n" .
							"Your new password is: $newPassword";
				$emailSubject = 'Password Reset for Geology Instructor Contact Table';
				$emailFrom = 'do-not-reply@geology-instructor-contacts.net';
				mail($e, $emailSubject, $emailBody, "From:$emailFrom");
				$userFound = true;
			}
			//if the password was not successfully updated, something has gone wrong. display an errror.
			else
			{
				$errorArray['resetPasswordError'] = '<span class="form-error">Error resetting password.
							Contact your instructor for more information.</span>';
			}
    }
		//if the email entered is not in the database, display an error.
    else
    {
      $errorArray['noMatchingEmailError'] = '<span class="form-error">The email address you entered is not
						associated with an editor account.</span>';
    }
  }
}

//close database connection.
mysqli_close($geologyDBConnection);

?>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Reset Password</title>
	
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
							Reset Password
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
						<!--	Description	-->
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<p class="description">
									Enter the email address associated with your editor account.
									A new password will be sent to your email address which you can use to log in.
									After you log in, you can change your password using the change password
									button in the upper-right corner of the page.
								</p>
								<?php
								//if the form was submitted and there are no errors (successful password reset) then show a
								//success message telling the user to check their email.
								if ((empty($errorArray) && isset($_POST['submit'])))
								{
									print '<p class="form-success">Password successfully reset! Check your email
												for your new password.</p>';
								}
								?>
							</div>
						</div>
						<!-- Area for errors, if any -->
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<?php
								//if there were any errors, give a message at the top of the screen saying so.
								print empty($errorArray) ? '' : '<p><span class="form-error">There were errors.</span></p>';
								//if the password reset failed, show an error
								print empty($errorArray['resetPasswordError']) ? '' : $errorArray['resetPasswordError'];
								//if the email address was not found in the database, show an error.
								print empty($errorArray['noMatchingEmailError']) ? '' : $errorArray['noMatchingEmailError'];
								?>
							</div>
						</div>
						
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4 col-sm-offset-4">
									<label for="emailAddress">Email Address:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['emailAddress'])) ? '' : '<br />' . $errorArray['emailAddress'];
									?></label>
									<input type="email" class="form-control" id="emailAddress" name="emailAddress"
												placeholder="Email Address" value="<?php print $emailAddress; ?>" required="required">
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