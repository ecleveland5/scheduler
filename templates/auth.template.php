<?php
/**
* This file provides output functions for all auth pages
* No data manipulation is done in this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 03-04-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

$link = CmnFns::getNewLink();	// Get Link object

/**
 * Prints out a form for users can register
 *  filling in any values
 * @param boolean $edit whether this is an edit or a new register
 * @param array $data values to auto fill
 * @param string $msg error message to echo to user
 */
function printRegisterForm($edit, $data=array(), $msg='', $signin='', $auth=null) {
	global $conf;
	//echo "<br><br>vardump from Auth->template::printRegisterForm():<br />";
	//var_dump($data);
	if (empty($data)) {
		$data = array('email'=>'', 		'salutation'=>'', 	'first_name'=>'', 	'last_name'=>'', 'organization'=>'',
				'password'=>'', 	'user_type_id'=>'', 'advisor'=>'', 		'receive_announcements'=>'',
				'work_address'=>'', 	'work_address2'=>'', 	'work_address3'=>'', 	'work_city'=>'', 'work_state'=>'', 'work_zip'=>'', 'work_country'=>'',
				'work_phone'=>'', 		'webpage'=>'',		'groupsite'=>'', 		'biography'=>'',
				'lab_access'=>array(), 'umd_card_number'=>'', 'billing_organization'=>'', 'billing_contact_name'=>'', 'billing_email'=>'',
				'billing_phone'=>'',	'billing_address1'=>'', 'billing_address2'=>'', 'billing_address3'=>'', 'billing_city'=>'',
				'billing_state'=>'', 	'billing_zip'=>'',	'billing_po_issued'=>'', 'billing_po_number'=>''
		);
	}
	
	if(!isset($data['lab_access'])) {
		$data['lab_access'] = array();
	}
	
	extract($data);

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);

	?>
<script type="text/javascript">
	var valid=true;
	var edit=<?php if ($edit) { echo 'true'; }else{ echo 'false';} ?>;
	//var RecaptchaOptions = { theme : 'white' };

	function validateRegistrationData() {
		var theForm = document.getElementById('registerForm');
		valid = true;
		with (theForm) {
			if (checkEmptyField(email.value)) {
				throwDivError('email', 'Please enter your email.');
				valid = false;
			} else {
				clearDivError('email');
				if (isValidEmail(email.value)) {
					checkEmailTaken(email.value);
				} else {
					throwDivError('email', 'Please enter a valid email.');
					valid = false;
				}
			}
			if(checkEmptyField(password1.value)) {
				throwDivError('password', 'Please enter your password.');
				valid = false;
			}
			if (checkEmptyField(password2.value)) {
				throwDivError('password', 'Please verify your password.');
				valid = false;
			}
	    re = /[0-9]/;
	    if (password1.value.length < 8) {
	    	throwDivError('password', "Your new password must contain at least eight characters!");
	    	valid = false;
	    } else if (!re.test(password1.value)) {
	      throwDivError('password', "Your new password must contain at least one number (0-9)!");
	      valid = false;
	    } else if (password1.value != password2.value) {
		    throwDivError('password', "Your new password did not verify correctly.");
		    valid = false;
	    } else {
		    clearDivError('password');
	    }
			if(checkEmptyField(first_name.value) || checkEmptyField(last_name.value)) {
				throwDivError('name', 'Please enter your first and last names.');
				valid = false;
			}else{
				clearDivError('name');
			}
			if(checkEmptyField(type_id.value)) {
				throwDivError('type_id', 'Please specify your user type.');
				valid = false;
			}else{
				clearDivError('type_id');
			}
		}
		console.log(valid);
		return valid;
	}

	function checkEmptyField(field) {
		if(field=='' || field==null) {
			return true;
		}else{
			return false;
		}
	}

	function throwDivError(divID, errorMsg) {
		$('#'+divID+'_div').addClass('error');
		document.getElementById(divID+'_msg').innerHTML = errorMsg;
		valid = false;
	}

	function clearDivError(divID) {
		$('#'+divID+'_div').removeClass('error');
		document.getElementById(divID+'_msg').innerHTML = "";
	}

	function updateEmailTakenDiv(data, theEmail) {
		if(data==1) {
			throwDivError('email', 'That email is already in use.  \<a href="?e='+theEmail+'"\>Request password?\</a\>');
		}else{
			clearDivError('email');
		}
	}

	function checkEmailTaken(theEmail) {
		if(!edit) {
			$.post("<?php echo URLPATH;?>/ajax.php", {  a:"check_email_taken", email:theEmail }, function(data) {
				updateEmailTakenDiv(data, theEmail);
			});
		}
		return valid;
	}

	function isValidEmail(strEmail) {
		validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
		// search email text for regular exp matches
		if (strEmail.search(validRegExp) == -1) {
			return false;
		}
		return true;
	}

	function submitForm() {
	    if (validateRegistrationData()) {
		    console.log('submit fired.');
		    $("#registerForm").submit();
		}
	}
</script>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="registerForm" method="post" name="nanocenter_registration" onsubmit="return validateRegistrationData();">
<input type="hidden" name="register" value="1">
<div id="website_div" class="section_header">
	Contact Information
