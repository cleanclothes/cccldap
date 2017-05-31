<?php

/* Note to tinkerers: To create your own custom authentication function, simply replace the function below
   with one of your own design. It needs to return false if the user is not authenticated,
   or an associative array if the user is ok. The array looks like so:
        Array
        (
           [username] => jdoe
           [displayname] => John Doe
           [group] => Marketing
           [email] => doe@acmewidget.com
        )

	The group returned here will be matched up to RS groups using the matching table configured by the user.
	If there is no match, the fallback user group will be used.
*/

function cccldap_authenticate($username,$password){
	if (!function_exists('ldap_connect')){return false;}
	// given a username and password, return false if not authenticated, or
	// associative array of displayname, username, e-mail if valid
	global $cccldap;
	debug("LDAP - Connecting to LDAP server: " . $cccldap['ldapserver'] . " on port " . $cccldap['port']);
	$ds = ldap_connect( $cccldap['ldapserver'],$cccldap['port'] );

	if($ds){
		debug("LDAP - Connected to LDAP server ");
		}

	ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);


	//must always check that password length > 0
	if (!(strlen($password) > 0 && strlen($username) > 0)){
		return false;
		}


	$email_attribute=$cccldap['email_attribute'];
	$attributes = array("displayname",$email_attribute);
	$loginfield=$cccldap['loginfield'];
	$filter = "(&(objectClass=person)(". $loginfield . "=" . $username . "))";

	$searchdns=explode(";",$cccldap['basedn']);
	$dn=array();
	$ldapconnections=array();
	foreach($searchdns as $searchdn)
		{
		debug("LDAP - preparing search DN: " . $searchdn);
		$dn[]=$searchdn;
		}
	for($x=0;$x<count($dn);$x++)
		{
		$ldapconnections[$x] = ldap_connect( $cccldap['ldapserver'],$cccldap['port'] );


			$binduserstring = $cccldap['loginfield'] . "=" . $username . "," . $cccldap['basedn'];

		debug("LDAP - binding as " . $binduserstring);
		if(!(@ldap_bind($ldapconnections[$x], $binduserstring, $password ))){return false;}

		debug("LDAP - searching " . $dn[$x] . " as " . $binduserstring);

    }
	debug("LDAP - performing search: filter=" . $filter);
	debug("LDAP - retrieving attributes: " . implode(",",$attributes));
	$result = ldap_search($ldapconnections, $dn, $filter, $attributes);

	//exit(print_r($result));
	foreach ($result as $value)
		{
		debug("LDAP - search returned value " . $value);
		debug("LDAP - found " . ldap_count_entries($ds,$value) . " entries");
    if(ldap_count_entries($ds,$value)>0)
			{
			$search = $value;
			break;
			}
		}
	if (isset($search))
		{$entries = ldap_get_entries($ds, $search);}
	else
		{
		debug("LDAP - search returned no values");
		return false;
		}



	if($entries["count"] > 0){

		if (isset($entries[0]['displayname']) && count($entries[0]['displayname']) > 0){
			$displayname = $entries[0]['displayname'][0];
		} else {
			$displayname = '';
		}




		//$entry = ldap_first_entry($ds, $search);
		//var_dump($entries);



		//Extract email info
		if ((isset($entries[0][$email_attribute])) && count($entries[0][$email_attribute]) > 0)
			{
			$email = $entries[0][$email_attribute][0];
			}
		else
			{
			$email = $username . '@' . $cccldap['emailsuffix'];;
			}





		$return['username'] = $username;
		$return['binduser'] = $binduserstring;
		$return['displayname'] = $displayname;
		$return['email'] = $email;
		return $return;

	}


	ldap_unbind($ds);



}
