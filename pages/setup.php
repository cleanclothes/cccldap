<?php
include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("u")) {exit ("Permission denied.");}
include_once "../../../include/general.php";

$plugin_name="cccldap";
if(!in_array($plugin_name, $plugins))
	{plugin_activate_for_setup($plugin_name);}
	
$upload_status="";

if (getval('upload','')!='')
       {
       $upload_status=handle_rsc_upload($plugin_name);
       }
elseif (getval("submit","")!="" || getval("save","")!="" || getval("testConnflag","")!="")
	{

	$cccldap['emailsuffix'] = getvalescaped('emailsuffix','');
	$cccldap['ldapserver'] = getvalescaped('ldapserver','');
	$cccldap['port'] = getvalescaped('port','');
	$cccldap['basedn']= getvalescaped('basedn','');
	$cccldap['loginfield'] = getvalescaped('loginfield','');
	$cccldap['usersuffix'] = getvalescaped('usersuffix','');
	$cccldap['createusers'] = getvalescaped('createusers','');
	$cccldap['email_attribute'] = getvalescaped('email_attribute','');
	$cccldap['create_new_match_email'] = getvalescaped('create_new_match_email','');
	$cccldap['allow_duplicate_email'] = getvalescaped('allow_duplicate_email','');
	$cccldap['notification_email'] = getvalescaped('notification_email','');
	$cccldap['ldaptype'] = getvalescaped('ldaptype','');
	
	
	



	//$config['cccldap'] = $cccldap;
	if (getval("submit","")!="" || getval("save","")!="")
		{
		set_plugin_config("cccldap",array("cccldap"=>$cccldap));
		}
		
	if (getval("submit","")!="")
		{
		redirect("pages/team/team_plugins.php");
		}
	}





include "../../../include/header.php";

// if some of the values aren't set yet, fudge them so we don't get an undefined error
// this may be important for updates to the plugin that introduce new variables
foreach (array('ldapserver','port','basedn','loginfield','usersuffix','emailsuffix','email_attribute','create_new_match_email','allow_duplicate_email','notification_email','ldaptype') as $thefield){
	if (!isset($cccldap[$thefield])){
		$cccldap[$thefield] = '';
	}
}