</div>

<div id="email_div" class="field required">
	<div id="email_label" class="input_label">Email / Username</div>
	<div id="email_input" class="input"><input type="email" name="email" title="Email" onblur="checkEmailTaken(this.value)" value="<?php echo $email; ?>" /></div>
	<div id="email_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<?php
	if(!$edit) {
?>
<div id="password_div" class="field required">
	<div id="password_label" class="input_label">Password</div>
	<div id="password_input" class="input">
		<input type="password" name="password" id="password1" title="Password" />
		<span class="input_desc">Must be at least 8 characters and 1 number.</span>
		<br />
		
		<input type="password" name="password2" id="password2" title="Password Verification" />
		<span class="input_desc">Verify</span>
	</div>
	<div id="password_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
<?php
	} else {
?>
<div id="password_div" class="field required">
	<div id="password_label" class="input_label">Password</div>
	<div id="password_input" class="input">
		<p><a href="<?php //echo $conf['app']['weburi'];?>/change_pass.php">Click Here to Change Your Password</a></p>
	</div>
	<div id="password_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<?php
	} 
?>

<div id="name_div" class="field required">
	<div id="name_label" class="input_label">Name</div>
	<div id="name_input" class="input">
		<select name="salutation" title="Salutation">
			<option value="Mr."	  <?php if ($salutation=='Mr.') echo 'selected="selected"'; ?>>Mr.</option>
			<option value="Mrs."  <?php if ($salutation=='Mrs.') echo 'selected="selected"'; ?>>Mrs.</option>
			<option value="Ms."   <?php if ($salutation=='Ms.') echo 'selected="selected"'; ?>>Ms.</option>
			<option value="Dr."   <?php if ($salutation=='Dr.') echo 'selected="selected"'; ?>>Dr.</option>
			<option value="Prof." <?php if ($salutation=='Prof.') echo 'selected="selected"'; ?>>Prof.</option>
		</select>
		<input type="text" name="first_name" title="First Name" value="<?php echo $first_name; ?>" />
		<input type="text" name="last_name" title="Last Name" value="<?php echo $last_name; ?>" />
		<span class="input_desc">First, Last</span>
	</div>
	<div id="name_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
<?php if (!is_null($auth) && get_class($auth)==="NCAuth") {?>
<div id="type_id_div" class="field required">
	<div id="type_id_label" class="input_label">Type</div>
	<div id="type_id_input" class="input" style="vertical-align: middle">
		<?php $auth->printUserTypeSelectBox('type_id', 'User Type', (isset($type_id) ? $type_id : null)); ?>
		<br /><br /><span class="input_desc">If you are a UMD Student please select your advisor.</span><br />
		<?php $auth->printAdvisorsSelectBox('advisor', 'Advisor', $advisor); ?>
	</div>
	<div id="type_id_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
<?php } ?>

<div id="receive_announcements_div" class="field required">
	<div id="receive_announcements_label" class="input_label">Receive Email Announcements?</div>
	<div id="receive_announcements_input" class="input" style="vertical-align: middle">
		<label for="receive_announcements1"><input type="radio" name="receive_announcements" id="receive_announcements1" value="1" title="Receive Email Announcements" <?php if ($receive_announcements==1) echo 'checked="checked"'; ?> />Yes</label>
		<br />
		<label for="receive_announcements2"><input type="radio" name="receive_announcements" id="receive_announcements2" value="0" title="Receive Email Announcements" <?php if ($receive_announcements==0) echo 'checked="checked"'; ?> />No</label>
	</div>
	<div id="receive_announcements_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<div id="organization_div" class="field">
	<div id="organization_label" class="input_label">Organization</div>
	<div id="organization_input" class="input" style="vertical-align: middle">
		<input type="text" name="organization" title="Organization" value="<?php echo $organization; ?>" /><br />
	</div>
	<div id="organization_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<div id="contact_info_div" class="field">
	<div id="contact_info_label" class="input_label">Contact Information</div>
	<div id="contact_info_input" class="input" style="vertical-align: middle">
		<input type="text" name="work_address" title="Address 1" value="<?php echo $work_address; ?>" /> <span class="input_desc">Address 1</span><br />
		<input type="text" name="work_address2" title="Address 2" value="<?php echo $work_address2; ?>" /> <span class="input_desc">Address 2</span><br />
		<input type="text" name="work_city" title="City" value="<?php echo $work_city; ?>" /> <span class="input_desc">City</span><br />
		<?php CmnFns::printStateSelectBox('work_state', $work_state); ?> <span class="input_desc">State</span><br />
		<input type="number" name="work_zip" size="5" title="Zip" value="<?php echo $work_zip; ?>" /> <span class="input_desc">Zip</span><br />
		<?php CmnFns::printCountrySelectBox('work_country', $work_country); ?> <span class="input_desc">Country</span><br />
		<input type="tel" name="work_phone" title="Phone Number: xxx-xxx-xxxx" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="xxx-xxx-xxxx" value="<?php echo $work_phone; ?>" /> <span class="input_desc">Phone</span><br />
	</div>
	<div id="contact_info_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<div id="researcher_id_div" class="field">
	<div id="researcher_id_label" class="input_label">Researcher ID</div>
	<div id="researcher_id_input" class="input" style="vertical-align: middle">
		<input type="text" name="researcher_id" title="Researcher ID" placeholder="" value="<?php echo (isset($researcher_id) ? $researcher_id : ''); ?>" />
		<span class="input_desc"><a href="http://www.researcherid.com/" target="_blank">What is Researcher ID?</a></span><br />
	</div>
	<div id="website_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
