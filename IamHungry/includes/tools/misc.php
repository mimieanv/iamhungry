<?php

/**
 * Function isEmail
 * Check if $email is really an email address
 * @param String $email
 */
function isEmail($email)
{
	if(!preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $email))
		return false;
	else
		return true;
}  

?>