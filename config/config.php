<?php
$cccldap['emailsuffix'] = 'mycompany.org';
$cccldap['domain'] = 'mydomain.mycompany.org';
$cccldap['ldaptype'] = 1;
$cccldap['ldapserver'] = 'pdc.mycompany.org';
$cccldap['port'] = '389';
$cccldap['basedn']= 'CN=users, DC=mydomain,DC=mycompany,DC=org';
$cccldap['loginfield'] = 'uid';
$cccldap['usersuffix'] = '.LDAP';
$cccldap['createusers'] = true;
$cccldap['email_attribute'] = "userprincipalname";
$cccldap['update_group'] = true;
$cccldap['create_new_match_email'] = false;
$cccldap['allow_duplicate_email'] = true;
$cccldap['notification_email'] = "";