<!--
<div id="orcid_div" class="field">
	<div id="orcid_label" class="input_label">ORCID</div>
	<div id="orcid_input" class="input" style="vertical-align: middle">
		<input type="text" name="orcid" title="ORCID" placeholder="" value="<?php echo (isset($orcid) ? $orcid : ''); ?>" />
		<span class="input_desc"><a href="http://en.wikipedia.org/wiki/ORCID" target="_blank">What is ORCID?</a></span><br />
	</div>
	<div id="website_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
-->
<div id="website_div" class="field">
	<div id="website_label" class="input_label">Website</div>
	<div id="website_input" class="input" style="vertical-align: middle">
		<input type="url" name="webpage" title="Website: http://www.website.com" placeholder="http://" value="<?php echo $webpage; ?>" />
		<span class="input_desc">http://www.website.com</span><br />
	</div>
	<div id="website_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<div id="biography_div" class="field">
	<div id="biography_label" class="input_label">Biography</div>
	<div id="biography_input" class="input" style="vertical-align: middle">
		<textarea name="biography" title="Biography" rows="3"><?php echo $biography; ?></textarea>
	</div>
	<div id="biography_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>

<!-- FabLab Access Information -->
<div id="website_div" class="section_header">
	NanoCenter Lab Access
</div>

<div id="umd_card_number_div" class="field">
	<div id="umd_card_number_label" class="input_label">UMD ID Card Number</div>
	<div id="umd_card_number_input" class="input" style="vertical-align: middle">
		<input type="text" name="umd_uid" title="UMD ID Card Number" value="<?php echo (isset($umd_uid) ? $umd_uid : ''); ?>" />
		<span class="input_desc">Needed for Card Access</span><br />
	</div>
	<div id="umd_card_number_msg" class="input_msg"></div>
	<div class="clear"></div>
</div>
<?php if ($edit) { ?>
<div id="billing_link" class="field">
	<div id="billing_link_label" class=""><a href="<?php echo $conf['app']['weburi'];?>/my_accounts.php">Click Here to Add/Remove/Modify Your Billing Accounts</a></div>
	<div class="clear"></div>
</div>
<?php } ?>
<div id="submit_button_div" class="field">
	<div id="submit_button_input" class="input" style="vertical-align: middle; float: right; text-align: right;">
		<?php
		if ($edit) {
		?>
		<input type="submit" name="submit" value="Update">
		<?php
		} else {
		?>
		<script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="6LdHdDUUAAAAAFYo_rld_DNbqAX_LZ5NgZKridUr"></div>
        <!-- <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div> -->
		<input type="submit" name="submit" value="Submit">
		<?php
		}
		?>
	</div>
	<div class="clear"></div>
</div>
</form>
<?php
}


/**
* DEPRECATED
* Prints out a form for users can register
* filling in any values
* @param boolean $edit whether this is an edit or a new register
* @param array $data values to auto fill
* @param string $msg error message to print to user
*/
function print_register_form($edit, $data = array(), $msg = '', $signin='') {
	global $conf;

	//var_dump($data);
	$positions    = $conf['ui']['positions'];		// Postions that are availble in the pull down menu
	$institutions = $conf['ui']['institutions'];	// Institutions that are available in the pull down menu
	$use_logonname = (bool)$conf['app']['useLogonName'];	// If we are using logon name or email for authentication

	$user_id			= $data['user_id'];
	$email 				= $data['email'];
	$first_name			= $data['first_name'];
	$last_name			= $data['last_name'];

	$rank				= $data['rank'];
	$cell_phone			= $data['cell_phone'];
	$home_phone			= $data['home_phone'];
	$work_title			= $data['work_title'];
	$work_address		= $data['work_address'];
	$work_address2		= $data['work_address2'];
	$work_city			= $data['work_city'];
	$work_state			= $data['work_state'];
	$work_country		= $data['work_country'];
	$work_zip			= $data['work_zip'];
	$work_phone			= $data['work_phone'];
	$timestamp_added	= $data['timestamp_added'];
	$last_login			= $data['last_login'];
	$university			= $data['university'];
	$biography			= $data['biography'];
	$affiliations		= $data['affiliations'];
	$visibility			= $data['visibility'];
	$webpage			= $data['webpage'];
	$organization		= $data['organization'];
	$department			= $data['department'];
	$advisor			= $data['advisor'];
	$comments			= $data['comments'];
	$ps					= $data['ps'];
	$type_id			= $data['type_id'];
	$exec_type_id		= $data['exec_type_id'];
	$research_interests	= $data['research_interests'];
	$password			= $data['password'];
	$rights				= $data['rights'];
	$department_id		= $data['department_id'];
	$relationship		= $data['relationship'];
	$supervisor			= $data['supervisor'];
	$register_status	= $data['register_status'];
	$intranet_access	= $data['intranet_access'];
	$receive_announcements	= $data['receive_announcements'];
	$publish_email_on_site	= $data['publish_email_on_site'];
	$is_collaborator	= $data['is_collaborator'];
	$collaboration_info	= $data['collaboration_info'];
	$login_count		= $data['login_count'];

	$institution		= $data['institution'];
	$position			= $data['position'];

	// Print header
	echo '<h3 align="center">' . (($edit) ? translate('Please edit your profile') : translate('Please register')) . '</h3>' . "\n";

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);

