<?php

include_once(dirname(__FILE__) . "/../include/cccldap_functions.php");

function HookcccldapAllExternalauth($uname, $pword){
	if (!function_exists('ldap_connect')){return false;}
	global $cccldap;
	global $username;
	global $password_hash, $email_attribute;
	
	// oops - the password is getting escaped earlier in the process, and we don't want that 
    // when it goes to the ldap server. So remove the slashes for this purpose.
    $pword = stripslashes($pword);
	
	$auth = false;
	$authreturn=array();
	if ($uname != "" && $pword != "")
		{
		$userinfo = cccldap_authenticate($uname, $pword);
		//print_r($userinfo);
		if ($userinfo) { $auth = true; }
		} 


		
	if ($auth)
        {
        $usersuffix    = $cccldap['usersuffix'];
        $addsuffix     = ($usersuffix=="")?"":"." . $usersuffix;
        $username      = escape_check($uname . $addsuffix);
        $password_hash = md5('RS' . $username . generateSecureKey());
        $userid        = sql_value("SELECT ref AS `value` FROM user WHERE username = '{$username}'", 0);
        $email         = escape_check($userinfo["email"]);
        $displayname   = escape_check($userinfo['displayname']);

		debug ("LDAP - got user details email: " . $email);


					

		if ($userid > 0){
			// user exists, so update info
			
				{
				sql_query("update user set origin='cccldap', password = '$password_hash', fullname='$displayname', email='$email' where ref = '$userid'");
				}
			return true;
		} else {
			// user authenticated, but does not exist, so create if necessary
			if ($cccldap['createusers']){	
				
				$email_matches=sql_query("select ref, username, fullname from user where email='" . $email . "'");				
												
				if(count($email_matches)>0)
					{				
					if(count($email_matches)==1 && $cccldap['create_new_match_email'])
						{
						// We want adopt this matching account - update the username and details to match the new login credentials
						debug("LDAP - user authenticated with matching email for existing user . " . $email . ", updating user account " . $email_matches[0]["username"] . " to new username " . $username);
							{
							sql_query("update user set origin='cccldap',username='$username', password='$password_hash', fullname='$displayname',email='$email',comments=concat(comments,'\n" . date("Y-m-d") . " Updated to LDAP user by cccldap.') where ref='" . $email_matches[0]["ref"] . "'");
							}
						return true;
						}
						
					if (isset($cccldap['notification_email']) && $cccldap['notification_email']!="")
						{
						// Already account(s) with this email address, notify the administrator
						global $lang, $baseurl, $email_from;
						debug("LDAP - user authenticated with matching email for existing users: " . $email);
						$emailtext=$lang['cccldap_multiple_email_match_text'] . " " . $email . "<br /><br />";
						$emailtext.="<table class=\"InfoTable\" border=1>";
						$emailtext.="<tr><th>" . $lang["property-name"] . "</th><th>" . $lang["property-reference"] . "</th><th>" . $lang["username"] . "</th></tr>";
						foreach($email_matches as $email_match)
							{
							$emailtext.="<tr><td><a href=\"" . $baseurl . "/?u=" . $email_match["ref"] .  "\" target=\"_blank\">" . $email_match["fullname"] . "</a></td><td><a href=\"" . $baseurl . "/?u=" . $email_match["ref"] .  "\" target=\"_blank\">" . $email_match["ref"] . "</a></td><td>" . $email_match["username"] . "</td></tr>\n";
							}
						
						$emailtext.="</table>";
						send_mail($cccldap['notification_email'],$lang['cccldap_multiple_email_match_subject'],$emailtext,$email_from);
						}
							
				
					if(!$cccldap['allow_duplicate_email'])
						{
						// We are blocking accounts with the same email
						$authreturn["error"]=$lang['cccldap_duplicate_email_error'];
						return $authreturn;
						}										
					}
			
				// Create the user
				$ref=new_user($username);
				if (!$ref) { echo "returning false!"; exit; return false;} // this shouldn't ever happen
				

				
				
				
				
				return true;
			} else {
				// user creation is disabled, so return false
				return false;
			}

		}
	

	} else {
		// user is not authorized
		return false;
	}


}
		
?>
