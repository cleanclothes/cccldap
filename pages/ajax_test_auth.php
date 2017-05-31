<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include_once "../../../include/general.php";


$cccldap['ldapserver'] = getvalescaped('ldapserver','');
$cccldap['ldapuser'] = getvalescaped('ldapuser','');
$cccldap['ldappassword'] = getvalescaped('ldappassword','');
$cccldap['port'] = getvalescaped('port','');
$cccldap['basedn']= getvalescaped('basedn','');
$cccldap['loginfield'] = getvalescaped('loginfield','');
$cccldap['email_attribute'] = getvalescaped('email_attribute','');



	$searchdns=explode(";",$cccldap['basedn']);
	foreach($searchdns as $searchdn)
		{
		$binduserstring = $cccldap['loginfield'] . "=" . $cccldap['ldapuser'] . "," . $searchdn;
		debug("LDAP - Attempting to bind to LDAP server as : " . $binduserstring);
		$login = @ldap_bind( $ds, $binduserstring, $cccldap['ldappassword'] );
		if (!$login){continue;}else{$bindsuccess=true;break;}
		}


$response['bindsuccess']=$bindsuccess?$lang["status-ok"]:$lang["status-fail"];

$userdetails=cccldap_authenticate($cccldap['ldapuser'],$cccldap['ldappassword']);

if($userdetails)
	{
	$response['success'] = true;
	$response['message'] = $lang["status-ok"];
	$response['binduser'] = $userdetails['binduser'];
	$response['username'] = $userdetails['username'];
	$response['displayname'] = $userdetails['displayname'];
	$response['email'] = $userdetails['email'];

	}
else
	{
	$response['success'] = false;
	$response['message'] = $lang["status-fail"];
	}

$response['complete'] = true;

echo json_encode($response);
exit();