?>
<form name="register" method="post" action="<?php print  $_SERVER['PHP_SELF'] . '?' . ($edit ? 'edit=' . $edit : ''); ?>">
<center>
<table border="0" cellpadding="4" cellspacing="1" style="background-color: #000000;" width="675">
	<tr style="background-color: #880000;">
		<td colspan="2"><strong><font color="#ffffff">General Information</font></strong></td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC" width="45%"><strong>Email</strong> <font color="red" size=+1><b>*</b></font><br>
			will serve as your username login
		</td>
		<td class="register_form_field" style="background-color:#FF99CC">
			<input type="text" name="email" value="<?php print $email; ?>" size="50"><br>
		</td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><b>First Name</b> <font color="red" size=+1><b>*</b></font></td>
		<td class="register_form_field" style="background-color:#FF99CC"><input type="text" name="first_name" value="<?php print $first_name; ?>" size="50"></td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><b>Last Name</b> <font color="red" size=+1><b>*</b></font></td>
		<td class="register_form_field" style="background-color:#FF99CC"><input type="text" name="last_name" value="<?php print $last_name; ?>" size="50"></td>
	</tr>


	<?php if (!$edit || Auth::isAdmin()) {
	?>
	<tr>
		<td class="register_form_field" style="background-color:#FF99CC"><strong>User Type</strong> <font color="red" size=+1><b>*</b></font></td>
		<td class="register_form_field" style="background-color:#FF99CC">
<?php
	$user_type_sql="SELECT * FROM user_type WHERE user_type_id > 2 order by title";
	$user_type_rs=mysql_query($user_type_sql);
	while ($user_type_row=mysql_fetch_assoc($user_type_rs)){
?>
			<input type="radio" name="type_id" value="<?php print $user_type_row['user_type_id'];?>" <?php print ($type_id == $user_type_row['user_type_id']) ? "checked" : "";?><?php
				if ($user_type_row['user_type_id']==5){
					?> onclick="show('advisor_div');"<?php
				}else{
					?> onclick="hide('advisor_div');"<?php
				} ?>><?php print $user_type_row['title'];?><br>
<?php
	}
?>
<br />
<div id="advisor_div">
	Please identify your advisor:<br />
	<font size="1">If you advisor is not listed please specify in the NanoCenter Relation box.</font>
	<br />
	<select name="advisor" id="advisor">
	<option value="">Select Advisor</option>
<?php
	$advisors = mysql_query("SELECT user_id, first_name, last_name FROM user WHERE type_id = 3 ORDER BY last_name, first_name");
	while($row = mysql_fetch_assoc($advisors)){
?>
	<option value="<?php echo $row['user_id']; ?>" <?php print ($advisor == $row['user_id']) ? "selected" : ""; ?>><?php echo $row['last_name'] . ", " . $row['first_name']; ?></option>
<?php
	}
?>
	</select>
</div>
<?php
	if ($advisor == ''){
?>
<script>
hide('advisor_div');
</script>
<?php	}	?>
		</td class="register_form_field">
	</tr>
	<?php
	}  // end of if $edit
	?>

	<tr bgcolor="#efefef"><td class="register_form_field"><b>Title</b></td>
		<td class="register_form_field"><input type="text" name="work_title" value="<?php echo $work_title?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Organization</b><br />
			<div align="left">Company name, University, Gov agency, etc.</div>
		</td>
		<td class="register_form_field"><input type="text" name="organization" value="<?php echo $organization?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Department</b></td>
		<td class="register_form_field"><input type="text" name="department" value="<?php echo $department?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Work Address</b></td>
		<td class="register_form_field"><input type="text" name="work_address" value="<?php echo $work_address?>" size="50"><br>
			<input type="text" name="work_address2" value="<?php echo $work_address2?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>City</b></td>
		<td class="register_form_field"><input type="text" name="work_city" value="<?php echo $work_city?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>State</b></td>
		<td class="register_form_field"><input type="text" name="work_state" value="<?php echo $work_state?>" size="2"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Postal Code</b></td>
		<td class="register_form_field"><input type="text" name="work_zip" value="<?php echo $work_zip?>" size="12"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Country</b></td>
		<td class="register_form_field"><input type="text" name="work_country" value="<?php echo $work_country?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Work Phone</b><br><font size="1">xxx-xxx-xxxx ext: xxxx</font></td>
		<td class="register_form_field"><input type="text" name="work_phone" value="<?php echo $work_phone?>" size="19" maxlength="25"></td>
	</tr>
	<tr bgcolor="#efefef"><td class="register_form_field"><b>Website</b></td>
		<td class="register_form_field"><input type="text" name="webpage" value="<?php echo $webpage?>" size="50"></td>
	</tr>
	<tr style="background-color: #880000;">
		<td colspan="2"><font color="#ffffff"><strong>Biography</strong></font></td>
	</tr>
	<tr bgcolor="#efefef">
		<td colspan="2" class="register_form_field">This information will be displayed on your Maryland NanoCenter user page, along with your contact information.<br></td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><strong>Are you a NanoCenter collaborator?</strong></td>
		<td class="register_form_field" style="background-color:#FF99CC">
		<div><font size="1">A collaborator is a person outside UMD who has co-authored a paper with a UMD nanocenter faculty member within the past 5 years. Please list the paper title, name of the UMD faculty, where it was published and the approximate publication date in the box below.</font></div><br />
		<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
		<input type="radio" name="is_collaborator" id="is_collaborator[0]" value="1" <?php print $is_collaborator ? "checked" : "";?>>Yes<br>
		<input type="radio" name="is_collaborator" id="is_collaborator[1]" value="0" <?php print !$is_collaborator ? "checked" : "";?> onClick="javascript: document.getElementById('collaboration_info').value='';">No
		</td>
		<td align="center">
		<textarea name="collaboration_info" id="collaboration_info" cols="22" rows="3" onKeyPress="javascript: document.getElementById('is_collaborator[0]').checked = true;"><?php echo $collaboration_info; ?></textarea>
		</td></tr></table>
		</td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><strong>Nanocenter Relation</strong> <font color="red" size=+1><b>*</b></font><br>
			<div align="left"><font size="1">Please describe in 1 or 2 sentences your involvement with Maryland NanoCenter or nanotechnology.<br>
			If you are a student, please give the name of your advisor.<br><br>
			i.e. I am a student of a professor at UMD<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I am a collaborator with the ISR Dept<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I research nanotube fabrication at NIST.<br><br></font></div>
		</td>
		<td class="register_form_field" style="background-color:#FF99CC" valign="middle"><textarea name="relationship" cols="40" rows="7"><?php echo $relationship?></textarea></td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field"><strong>Focus Area(s)</strong></td>
		<td class="register_form_field">
			<table>
			<tr>
			<td align="left" valign="top">
 <?php
	$focus_area_sql="SELECT * FROM user_focus_area order by title";
	$focus_area_rs=mysql_query($focus_area_sql);
	$num_focus_areas=mysql_num_rows($focus_area_rs);
	$col_count = round($num_focus_areas/2);
	$i=0;
	while ($focus_area_row=mysql_fetch_assoc($focus_area_rs)){
		$this_focus_area_id = $focus_area_row['user_focus_area_id'];
		$this_focus_area = $focus_area_row['title'];
?>
		<input type="checkbox" name="focus_area<?php echo $this_focus_area_id;?>" value="<?php print $this_focus_area_id;?>" <?php if (is_array($focus_areas)){ print (in_array($this_focus_area_id, $focus_areas)) ? "checked" : ""; }?>><?php print $this_focus_area;?><br>
<?php
		$i++;
		if ($i == $col_count) {
			print "</td>\n";
			print "<td align='left' valign='top'>\n";
			$i=0;
		}
	}
