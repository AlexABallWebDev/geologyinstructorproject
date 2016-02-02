<?php
/*Author: Alex Ball
 *Date: 12/18/2015
 *Geology Contact Table Website
 *
 *Filename: add-contact.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to add an
 *instructor to the contact table.
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

//set variables for form fields to empty strings
$addContactFName = '';
$addContactLName = '';
$addContactEmailAddress = '';
$addContactPhoneNumber = '';
$addContactState = '';
$addContactCity = '';
$addContactZIP = '';
$addContactAddressLine1 = '';
$addContactAddressLine2 = '';
$addContactInstitution = '';
$addContactDepartment = '';
$addContactDepartmentWebsite = '';
$addContactIndividualWebsite = '';
$addContactPrimaryTitle = '';
$addContactCampus = '';

//if form was submitted, validate submitted data
if (isset($_POST['submit']))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$addContactFName = check_if_empty('addContactFName', 'First Name cannot be empty!');
	$addContactLName = check_if_empty('addContactLName', 'Last Name cannot be empty!');
	$addContactEmailAddress = check_if_empty('addContactEmailAddress', 'Email Address cannot be empty!');
	$addContactPhoneNumber = check_if_empty('addContactPhoneNumber', 'Phone Number cannot be empty!');
	$addContactState = check_if_empty('addContactState', 'You must select a State!');
	$addContactCity = check_if_empty('addContactCity', 'City cannot be empty!');
	$addContactZIP = check_if_empty('addContactZIP', 'ZIP cannot be empty!');
	$addContactAddressLine1 = check_if_empty('addContactAddressLine1', 'Address Line 1 cannot be empty!');
	$addContactInstitution = check_if_empty('addContactInstitution', 'Institution cannot be empty!');
	$addContactDepartment = check_if_empty('addContactDepartment', 'Department cannot be empty!');
	$addContactPrimaryTitle = check_if_empty('addContactPrimaryTitle', 'Primary Title cannot be empty!');
	
	
	//optional fields; therefore, they are allowed to be empty.
	$addContactAddressLine2 = empty($_POST['addContactAddressLine2']) ? '' : $_POST['addContactAddressLine2'];
	$addContactDepartmentWebsite = empty($_POST['addContactDepartmentWebsite']) ? '' : $_POST['addContactDepartmentWebsite'];
	$addContactIndividualWebsite = empty($_POST['addContactIndividualWebsite']) ? '' : $_POST['addContactIndividualWebsite'];
	$addContactCampus = empty($_POST['addContactCampus']) ? '' : $_POST['addContactCampus'];

	//check to make sure State data is in the array of valid states. if not, show an error.
	if (empty($errorArray['addContactState']))
	{
		if (!in_array($addContactState, $listOfStates))
		{
			$errorArray['addContactState'] = '<span class="form-error">You must select a State from the dropdown list!</span>';
		}
	}
	
	//clean optional fields, as they were not checked using check_if_empty (and therefore were not cleaned).
	$addContactAddressLine2 = clean_data($addContactAddressLine2);
	$addContactDepartmentWebsite = clean_data($addContactDepartmentWebsite);
	$addContactIndividualWebsite = clean_data($addContactIndividualWebsite);
	$addContactCampus = clean_data($addContactCampus);
	
	//if everything is valid, add instructor contact data to database and redirect to table page.
	if (empty($errorArray))
	{
		//get database connection named $geologyDBConnection
		require('../secure-includes/db-connection.php');
		
		//escape data for mySQL database
		$addContactFName = mysqli_real_escape_string($geologyDBConnection, $addContactFName);
		$addContactLName = mysqli_real_escape_string($geologyDBConnection, $addContactLName);
		$addContactEmailAddress = mysqli_real_escape_string($geologyDBConnection, $addContactEmailAddress);
		$addContactPhoneNumber = mysqli_real_escape_string($geologyDBConnection, $addContactPhoneNumber);
		$addContactState = mysqli_real_escape_string($geologyDBConnection, $addContactState);
		$addContactCity = mysqli_real_escape_string($geologyDBConnection, $addContactCity);
		$addContactZIP = mysqli_real_escape_string($geologyDBConnection, $addContactZIP);
		$addContactAddressLine1 = mysqli_real_escape_string($geologyDBConnection, $addContactAddressLine1);
		$addContactInstitution = mysqli_real_escape_string($geologyDBConnection, $addContactInstitution);
		$addContactDepartment = mysqli_real_escape_string($geologyDBConnection, $addContactDepartment);
		$addContactPrimaryTitle = mysqli_real_escape_string($geologyDBConnection, $addContactPrimaryTitle);
		$addContactAddressLine2 = mysqli_real_escape_string($geologyDBConnection, $addContactAddressLine2);
		$addContactDepartmentWebsite = mysqli_real_escape_string($geologyDBConnection, $addContactDepartmentWebsite);
		$addContactIndividualWebsite = mysqli_real_escape_string($geologyDBConnection, $addContactIndividualWebsite);
		$addContactCampus = mysqli_real_escape_string($geologyDBConnection, $addContactCampus);
		
		//check if optional fields were used or not (this is needed to build the sql statement).
		$addressLine2SQL = empty($addContactAddressLine2) ? '' : ', address_line_2';
		$DepartmentWebsiteSQL = empty($addContactDepartmentWebsite) ? '' : ', department_website';
		$IndividualWebsiteSQL = empty($addContactIndividualWebsite) ? '' : ', personal_website';
		$CampusSQL = empty($addContactCampus) ? '' : ', campus';
		//add sql syntax to optional values if any were not empty
		$dataAddressLine2 = empty($addContactAddressLine2) ? '' : ", '" . $addContactAddressLine2 . "'";
		$dataContactDepartmentWebsite = empty($addContactDepartmentWebsite) ? '' : ", '" . $addContactDepartmentWebsite . "'";
		$dataContactIndividualWebsite = empty($addContactIndividualWebsite) ? '' : ", '" . $addContactIndividualWebsite . "'";
		$dataContactCampus = empty($addContactCampus) ? '' : ", '" . $addContactCampus . "'";
		
		//define sql statement, starting with the column names
		$addContactSQL = 'INSERT INTO geology_instructor_contacts (first_name, last_name, email, phone_number, state,';
		$addContactSQL = $addContactSQL . ' city, zip, address_line_1, institution, department, instructor_primary_title, timestamp';
		$addContactSQL = $addContactSQL . $addressLine2SQL . $DepartmentWebsiteSQL . $IndividualWebsiteSQL . $CampusSQL . ')';
		//and then the values
		$addContactSQL = $addContactSQL . " VALUES ('$addContactFName', '$addContactLName', '$addContactEmailAddress', '$addContactPhoneNumber'";
		$addContactSQL = $addContactSQL . ", '$addContactState', '$addContactCity', '$addContactZIP', '$addContactAddressLine1'";
		$addContactSQL = $addContactSQL . ", '$addContactInstitution', '$addContactDepartment', '$addContactPrimaryTitle', NOW()";
		$addContactSQL = $addContactSQL . $dataAddressLine2 . $dataContactDepartmentWebsite . $dataContactIndividualWebsite . $dataContactCampus . ")";
		
		//insert row into database
		$result = mysqli_query($geologyDBConnection, $addContactSQL);
		
		//close connection
		mysqli_close($geologyDBConnection);
		
		//if successful, update csv file and redirect to table page.
		if ($result)
		{
			include('includes/export-table-to-csv.php');
			header('location: index.php');
		}
		//otherwise, an error is generated.
		else
		{
			$errorArray['databaseUpdateError'] = '<p><span class="form-error">Error updating database. Try again.</span></p>';
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--	Author: Alex Ball
					Date: 12/18/2015
					Geology Contact Table Website
					
					Filename: add-contact.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to add an
					instructor to the contact table.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Add Contact</title>
	
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
							Add an Instructor
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
								<?php
								//if there were any errors, give a message at the top of the screen saying so.
								print empty($errorArray) ? '' : '<p><span class="form-error">There were errors.</span></p>';
								//if there was an error with updating the database, give an error saying so.
								print empty($errorArray['databaseUpdateError']) ? '' : $errorArray['databaseUpdateError'];
								?>
							</div>
						</div>
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- First Name -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactFName">First Name: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactFName'])) ? '' : '<br />' . $errorArray['addContactFName'];
									?></label>
									<input type="text" class="form-control" id="addContactFName" name="addContactFName"
												placeholder="First Name" value="<?php print $addContactFName; ?>" required="required">
								</div>
								
								<!-- Last Name -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactLName">Last Name: <span class="form-error">*</span>
												<?php
												//if there is an error for this element, print it. otherwise, print an empty string.
												print (empty($errorArray['addContactLName'])) ? '' : '<br />' . $errorArray['addContactLName'];
												?></label>
									<input type="text" class="form-control" id="addContactLName" name="addContactLName"
												placeholder="Last Name" value="<?php print $addContactLName; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 2 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactEmailAddress">Email Address: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactEmailAddress'])) ? '' : '<br />' . $errorArray['addContactEmailAddress'];
									?></label>
									<input type="email" class="form-control" id="addContactEmailAddress" name="addContactEmailAddress"
												placeholder="Email Address" value="<?php print $addContactEmailAddress; ?>" required="required">
								</div>
								
								<!-- Phone Number -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactPhoneNumber">Phone Number:
												<span class="form-error">*</span>
												<?php
												//if there is an error for this element, print it. otherwise, print an empty string.
												print (empty($errorArray['addContactPhoneNumber'])) ? '' : '<br />' . $errorArray['addContactPhoneNumber'];
												?></label>
									<input type="text" class="form-control" id="addContactPhoneNumber" name="addContactPhoneNumber"
												placeholder="Phone Number" value="<?php print $addContactPhoneNumber; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- State -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactState">State: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactState'])) ? '' : '<br />' . $errorArray['addContactState'];
									?></label>
									<select class="form-control" id="addContactState" name="addContactState">
										<option value="">Select a State</option>
										<?php create_select_options_list($listOfStates, $addContactState); ?>
									</select>
								</div>
								
								<!-- City -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactCity">City: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactCity'])) ? '' : '<br />' . $errorArray['addContactCity'];
									?></label>
									<input type="text" class="form-control" id="addContactCity" name="addContactCity"
												placeholder="City" value="<?php print $addContactCity; ?>" required="required">
								</div>
								
								<!-- ZIP -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactZIP">ZIP: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactZIP'])) ? '' : '<br />' . $errorArray['addContactZIP'];
									?></label>
									<input type="text" class="form-control" id="addContactZIP" name="addContactZIP"
												placeholder="ZIP" value="<?php print $addContactZIP; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 4 -->
							<div class="row">
								<!-- Address Line 1 -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactAddressLine1">Address Line 1: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactAddressLine1'])) ? '' : '<br />' . $errorArray['addContactAddressLine1'];
									?></label>
									<input type="text" class="form-control" id="addContactAddressLine1" name="addContactAddressLine1"
												placeholder="Address Line 1" value="<?php print $addContactAddressLine1; ?>" required="required">
								</div>
								
								<!-- Address Line 2 -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactAddressLine2">Address Line 2:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactAddressLine2'])) ? '' : '<br />' . $errorArray['addContactAddressLine2'];
									?></label>
									<input type="text" class="form-control" id="addContactAddressLine2" name="addContactAddressLine2"
												placeholder="Address Line 2" value="<?php print $addContactAddressLine2; ?>">
								</div>
							</div>
							
							<!-- Row 5 -->
							<div class="row">
								<!-- Institution -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactInstitution">Institution: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactInstitution'])) ? '' : '<br />' . $errorArray['addContactInstitution'];
									?></label>
									<input type="text" class="form-control" id="addContactInstitution" name="addContactInstitution"
												placeholder="Institution" value="<?php print $addContactInstitution; ?>" required="required">
								</div>
								
								<!-- Department -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactDepartment">Department: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactDepartment'])) ? '' : '<br />' . $errorArray['addContactDepartment'];
									?></label>
									<input type="text" class="form-control" id="addContactDepartment" name="addContactDepartment"
												placeholder="Department" value="<?php print $addContactDepartment; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 6 -->
							<div class="row">
								<!-- Department Website -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactDepartmentWebsite">Department Website:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactDepartmentWebsite'])) ? '' : '<br />' . $errorArray['addContactDepartmentWebsite'];
									?></label>
									<input type="text" class="form-control" id="addContactDepartmentWebsite" name="addContactDepartmentWebsite"
												placeholder="Department Website" value="<?php print $addContactDepartmentWebsite; ?>">
								</div>
								
								<!-- Individual Website -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactIndividualWebsite">Individual Website:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactIndividualWebsite'])) ? '' : '<br />' . $errorArray['addContactIndividualWebsite'];
									?></label>
									<input type="text" class="form-control" id="addContactIndividualWebsite" name="addContactIndividualWebsite"
												placeholder="Individual Website" value="<?php print $addContactIndividualWebsite; ?>">
								</div>
							</div>
							
							<!-- Row 7 -->
							<div class="row">
								<!-- Primary Title/Position -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactPrimaryTitle">Primary Title/Position: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactPrimaryTitle'])) ? '' : '<br />' . $errorArray['addContactPrimaryTitle'];
									?></label>
									<input type="text" class="form-control" id="addContactPrimaryTitle" name="addContactPrimaryTitle"
												placeholder="Primary Title/Position" value="<?php print $addContactPrimaryTitle; ?>" required="required">
								</div>
								
								<!-- Campus -->
								<div class="col-xs-12 col-sm-4">
									<label for="addContactCampus">Campus:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['addContactCampus'])) ? '' : '<br />' . $errorArray['addContactCampus'];
									?></label>
									<input type="text" class="form-control" id="addContactCampus" name="addContactCampus"
												placeholder="Campus" value="<?php print $addContactCampus; ?>">
								</div>
							</div>
							
							<!-- Row 8 -->
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