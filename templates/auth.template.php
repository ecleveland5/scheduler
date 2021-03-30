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
* Prints out a login form and any error messages
* @param string $msg error messages to display for user
* @param string $resume page to resume on after login
*/
function printLoginForm($msg = '') {
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
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p><b><?php echo translate('Keep me logged in')?></b></p>
		</td>
		<td>
		  <input type="checkbox" name="setCookie" value="true" checked />
		</td>
	  </tr>
-->
	  <tr bgcolor="#FAFAFA">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" name="login" value="<?php echo translate('Log In')?>" class="button">
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
			<input type="hidden" name="login" value="login" />
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