?>
			</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field"><strong>Research Interests</strong></td>
		<td class="register_form_field"><textarea name="research_interests" cols="40" rows="7"><?php echo $research_interests?></textarea></td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field"><strong>Other Affiliations</strong></td>
		<td class="register_form_field"><input type="text" name="affiliations" value="<?php echo $affiliations?>" size="50"></td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field"><strong>Biography</strong></td>
		<td class="register_form_field"><textarea name="biography" cols="40" rows="7"><?php echo $biography?></textarea></td>
	</tr>
	<tr style="background-color: #880000;">
		<td colspan="2"><font color="#ffffff"><strong>Preferences</strong></font></td>
	</tr>
	<tr>
		<td class="register_form_field" style="background-color:#FF99CC"><strong>Request Intranet Access</strong> <?php echo !$edit?'<font color="red" size=+1><b>*</b></font>':''?><br>
			<div align="left">For Information reguarding the Intranet please click <a href="/about_intranet.php" target="_blank">here</a>.<br><br>
			You will receive an email when your request has been approved.</div>
		</td>
		<td class="register_form_field" style="background-color:#FF99CC" valign="middle">
			<input type="radio" name="intranet_access" value="<?php print $intranet_access? $intranet_access : "1"?>" <?php print $intranet_access ? "checked" : "";?>>Yes
			<?php if ($edit && $intranet_access) { print " - Granted"; } ?><br />
			<input type="radio" name="intranet_access" value="0" <?php print !$intranet_access ? "checked" : "";?>>No
		</td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field">
			<strong>Receive email announcements</strong>
		</td>
		<td class="register_form_field">
			<input type="radio" name="receive_announcements" value="1" <?php print $receive_announcements ? "checked" : "";?>>Yes<br />
			<input type="radio" name="receive_announcements" value="0">No<br>
		</td>
	</tr>
	<tr bgcolor="#efefef">
		<td class="register_form_field">
			<strong>Publish email on site</strong><br />
			<div align="left">Checking 'Yes' will show your email on public pages.</div>
		</td>
		<td class="register_form_field">
			<input type="radio" name="publish_email_on_site" value="1" <?php print $publish_email_on_site ? "checked" : "";?>>Yes<br />
			<input type="radio" name="publish_email_on_site" value="0">No
		</td>
	</tr>