if(getval("testConnflag","")!="" && getval("submit","")=="" && getval("save","")=="")
		{
		?>
		<div class="BasicsBox"> 
		<?php
		echo "<h1>" . $lang["cccldap_test"] . " " . $cccldap['ldapserver'] . ":" . $cccldap['port'] ."</h1>";
		
		debug("LDAP - Connecting to LDAP server: " . $cccldap['ldapserver'] . " on port " . $cccldap['port']);
		$dstestconn=  @fsockopen($cccldap['ldapserver'], $cccldap['port'], $errno, $errstr, 5);
		
		if($dstestconn)
			{
			fclose($dstestconn);
			debug("LDAP - Connected to LDAP server ");
			?>
			<div class="Question">
			<label for="ldapuser"><?php echo $lang["cccldap_username"] ?></label><input id='ldapuser' type="text" name='ldapuser'>
			</div>
			
			<div class="Question">
			<label for="ldappassword"><?php echo $lang["cccldap_password"] ?></label><input id='ldappassword' type="password" name='ldappassword'>
			</div>		


		
		<input type="submit" onClick="cccldap_test();return false;" name="testauth" value="<?php echo $lang["cccldap_test_auth"]; ?>" <?php if (!$dstestconn){echo "disabled='true'";} ?>>		
		<input type="submit" onClick="ModalClose();return false;" name="cancel" value="<?php echo $lang["cancel"]; ?>">
		
		<br /><br />
		<!--<textarea id="cccldaptestresults" class="Fixed" rows=15 cols=100 style="display: none; width: 100%; border: solid 1px;" ></textarea>-->
		
		<script>
		function cccldap_test()
			{
			jQuery('.resultrow').remove();
			jQuery('#testgetuserresult').html('');
			testurl= '<?php echo get_plugin_path("cccldap",true) . "/pages/ajax_test_auth.php";?>',
			user = jQuery('#ldapuser').val();
			password = jQuery('#ldappassword').val();
			var post_data = {
				ajax: true,
				ldapserver: '<?php echo htmlspecialchars($cccldap['ldapserver']) ?>',
				port: '<?php echo htmlspecialchars($cccldap['port']) ?>',
				ldaptype: '<?php echo htmlspecialchars($cccldap['ldaptype']) ?>',
				loginfield: '<?php echo htmlspecialchars($cccldap['loginfield']) ?>',				
				basedn: '<?php echo htmlspecialchars($cccldap['basedn']) ?>',	
				email_attribute: '<?php echo htmlspecialchars($cccldap['email_attribute']) ?>',
				ldapuser: user,
				ldappassword: password
			
				
			};
			
			jQuery.ajax({
				  type: 'POST',
				  url: testurl,
				  data: post_data,
				  dataType: 'json', 
				  success: function(response){
						if(response.complete === true){
						
						jQuery('#testbindresult').html(response.bindsuccess);
						if(response.success){
							jQuery('#testgetuserresult').html('<?php echo $lang["status-ok"]; ?> (' + response.binduser + ')');
						}
						else {
							jQuery('#testgetuserresult').html('<?php echo $lang["status-fail"]; ?>');
						}
							
												
						returnmessage = response.message;
						if(response.success) {						
							returnmessage += "<tr class='resultrow'><td><?php echo $lang["email"]; ?>: </td><td>" + response.email + "</td></tr>";
	
							returnmessage += "</td></tr>";
						}
						jQuery('#blankrow').before(returnmessage);
					}
					else if(response.complete === false && response.message && response.message.length > 0) {
						jQuery('#testgetuserdata').html('<?php echo $lang["error"]; ?> : ' + response.message);
					}
					else {
						jQuery('#testgetuserdata').html('<?php echo $lang["error"]; ?>');
					}
				},
				  error: function(xhr, textStatus, error){
					  jQuery('#cccldaptestresults').html(textStatus + ":&nbsp;" + xhr.status    + "&nbsp;" + error  );
				}
			});
			
			}
		
		</script>
		<?php
		
		echo "<table class='InfoTable' style='width: 100%' ><tbody>";
		echo "<tr><td width='40%'><h2>" .  $lang["cccldap_test_title"] . "</h2></td><td width='60%'><h2>" . $lang["cccldap_result"] . "</h2></td></tr>";
		echo "<tr><td>" . $lang["cccldap_connection"] . " " . $cccldap['ldapserver'] . ":" . $cccldap['port'] . "</td><td id='testconnectionresult'>" . (($dstestconn)?$lang["status-ok"]:$lang["status-fail"]) . "</td></tr>";
		echo "<tr><td>" . $lang["cccldap_bind"] . "</td><td id='testbindresult'></td></tr>";
		echo "<tr><td>" . $lang["cccldap_retrieve_user"] . "</td><td id='testgetuserresult'></td></tr>";
		echo "<tr id='blankrow'><td colspan='2' ></td></tr>";				
		echo "</tbody></table>";
		?>
		</div>
		<?php
		exit();
		}	
		


?>
<div class="BasicsBox"> 
  <h2>&nbsp;</h2>
 
<?php 
if (!function_exists('ldap_connect'))
	{
	echo "<div class=\"PageInformal\">" . $lang["cccldap_externsion_required"] . "</div>";
	}
	
?>
 <h1>cccldap Configuration</h1>
  
 <form id="form1" name="form1" enctype= "multipart/form-data" method="post" action="<?php echo get_plugin_path("cccldap",true) . "/pages/setup.php";?>">

<?php echo config_single_select("ldaptype", $lang['cccldap_ldaptype'], $cccldap['ldaptype'], array(1=>"Active Directory",2=>"Oracle Directory")); ?>
<?php echo config_text_field("ldapserver",$lang['ldapserver'],$cccldap['ldapserver'],60);?>
<?php echo config_text_field("emailsuffix",$lang['emailsuffix'],$cccldap['emailsuffix'],60);?>
<?php echo config_text_field("email_attribute",$lang['email_attribute'],$cccldap['email_attribute'],60);?>
<?php echo config_text_field("port",$lang['port'],$cccldap['port'],5);?>
<?php echo config_text_field("basedn",$lang['basedn'],$cccldap['basedn'],60);?>
<?php echo config_text_field("loginfield",$lang['loginfield'],$cccldap['loginfield'],30);?>
<?php echo config_text_field("usersuffix",$lang['usersuffix'],$cccldap['usersuffix'],30);?>
<?php echo config_boolean_field("createusers",$lang['createusers'],$cccldap['createusers'],30);?>
<?php echo config_boolean_field("create_new_match_email",$lang['cccldap_create_new_match_email'],$cccldap['create_new_match_email'],30);?>
<?php echo config_boolean_field("allow_duplicate_email",$lang['cccldap_allow_duplicate_email'],$cccldap['allow_duplicate_email'],30);?>
<?php echo config_text_field("notification_email",$lang['cccldap_notification_email'],$cccldap['notification_email'],60);?>


<div class="clearerleft"></div>






<div class="Question">
	<input type="hidden" name="testConnflag" id="testConnflag" value="" />
	<input type="submit" name="testConn" onclick="jQuery('#testConnflag').val('true');ModalPost(this.form,true);return false;" value="<?php echo $lang['cccldap_test'] ?>" />
 </div>
<div class="clearerleft"></div>

<div class="Question">
	<label for="submit"></label>
<input type="submit" name="save" value="<?php echo $lang["save"]?>">
<input type="submit" name="submit" value="<?php echo $lang["plugins-saveandexit"]?>">

</div>
<div class="clearerleft"></div>


<?php
    display_rsc_upload($upload_status);
?>

</form>
</div>	
<?php include "../../../include/footer.php";