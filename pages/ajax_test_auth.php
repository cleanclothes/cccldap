<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include_once "../../../include/general.php";


$cccldap['domain'] = getvalescaped('domain','');
$cccldap['ldapserver'] = getvalescaped('ldapserver','');
$cccldap['ldapuser'] = getvalescaped('ldapuser','');
$cccldap['ldappassword'] = getvalescaped('ldappassword','');
$userdomain = getvalescaped('userdomain','');
$cccldap['port'] = getvalescaped('port','');
$cccldap['ldaptype'] = getvalescaped('ldaptype',1);
$cccldap['basedn']= getvalescaped('basedn','');
$cccldap['loginfield'] = getvalescaped('loginfield','');
$cccldap['email_attribute'] = getvalescaped('email_attribute','');

// Test we can connect to domain
$bindsuccess=false;	
$ds = ldap_connect( $cccldap['ldapserver'],$cccldap['port'] );	
if(!isset($cccldap['ldaptype']) || $cccldap['ldaptype']==1) 
	{
	$binduserstring = $cccldap['ldapuser'] . "@" . $userdomain;
	debug("LDAP - Attempting to bind to AD server as : " . $binduserstring);
	$login = @ldap_bind( $ds, $binduserstring, $cccldap['ldappassword'] );
	if ($login)
		{
		debug("LDAP - Success binding to AD server as : " . $binduserstring);
		$bindsuccess=true;
		}
	else
		{
		debug("LDAP - Failed binding to AD server as : " . $binduserstring);
		}
	}
else
	{
	$searchdns=explode(";",$cccldap['basedn']);
	foreach($searchdns as $searchdn)
		{
		$binduserstring = $cccldap['loginfield'] . "=" . $cccldap['ldapuser'] . "," . $searchdn;
		debug("LDAP - Attempting to bind to LDAP server as : " . $binduserstring);
		$login = @ldap_bind( $ds, $binduserstring, $cccldap['ldappassword'] );
		if (!$login){continue;}else{$bindsuccess=true;break;}
		}
	}			
	
$response['bindsuccess']=$bindsuccess?$lang["status-ok"]:$lang["status-fail"];	
$response['memberof'] = array();

$userdetails=cccldap_authenticate($cccldap['ldapuser'],$cccldap['ldappassword']);

if($userdetails)
	{
	$response['success'] = true;
	$response['message'] = $lang["status-ok"];
	$response['domain'] = $userdetails['domain'];
	$response['binduser'] = $userdetails['binduser'];
	$response['username'] = $userdetails['username'];
	$response['displayname'] = $userdetails['displayname'];
	$response['email'] = $userdetails['email'];
	$response['memberof'] = $userdetails['memberof'];
	}
else
	{
	$response['success'] = false;
	$response['message'] = $lang["status-fail"];
	}

$response['complete'] = true;

echo json_encode($response);
exit();