<?php
//if ($edit) {
?>
	<tr style="background-color: #880000;">
		<td colspan="2"><font color="#ffffff"><strong>Password</strong></font></td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><strong>Password</strong> <?php echo !$edit?'<font color="red" size=+1><b>*</b></font>':''?><br><font size="1">at least 6 characters</font></td>
		<td class="register_form_field" style="background-color:#FF99CC"><input type="password" name="password" size="50"></td>
	</tr>
	<tr><td class="register_form_field" style="background-color:#FF99CC"><strong>Verify Password</strong> <?php echo !$edit?'<font color="red" size=+1><b>*</b></font>':''?></td>
		<td class="register_form_field" style="background-color:#FF99CC"><input type="password" name="password2" size="50"></td>
	</tr>
<?php
//}
?>
</table>
<br />
<?php if ($edit) { ?>
<input type="submit" name="update" value="<?php echo translate('Edit Profile')?>" class="button" />
<input type="button" name="cancel" value="<?php echo translate('Cancel')?>" class="button" onclick="javascript: document.location='ctrlpnl.php';" />
<?php } else {?>
<input type="submit" name="register" value="<?php echo translate('Register')?>" class="button" />
<input type="button" name="cancel" value="<?php echo translate('Cancel')?>" class="button" onclick="javascript: document.location='index.php';" />
<?php } ?>
<input type="hidden" name="signin" value="<?php echo $signin?>" />
</form>
<?php
}

/**
* Prints out a login form and any error messages
* @param string $msg error messages to display for user
* @param string $resume page to resume on after login
*/
function printLoginForm($msg = '', $resume = '') {
	global $conf;
	$link = CmnFns::getNewLink();
	$use_logonname = (bool)$conf['app']['useLogonName'] || (bool)$conf['ldap']['authentication'];

	// Check browser information
	echo '<script language="JavaScript" type="text/javascript">checkBrowser();</script>';

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);
?>
<form name="login" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<table width="350px" border="0" cellspacing="0" cellpadding="1" align="center">
<tr>
  <td bgcolor="#CCCCCC">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr bgcolor="#EDEDED">
		<td colspan="2" style="border-bottom: solid 1px #CCCCCC;">
		  <h5 align="center"><?php echo translate('Please Log In')?></h5>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b><?php echo translate(($use_logonname ? 'Logon name' : 'Email address'))?></b></p>
		</td>
		<td>
		  <input type="text" name="email" class="textbox" />
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Password')?></b></p>
		</td>
		<td>
		  <input type="password" name="password" class="textbox" />
		</td>
	  </tr>
<!--
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Language')?></b></p>
		</td>
		<td>
		<?php CmnFns::print_language_pulldown()?>
		</td>
	  </tr>
-->
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Keep me logged in')?></b></p>
		</td>
		<td>
		  <input type="checkbox" name="setCookie" value="true" checked />
		</td>
	  </tr>
	  <tr bgcolor="#FAFAFA">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" name="login" value="<?php echo translate('Log In')?>" class="button" />
			<input type="hidden" name="resume" value="<?php echo $resume?>" />
			<input type="hidden" name="current_page" value="">
		  </p>
		  <h4 align="center" style="margin-bottom:1px;"><b><?php echo translate('First time user')?></b>
			<?php $link->doLink('/register.php', translate('Click here to register'), '', '', translate('Register for phpScheduleIt')) ?>
		  </h4>
		</td>
	  </tr>
	</table>
  </td>
</tr>
</table>
<p align="center">
<?php $link->doLink('roschedule.php', 'View Lab Schedule', '', '', translate('View a read-only version of the lab')) ?>
|
<?php $link->doLink('forgot_pwd.php', translate('I Forgot My Password'), '', '', translate('Retreive lost password')) ?>
|
<?php $link->doLink('javascript: help();', translate('Help'), '', '', translate('Get online help')) ?>
</p>
</form>
<?php
}

/**
* Prints out a sign in form and any error messages
* @param string $msg error messages to display for user
* @param array $users to show dropdown list of users for quick sign in
*/
function printSigninForm($msg = '', $users='', $signed_users='', $lab_id){
	global $conf;
	$link = CmnFns::getNewLink();

	// Check browser information
	echo '<script language="JavaScript" type="text/javascript">checkBrowser();</script>';

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);
?>
<script>
	function reload_window(){
		//window.location="<?php echo $_SERVER['PHP_SELF']?>?lab_id=<?php echo$lab_id ?>";
	}

	self.setTimeout('reload_window()', 30000);
