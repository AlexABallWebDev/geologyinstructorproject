<?php
/*This file contains the functions used in the geology instructor contact table website.
 */

/*creates a list of options for a select dropdown list from the given valueArray.
 *If the checkedOption is in the valueArray, then it will be selected.
 */
function create_select_options_list($valueArray, $checkedOption)
{
	foreach ($valueArray as $value)
	{
		$item = "<option value='$value'";
		
		//if the array contains the checkedOption item, it should be selected.
		if (!empty($checkedOption) && $value == $checkedOption)
		{
			$item = $item . ' selected';
		}
		$item = $item . ">$value</option>";
		print $item;
	}
}

/*This function cleans data. This function will
 *trim spaces and strip tags for the given variable, returning the cleaned version.
 */
function clean_data($item)
{
	//clean item
	$cleanedItem = trim($item);
	$cleanedItem = strip_tags($cleanedItem);
	
	return $cleanedItem;
}

/*This function checks if a submitted dropdown form input is empty.
 *The itemName is the given name (name attribute on the form) of the input.
 *If the input is empty, an empty string is returned.
 */
function check_dropdown_data($itemName)
{
	if (!empty($_POST[$itemName]))
	{
		return $_POST[$itemName];
	}
	else
	{
		return '';
	}
}

/*This function checks if the given $itemName's value in the POST array is empty.
 *If it is empty, an empty string is returned and the $errorMessage is put into a span of
 *class "form-error" in a string, which is placed into the $errorArray, which is assumed to exist.
 */
function check_if_empty($itemName, $errorMessage)
{
	global $errorArray;
	
	if (!empty($_POST[$itemName]))
	{
		$item = clean_data($_POST[$itemName]);
		return $item;
	}
	else
	{
		$errorArray[$itemName] = "<span class=\"form-error\">$errorMessage</span>";
		return '';
	}
}

/*This function adds an error to $errorArray with a key of $itemName if $item is not in the
 *$validOptions array, and no error is already in $errorArray with a key of $itemName. Does nothing otherwise.
 */
function validate_for_spoofing($item, $itemName, $validOptions)
{
	global $errorArray;
	
	if (!in_array($item, $validOptions) && empty($errorArray[$itemName]))
	{
		$errorArray[$itemName] = '<span class="form-error">You may only choose from the available options!</span>';
	}
}
