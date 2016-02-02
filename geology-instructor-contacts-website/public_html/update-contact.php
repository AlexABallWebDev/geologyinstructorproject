<?php
/*Author: Alex Ball
 *Date: 12/20/2015
 *Geology Contact Table Website
 *
 *Filename: update-contact.php
 *
 *This website is intended to provide geology instructors
 *with a way to get each other's contact info.
 *This page contains a form that is used to update an
 *instructor that is already on the contact table.
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

//get the contact id for the row that is being edited from the GET array.
//if it does not exist, show an error.
if (!empty($_GET['contact_id']))
{
	$contact_id = $_GET['contact_id'];
}
else
{
	$contact_id = '';
	$errorArray['failedToGetContactID'] = '<p class="form-error">An instructor was not correctly selected!
				Go back to the instructor table and click the "edit" button to edit an instructor!</p>';
}

//check editor privileges and only allow changes by editors that are allowed to edit this data.

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
//if user is state editor, check to see what state they oversee
else if ($editorPrivileges['editor_state'] != 'Not a State Editor')
{
	$stateEditor = true;
	$editorState = $editorPrivileges['editor_state'];
}
//if user is just a normal editor, check if the user's email address matches this instructor row's email.
//if it does, allow changes. otherwise, show an error.
else
{
	//define sql statement
	$checkEmailSQL = 'SELECT email FROM geology_instructor_contacts WHERE contact_id = ' . $contact_id;
	$checkEmailSQL .= ' AND email = "' . $editorPrivileges['email'] . '"';
	
	//check for a result
	$checkEmailResult = mysqli_query($geologyDBConnection, $checkEmailSQL);
	
	//if one result, allow changes. Otherwise, show an error stating that this editor cannot edit other instructor's data.
	if (mysqli_num_rows($checkEmailResult) != 1)
	{
		$errorArray['normalEditorError'] = '<p class="form-error">You can only edit your own instructor data: your editor
					account\'s email address must match the instructor row\'s email address.</p>';
	}
}

//close connection
mysqli_close($geologyDBConnection);

//set variables for form fields to empty strings
$updateContactFName = '';
$updateContactLName = '';
$updateContactEmailAddress = '';
$updateContactPhoneNumber = '';
$updateContactState = '';
$updateContactCity = '';
$updateContactZIP = '';
$updateContactAddressLine1 = '';
$updateContactAddressLine2 = '';
$updateContactInstitution = '';
$updateContactDepartment = '';
$updateContactDepartmentWebsite = '';
$updateContactIndividualWebsite = '';
$updateContactPrimaryTitle = '';
$updateContactCampus = '';

//get database connection named $geologyDBConnection
require('../secure-includes/db-connection.php');

//create sql statement
$getTableDataSQL = 'SELECT * FROM geology_instructor_contacts WHERE contact_id = ' . $contact_id;

//query database for contact information
$results = mysqli_query($geologyDBConnection, $getTableDataSQL);

//if a row was found successfully, assign each of its columns to the data variables so the form
//is filled out with the information. 
if ($results && mysqli_affected_rows($geologyDBConnection) == 1)
{
	foreach($results as $row)
	{
		//if the editor is a state editor (not a super editor) then only instructors with matching
		//states can be edited by this editor.
		if (!isset($superEditor) && isset($stateEditor))
		{
			//add an error if the editor and instructor data states do not match
			if ($editorState != $row['state'])
			{
				$errorArray['editorStateDoesNotMatch'] = '<p class="form-error">As a state editor for ' . $editorState . ', you cannot edit instructor data from other states.</p>';
			}
		}
		
		$updateContactFName = $row['first_name'];
		$updateContactLName = $row['last_name'];
		$updateContactEmailAddress = $row['email'];
		$updateContactPhoneNumber = $row['phone_number'];
		$updateContactState = $row['state'];
		$updateContactCity = $row['city'];
		$updateContactZIP = $row['zip'];
		$updateContactAddressLine1 = $row['address_line_1'];
		$updateContactAddressLine2 = empty($row['address_line_2']) ? '' : $row['address_line_2'];
		$updateContactInstitution = $row['institution'];
		$updateContactDepartment = $row['department'];
		$updateContactDepartmentWebsite = empty($row['department_website']) ? '' : $row['department_website'];
		$updateContactIndividualWebsite = empty($row['personal_website']) ? '' : $row['personal_website'];
		$updateContactPrimaryTitle = $row['instructor_primary_title'];
		$updateContactCampus = empty($row['campus']) ? '' : $row['campus'];
	}
}
else
{
	$errorArray['failedToFindInstructor'] = '<p class="form-error">The row for this instructor cannot be found in the database!</p>';
	$noRowFound = true;
}

//if form was submitted and a contact id was found in the get array, and the row with the given contact id exists, validate submitted data
if (isset($_POST['submit']) && !empty($contact_id) && !isset($noRowFound))
{
	//check to make sure all required fields are filled out and put data into variables
	//note that check_if_empty also cleans the data, if found.
	$updateContactFName = check_if_empty('updateContactFName', 'First Name cannot be empty!');
	$updateContactLName = check_if_empty('updateContactLName', 'Last Name cannot be empty!');
	$updateContactEmailAddress = check_if_empty('updateContactEmailAddress', 'Email Address cannot be empty!');
	$updateContactPhoneNumber = check_if_empty('updateContactPhoneNumber', 'Phone Number cannot be empty!');
	$updateContactState = check_if_empty('updateContactState', 'You must select a State!');
	$updateContactCity = check_if_empty('updateContactCity', 'City cannot be empty!');
	$updateContactZIP = check_if_empty('updateContactZIP', 'ZIP cannot be empty!');
	$updateContactAddressLine1 = check_if_empty('updateContactAddressLine1', 'Address Line 1 cannot be empty!');
	$updateContactInstitution = check_if_empty('updateContactInstitution', 'Institution cannot be empty!');
	$updateContactDepartment = check_if_empty('updateContactDepartment', 'Department cannot be empty!');
	$updateContactPrimaryTitle = check_if_empty('updateContactPrimaryTitle', 'Primary Title cannot be empty!');
	
	
	//optional fields; therefore, they are allowed to be empty.
	$updateContactAddressLine2 = empty($_POST['updateContactAddressLine2']) ? '' : $_POST['updateContactAddressLine2'];
	$updateContactDepartmentWebsite = empty($_POST['updateContactDepartmentWebsite']) ? '' : $_POST['updateContactDepartmentWebsite'];
	$updateContactIndividualWebsite = empty($_POST['updateContactIndividualWebsite']) ? '' : $_POST['updateContactIndividualWebsite'];
	$updateContactCampus = empty($_POST['updateContactCampus']) ? '' : $_POST['updateContactCampus'];

	//check to make sure State data is in the array of valid states. if not, show an error.
	if (empty($errorArray['updateContactState']))
	{
		if (!in_array($updateContactState, $listOfStates))
		{
			$errorArray['updateContactState'] = '<span class="form-error">You must select a State from the dropdown list!</span>';
		}
	}
	
	//clean optional fields, as they were not checked using check_if_empty (and therefore were not cleaned).
	$updateContactAddressLine2 = clean_data($updateContactAddressLine2);
	$updateContactDepartmentWebsite = clean_data($updateContactDepartmentWebsite);
	$updateContactIndividualWebsite = clean_data($updateContactIndividualWebsite);
	$updateContactCampus = clean_data($updateContactCampus);
	
	//if everything is valid, add instructor contact data to database and redirect to table page.
	if (empty($errorArray))
	{
		//get database connection named $geologyDBConnection
		require('../secure-includes/db-connection.php');
		
		//escape data for mySQL database
		$updateContactFName = mysqli_real_escape_string($geologyDBConnection, $updateContactFName);
		$updateContactLName = mysqli_real_escape_string($geologyDBConnection, $updateContactLName);
		$updateContactEmailAddress = mysqli_real_escape_string($geologyDBConnection, $updateContactEmailAddress);
		$updateContactPhoneNumber = mysqli_real_escape_string($geologyDBConnection, $updateContactPhoneNumber);
		$updateContactState = mysqli_real_escape_string($geologyDBConnection, $updateContactState);
		$updateContactCity = mysqli_real_escape_string($geologyDBConnection, $updateContactCity);
		$updateContactZIP = mysqli_real_escape_string($geologyDBConnection, $updateContactZIP);
		$updateContactAddressLine1 = mysqli_real_escape_string($geologyDBConnection, $updateContactAddressLine1);
		$updateContactInstitution = mysqli_real_escape_string($geologyDBConnection, $updateContactInstitution);
		$updateContactDepartment = mysqli_real_escape_string($geologyDBConnection, $updateContactDepartment);
		$updateContactPrimaryTitle = mysqli_real_escape_string($geologyDBConnection, $updateContactPrimaryTitle);
		$updateContactAddressLine2 = mysqli_real_escape_string($geologyDBConnection, $updateContactAddressLine2);
		$updateContactDepartmentWebsite = mysqli_real_escape_string($geologyDBConnection, $updateContactDepartmentWebsite);
		$updateContactIndividualWebsite = mysqli_real_escape_string($geologyDBConnection, $updateContactIndividualWebsite);
		$updateContactCampus = mysqli_real_escape_string($geologyDBConnection, $updateContactCampus);
		
		//add sql syntax to optional values if any were not empty
		$dataAddressLine2 = empty($updateContactAddressLine2) ? '' : ", address_line_2 = '$updateContactAddressLine2'";
		$dataContactDepartmentWebsite = empty($updateContactDepartmentWebsite) ? '' : ", department_website = '$updateContactDepartmentWebsite'";
		$dataContactIndividualWebsite = empty($updateContactIndividualWebsite) ? '' : ", personal_website = '$updateContactIndividualWebsite'";
		$dataContactCampus = empty($updateContactCampus) ? '' : ", campus = '$updateContactCampus'";
		
		//define sql statement
		$updateContactSQL = "UPDATE geology_instructor_contacts SET first_name = '$updateContactFName', last_name = '$updateContactLName'";
		$updateContactSQL .= ", email = '$updateContactEmailAddress', phone_number = '$updateContactPhoneNumber'";
		$updateContactSQL .= ", state = '$updateContactState', city = '$updateContactCity'";
		$updateContactSQL .= ", zip = '$updateContactZIP', address_line_1 = '$updateContactAddressLine1', institution = '$updateContactInstitution'";
		$updateContactSQL .= ", department = '$updateContactDepartment', instructor_primary_title = '$updateContactPrimaryTitle'";
		
		//optional fields
		$updateContactSQL .= $dataAddressLine2;
		$updateContactSQL .= $dataContactDepartmentWebsite;
		$updateContactSQL .= $dataContactIndividualWebsite;
		$updateContactSQL .= $dataContactCampus;
		
		//only update the one row with the given contact id
		$updateContactSQL .= ' WHERE contact_id = ' . $contact_id;
		
		//insert row into database
		$result = mysqli_query($geologyDBConnection, $updateContactSQL);
		
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
					Date: 12/20/2015
					Geology Contact Table Website
					
					Filename: update-contact.php
					
					This website is intended to provide geology instructors
					with a way to get each other's contact info.
					This page contains a form that is used to update an
					instructor that is already on the contact table.
					
		-->
			
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<title>Geology Instructor Contact Information Table - Update Contact</title>
	
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
							Update an Instructor
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
								//if there was no contact id in the get array, give an error saying so.
								print empty($errorArray['failedToGetContactID']) ? '' : $errorArray['failedToGetContactID'];
								//if there was no instructor with the given contact id, give an error saying so.
								print empty($errorArray['failedToFindInstructor']) ? '' : $errorArray['failedToFindInstructor'];
								//if this editor is not a state or super editor, give an error saying so.
								print empty($errorArray['normalEditorError']) ? '' : $errorArray['normalEditorError'];
								//if a state editor's state and the instructor's state do not match, give an error saying so.
								print empty($errorArray['editorStateDoesNotMatch']) ? '' : $errorArray['editorStateDoesNotMatch'];
								
								?>
							</div>
						</div>
						<div class="form-group">
							<!-- Row 1 -->
							<div class="row">
								<!-- First Name -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactFName">First Name: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactFName'])) ? '' : '<br />' . $errorArray['updateContactFName'];
									?></label>
									<input type="text" class="form-control" id="updateContactFName" name="updateContactFName"
												placeholder="First Name" value="<?php print $updateContactFName; ?>" required="required">
								</div>
								
								<!-- Last Name -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactLName">Last Name: <span class="form-error">*</span>
												<?php
												//if there is an error for this element, print it. otherwise, print an empty string.
												print (empty($errorArray['updateContactLName'])) ? '' : '<br />' . $errorArray['updateContactLName'];
												?></label>
									<input type="text" class="form-control" id="updateContactLName" name="updateContactLName"
												placeholder="Last Name" value="<?php print $updateContactLName; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 2 -->
							<div class="row">
								<!-- Email Address -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactEmailAddress">Email Address: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactEmailAddress'])) ? '' : '<br />' . $errorArray['updateContactEmailAddress'];
									?></label>
									<input type="email" class="form-control" id="updateContactEmailAddress" name="updateContactEmailAddress"
												placeholder="Email Address" value="<?php print $updateContactEmailAddress; ?>" required="required">
								</div>
								
								<!-- Phone Number -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactPhoneNumber">Phone Number:
												<span class="form-error">*</span>
												<?php
												//if there is an error for this element, print it. otherwise, print an empty string.
												print (empty($errorArray['updateContactPhoneNumber'])) ? '' : '<br />' . $errorArray['updateContactPhoneNumber'];
												?></label>
									<input type="text" class="form-control" id="updateContactPhoneNumber" name="updateContactPhoneNumber"
												placeholder="Phone Number" value="<?php print $updateContactPhoneNumber; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 3 -->
							<div class="row">
								<!-- State -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactState">State: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactState'])) ? '' : '<br />' . $errorArray['updateContactState'];
									?></label>
									<select class="form-control" id="updateContactState" name="updateContactState">
										<option value="">Select a State</option>
										<?php create_select_options_list($listOfStates, $updateContactState); ?>
									</select>
								</div>
								
								<!-- City -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactCity">City: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactCity'])) ? '' : '<br />' . $errorArray['updateContactCity'];
									?></label>
									<input type="text" class="form-control" id="updateContactCity" name="updateContactCity"
												placeholder="City" value="<?php print $updateContactCity; ?>" required="required">
								</div>
								
								<!-- ZIP -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactZIP">ZIP: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactZIP'])) ? '' : '<br />' . $errorArray['updateContactZIP'];
									?></label>
									<input type="text" class="form-control" id="updateContactZIP" name="updateContactZIP"
												placeholder="ZIP" value="<?php print $updateContactZIP; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 4 -->
							<div class="row">
								<!-- Address Line 1 -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactAddressLine1">Address Line 1: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactAddressLine1'])) ? '' : '<br />' . $errorArray['updateContactAddressLine1'];
									?></label>
									<input type="text" class="form-control" id="updateContactAddressLine1" name="updateContactAddressLine1"
												placeholder="Address Line 1" value="<?php print $updateContactAddressLine1; ?>" required="required">
								</div>
								
								<!-- Address Line 2 -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactAddressLine2">Address Line 2:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactAddressLine2'])) ? '' : '<br />' . $errorArray['updateContactAddressLine2'];
									?></label>
									<input type="text" class="form-control" id="updateContactAddressLine2" name="updateContactAddressLine2"
												placeholder="Address Line 2" value="<?php print $updateContactAddressLine2; ?>">
								</div>
							</div>
							
							<!-- Row 5 -->
							<div class="row">
								<!-- Institution -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactInstitution">Institution: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactInstitution'])) ? '' : '<br />' . $errorArray['updateContactInstitution'];
									?></label>
									<input type="text" class="form-control" id="updateContactInstitution" name="updateContactInstitution"
												placeholder="Institution" value="<?php print $updateContactInstitution; ?>" required="required">
								</div>
								
								<!-- Department -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactDepartment">Department: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactDepartment'])) ? '' : '<br />' . $errorArray['updateContactDepartment'];
									?></label>
									<input type="text" class="form-control" id="updateContactDepartment" name="updateContactDepartment"
												placeholder="Department" value="<?php print $updateContactDepartment; ?>" required="required">
								</div>
							</div>
							
							<!-- Row 6 -->
							<div class="row">
								<!-- Department Website -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactDepartmentWebsite">Department Website:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactDepartmentWebsite'])) ? '' : '<br />' . $errorArray['updateContactDepartmentWebsite'];
									?></label>
									<input type="text" class="form-control" id="updateContactDepartmentWebsite" name="updateContactDepartmentWebsite"
												placeholder="Department Website" value="<?php print $updateContactDepartmentWebsite; ?>">
								</div>
								
								<!-- Individual Website -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactIndividualWebsite">Individual Website:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactIndividualWebsite'])) ? '' : '<br />' . $errorArray['updateContactIndividualWebsite'];
									?></label>
									<input type="text" class="form-control" id="updateContactIndividualWebsite" name="updateContactIndividualWebsite"
												placeholder="Individual Website" value="<?php print $updateContactIndividualWebsite; ?>">
								</div>
							</div>
							
							<!-- Row 7 -->
							<div class="row">
								<!-- Primary Title/Position -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactPrimaryTitle">Primary Title/Position: <span class="form-error">*</span>
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactPrimaryTitle'])) ? '' : '<br />' . $errorArray['updateContactPrimaryTitle'];
									?></label>
									<input type="text" class="form-control" id="updateContactPrimaryTitle" name="updateContactPrimaryTitle"
												placeholder="Primary Title/Position" value="<?php print $updateContactPrimaryTitle; ?>" required="required">
								</div>
								
								<!-- Campus -->
								<div class="col-xs-12 col-sm-4">
									<label for="updateContactCampus">Campus:
									<?php
									//if there is an error for this element, print it. otherwise, print an empty string.
									print (empty($errorArray['updateContactCampus'])) ? '' : '<br />' . $errorArray['updateContactCampus'];
									?></label>
									<input type="text" class="form-control" id="updateContactCampus" name="updateContactCampus"
												placeholder="Campus" value="<?php print $updateContactCampus; ?>">
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