</script>
<table width="650" border="0" cellspacing="0" cellpadding="1" align="center">
<tr>
  <td bgcolor="#CCCCCC" width="325">
    <form name="signin" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr bgcolor="#EDEDED">
		<td colspan="2" style="border-bottom: solid 1px #CCCCCC;">
		  <h2 align="center">Sign In</h2>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p style="text-align:right"><b>User</b></p>
		</td>
		<td>
		  <select name="user_id">
<?php for ($i=0;$i<count($users);$i++){ ?>
		  	<option value="<?php echo $users[$i]['user_id'];?>"><?php echo $users[$i]['last_name'].", ".$users[$i]['first_name'];?></option>
<?php } ?>
		  </select><br />
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p style="text-align:right"><b><?php echo translate('Password')?></b></p>
		</td>
		<td>
		  <input type="password" name="password" class="textbox" />
		</td>
	  </tr>
<!--
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Language')?></b></p>
		</td>
		<td>
		<?CmnFns::print_language_pulldown()?>
		</td>
	  </tr>
-->
	  <tr bgcolor="#FAFAFA">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" value="Sign In" class="button" />
			<input type="hidden" name="signin" value="Sign In" />
			<input type="hidden" name="login" value="" />
			<input type="hidden" name="lab_id" value="<?php echo$lab_id ?>" />
		  </p>
		</td>
	  </tr>
	</table>
	</form>
  </td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>


  <!-- sign out section -->
  <td bgcolor="#CCCCCC" width="50%">
    <form name="signout" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr bgcolor="#EDEDED">
		<td colspan="2" style="border-bottom: solid 1px #CCCCCC;">
		  <h2 align="center">Sign Out</h2>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p style="text-align:right"><b>User</b></p>
		</td>
		<td>
		  <select name="signid">
<?php
	if ($signed_users!=0){
		for ($i=0;$i<count($signed_users);$i++){ ?>
		  	<option value="<?php echo $signed_users[$i]['signid'];?>"><?php echo $signed_users[$i]['last_name'].", ".$signed_users[$i]['first_name'];?></option>
<?php
 		}
	}else{
?>
			<option value="0">No Users Signed In</option>
<?php
	}
?>
		  </select><br />
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p style="text-align:right"><b><?php echo translate('Password')?></b></p>
		</td>
		<td>
		  <input type="password" name="password" class="textbox" />
		</td>
	  </tr>
<!--
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Language')?></b></p>
		</td>
		<td>
		<?CmnFns::print_language_pulldown()?>
		</td>
	  </tr>
-->
	  <tr bgcolor="#FAFAFA">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" value="Sign Out" class="button" />
			<input type="hidden" name="signout" value="Sign Out" />
			<input type="hidden" name="login" value="" />
			<input type="hidden" name="lab_id" value="<?php echo$lab_id ?>" />
		  </p>
		</td>
	  </tr>
	</table>
	</form>
  </td>
</tr>
</table>

<h4 align="center" style="margin-bottom:1px;"><b>
<?php $link->doLink('register.php?current_page='.$_SERVER['SCRIPT_NAME'], translate('Click here to register'), '', '', translate('Register for phpScheduleIt')) ?></b>
</h4>
<br />
<p align="center">
<?php $link->doLink('roschedule.php', translate('View Lab'), '', '', translate('View a read-only version of the lab')) ?>
|
<?php $link->doLink('forgot_pwd.php', translate('I Forgot My Password'), '', '', translate('Retreive lost password')) ?>
|
<?php $link->doLink('javascript: help();', translate('Help'), '', '', translate('Get online help')) ?>
</p>
<?
}

/**
* Prints out a sign in form and any error messages
* @param string $msg error messages to display for user
* @param array $users to show dropdown list of users for quick sign in
*/
function printResourceSigninForm($msg = '', $resources='', $resources_users='', $lab_id, $user_id){
	global $conf;
	$link = CmnFns::getNewLink();
	// Check browser information
	echo '<script language="JavaScript" type="text/javascript">checkBrowser();</script>';

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);
?>
<script>
	function reload_window(){
		window.location="<?php echo $_SERVER['PHP_SELF']?>?lab_id=<?php echo $lab_id ?>";
	}

	self.setTimeout('reload_window()', 30000);
</script>
<?php
  $count = 0;
  while($user_row = mysql_fetch_assoc($resources_users)){
  	$users[$count]['first_name'] = $user_row['first_name'];
  	$users[$count]['last_name'] = $user_row['last_name'];
  	$users[$count]['machid'] = $user_row['machid'];
	$users[$count]['user_id'] = $user_row['user_id'];
	$users[$count]['useid'] = $user_row['useid'];
	$count++;
  }

?>

<br>
<center>
<?php echo $link->getLink("javascript: reservation_sign_in()", 'Reservation Sign In', '', '', 'Reservation Sign In'); ?>
</center>
<br>
<table width="350px" border="0" cellspacing="0" cellpadding="1" align="center">
<tr>
  <td bgcolor="#CCCCCC">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr bgcolor="#EDEDED">
		<td colspan="4" style="border-bottom: solid 1px #CCCCCC;">
		  <h5 align="center">Sign In/Out</h5>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b>Resource</b></p>
		</td>
		<td>
		  <p><b>User</b></p>
		</td>
		<td nowrap>
		  <p><b>Sign In/Out</b></p>
		</td>
		<td nowrap><p><b>Usage History</b></p></td>
	  </tr>
