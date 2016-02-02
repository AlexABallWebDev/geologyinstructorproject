/*
	This file contains the MySQL code to create the tables needed for the
	geology instructor contacts table.
*/

/*This is the database that is used for the entire contacts table website*/
USE alexb_geology_instructor_contacts;

/*destroy current tables if they already exist*/
DROP TABLE IF EXISTS geology_instructor_contacts;
DROP TABLE IF EXISTS geology_instructor_editors;

/*table for the geology instructor contacts table*/
CREATE TABLE geology_instructor_contacts (
	/*primary key*/
	contact_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	
	first_name VARCHAR(100) NOT NULL,
	last_name VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL,
	phone_number VARCHAR(30) NOT NULL,
	state VARCHAR(50) NOT NULL,
	city VARCHAR(100) NOT NULL,
	zip VARCHAR(30) NOT NULL,
	address_line_1 VARCHAR(100) NOT NULL,
	address_line_2 VARCHAR(100) DEFAULT NULL,
	institution VARCHAR(100) NOT NULL,
	department VARCHAR(100) NOT NULL,
	department_website VARCHAR(200) DEFAULT NULL,
	personal_website VARCHAR(200) DEFAULT NULL,
	instructor_primary_title VARCHAR(100) NOT NULL,
	campus VARCHAR(100) DEFAULT NULL,

	timestamp DATETIME NOT NULL
);

/*table for editor account information*/
CREATE TABLE geology_instructor_editors (
	/*primary key*/
	editor_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	
	email VARCHAR(200) UNIQUE NOT NULL,
	password VARCHAR(100) NOT NULL,
	
	/*If this user is a state user, then their state abbreviation is saved here*/
	editor_state VARCHAR(20) NOT NULL DEFAULT 'Not a State Editor',
	super_user BOOLEAN NOT NULL DEFAULT 0
);




