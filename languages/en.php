<?php
# English
# Language File for the cccldap Plugin
# -------
$lang['ldapserver'] = "LDAP Server";
$lang['emailsuffix'] = "Email suffix - used if no email attribute data found";
$lang['port'] = "Port";
$lang['basedn'] = "Base DN. If users are in multiple DNs,separate with semi-colons";
$lang['loginfield'] = "Login Field";
$lang['usersuffix'] = "User Suffix (a dot will be added in front of the suffix)";
$lang['createusers'] = "Create Users";
$lang['ldapvalue'] = "LDAP Value";
$lang['addrow'] = "Add Row";
$lang['email_attribute'] = "Attribute to use for email address";
$lang['cccldap_unknown'] = "unknown";
$lang['cccldap_create_new_match_email'] = "Email-match: Before creating new users, check if LDAP email matches existing RS account email and adopt that account";
$lang['cccldap_allow_duplicate_email'] ="Allow new accounts to be created if there are existing accounts with the same email address? (this is overridden if email-match is set above and one match is found)";
$lang['cccldap_multiple_email_match_subject'] ="ResourceSpace - conflicting email login attempt";
$lang['cccldap_multiple_email_match_text'] ="A new LDAP user has logged in but there is already more than one account with the same email address: ";
$lang['cccldap_notification_email']="Notification address e.g. if duplicate email addresses are registered. If blank none will be sent.";
$lang['cccldap_duplicate_email_error']="There is an existing account with the same email address. Please contact your administrator.";
$lang['cccldap_test'] = "Test LDAP configuration";
$lang['cccldap_testing'] = "Testing LDAP configuration";
$lang['cccldap_connection'] = "Connection to LDAP server";
$lang['cccldap_bind'] = "Bind to LDAP server";
$lang['cccldap_username'] = "Username/User DN";
$lang['cccldap_password'] = "Password";
$lang['cccldap_test_auth'] = "Test authentication";
$lang['cccldap_displayname'] = "Display name";
$lang["cccldap_test_title"] = "Test";
$lang["cccldap_result"] = "Result";
$lang["cccldap_retrieve_user"] = "Retrieve user details";
$lang["cccldap_externsion_required"] = "The PHP LDAP module must be enabled for this plugin to work";
$lang["cccldap_usercomment"] = "Created by cccldap plugin.";
$lang["cccldap_usermatchcomment"] = "Updated to LDAP user by cccldap.";
$lang["origin_cccldap"] = "cccldap plugin";