<?php
  	//var_dump($users);

	while($row = mysql_fetch_assoc($resources)){
		$signedin = false;
		$signout_id = '';
		$count = 0;
?>
	  <tr bgcolor="#FFFFFF">
		  <td nowrap="nowrap"><?php echo $row['name']?></td>
		  <td>
		  <?php
		  while($count < count($users)){
		  	if($row['machid'] == $users[$count]['machid']){
				$signedin = true;
				$user_id = $users[$count]['user_id'];
				echo $users[$count]['first_name'] . ' ' . $users[$count]['last_name'];
				$signout_id = $users[$count]['user_id'];
				$useid = $users[$count]['useid'];
			}
			$count++;
		  }
		  ?>
		  </td>
		  <td>
		  <?php
		  	if($signedin){
		  	  echo $link->getLink("javascript: equipment_login('" . $row['machid']. "&signout=1&user_id=" . $signout_id . "&useid=" . $useid . "');", 'Sign Out', '', '', translate('Resource Sign Out'));
			  $signedin = false;
			}else{
		  	  echo $link->getLink("javascript: equipment_login('" . $row['machid']. "');", 'Sign In', '', '', translate('Resource Sign In'));
			}
		  ?>
		  	<!-- <a href="equipment_signin.php?machid=<?php echo $row['machid']?>" target="_blank">Sign In</a>-->
		  </td>
		  <td>
		  	<!--<?php echo $link->getLink("javascript: usage_history('" . $row['machid']. "');", 'View', '', '', 'View'); ?>-->
		  </td>
	  </tr>
<?php
	}
?>
	</table>
  </td>
</tr>
</table>
<h4 align="center" style="margin-bottom:1px;"><b>
<?php $link->doLink('register.php?signin=1', translate('Click here to register'), '', '', translate('Register for phpScheduleIt')) ?>
</h4>
<br />
<p align="center">
<?php $link->doLink('roschedule.php', 'View Lab Schedule', '', '', translate('View a read-only version of the lab')) ?>
|
<?php $link->doLink('forgot_pwd.php', translate('I Forgot My Password'), '', '', translate('Retreive lost password')) ?>
|
<?php $link->doLink('javascript: help();', translate('Help'), '', '', translate('Get online help')) ?>
</p>
</form>
<?php
}

/**
* Prints out a sign in form and any error messages
* @param string $msg error messages to display for user
* @param array $users to show dropdown list of users for quick sign in
*/
function printResourceLoginForm($msg = '', $users='', $equipment_id='', $user_id='', $useid='', $signaction=''){
	global $conf;
	$link = CmnFns::getNewLink();
	//echo $equipment_id;
	// Check browser information
	echo '<script language="JavaScript" type="text/javascript">checkBrowser();</script>';

	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);
?>
<table width="350px" border="0" cellspacing="0" cellpadding="1" align="center">
<tr>
<form name="signin" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
  <td bgcolor="#CCCCCC">
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
	  <tr bgcolor="#EDEDED">
		<td colspan="2" style="border-bottom: solid 1px #CCCCCC;">
		  <h2 align="center"><?php echo($signaction=='signin') ? "Sign In" : "Sign Out";?></h2>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b>User</b></p>
		</td>
		<td>
		  <select name="user_id">
<?php for ($i=0;$i<count($users);$i++){ ?>
		  	<option value="<?php echo $users[$i]['user_id'];?>" <?php echo($users[$i]['user_id']==$user_id) ? "selected" : ""; ?>><?php echo $users[$i]['last_name'].", ".$users[$i]['first_name'];?></option>
<?php } ?>
		  </select><br />
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Password')?></b></p>
		</td>
		<td>
		  <input type="password" name="password" class="textbox" />
		</td>
	  </tr>
<?php if($signaction == 'signout'){?>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b>Description</b></p>
		</td>
		<td>
		  <textarea cols="35" rows="3" name="description" class="textbox" /></textarea>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b>Notes</b></p>
		</td>
		<td>
		  <textarea cols="35" rows="3" name="notes" class="textbox" /></textarea>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b>Problems</b></p>
		</td>
		<td>
		  <textarea cols="35" rows="3" name="problems" class="textbox" /></textarea>
		</td>
	  </tr>
<?php }else{ ?>
	  <tr bgcolor="#FFFFFF">
		<td width="150">
		  <p><b>FRS</b></p>
		</td>
		<td>
		  <input type="text" name="frs" class="textbox" />
		</td>
	  </tr>
<?php } ?>
	  <tr bgcolor="#FAFAFA">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" name="<?php echo($signaction=='signin') ? "signin" : "signout";?>" value="<?php echo($signaction=='signin') ? "Sign In" : "Sign Out";?>" class="button" />
			<input type="hidden" name="useid" value="<?php echo$useid ?>" />
			<input type="hidden" name="login" value="" />
			<input type="hidden" name="equipment_id" value="<?php echo$equipment_id ?>" />
		  </p>
		</td>
	  </tr>
	</table>
  </td>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</form>
</tr>
</table>
<?php
}
?>