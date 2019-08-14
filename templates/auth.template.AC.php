<?php
/**
* Prints out a sign in form with autocomplete and any error messages
* @param string $msg error messages to display for user
* @param array $users to show dropdown list of users for quick sign in
*/
function printSigninFormAC($msg = '', $users='', $signed_users='', $lab_id){
	global $conf;
	$link = CmnFns::getNewLink();

	// Check browser information
	echo '<script language="JavaScript" type="text/javascript">checkBrowser();</script>';
	echo '<div style="padding:0 10px 10px 10px;margin: 10px auto;width:630px;border:solid 1px #ccc;">
		<h2>Attention Users!</h2>
		<ol>
				<li style="font-size:20px;"><span style="font-size:15px;font-weight:bold;">Start typing your name.</span></li>
			  <li style="font-size:20px;"><span style="font-size:15px;font-weight:bold;">Then click on your name.</span></li>
	  </ol>
		<br />
		If the system doesn not find your name then you might not be registered.<br />
		<br />
		If you are registered and cannot find your name, please send a note to
		Ernie Cleveland (<a href="mailto:eclevela@umd.edu">eclevela@umd.edu</a>) when you can.</p>
		</div>';
	if (!empty($msg))
		CmnFns::do_error_box($msg, '', false);
?>
<script>
	function reload_window(){
		//window.location="<?php echo $_SERVER['PHP_SELF']?>?lab_id=<?php echo  $lab_id ?>";
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
		  <input type="text" name="user_name" id="user_name">
		  <input type="hidden" name="user_id" id="user_id" value="">
		  <script>
		  var users = new Array();
		  <?php
			foreach($users as $user) {
				$user['first_name'] = str_replace("\t", '', $user['first_name']); // remove tabs
				$user['first_name'] = str_replace("\n", '', $user['first_name']); // remove new lines
				$user['first_name'] = str_replace("\r", '', $user['first_name']); // remove carriage returns
				$user['last_name'] = str_replace("\t", '', $user['last_name']); // remove tabs
				$user['last_name'] = str_replace("\n", '', $user['last_name']); // remove new lines
				$user['last_name'] = str_replace("\r", '', $user['last_name']); // remove carriage returns
				echo "users[".addslashes($user['user_id'])."] = \"".addslashes($user['first_name'])." ".addslashes($user['last_name'])." ".addslashes($user['email'])."\";\r\n ";
			}
		  ?>
		  function findUser(username) {
				$("#user_id").val(jQuery.inArray(username, users));
		  }

		  $(document).ready(function() {
			$("input#user_name").autocomplete({
				source: [<?php
					$start=false;
					foreach($users as $user) {
						if($start) echo ",";
						$start=true;
						$user['first_name'] = str_replace("\t", '', $user['first_name']); // remove tabs
						$user['first_name'] = str_replace("\n", '', $user['first_name']); // remove new lines
						$user['first_name'] = str_replace("\r", '', $user['first_name']); // remove carriage returns
						$user['last_name'] = str_replace("\t", '', $user['last_name']); // remove tabs
						$user['last_name'] = str_replace("\n", '', $user['last_name']); // remove new lines
						$user['last_name'] = str_replace("\r", '', $user['last_name']); // remove carriage returns
						echo '"'.addslashes($user['first_name'])." ".addslashes($user['last_name'])." ".addslashes($user['email']).'"';
					}
				?>]
			});
		  });
		  $("input#user_name").bind("autocompleteselect", function(event, ui, users) {
			findUser(ui.item.value);
		  });
		  </script>
		</td>
	  </tr>
	  <tr bgcolor="#FFFFFF">
		<td>
		  <p style="text-align:right"><b><?php echo translate('Password')?></b></p>
		</td>
		<td align="center">
		  <input type="password" name="password" class="textbox" style="height:16px;width:151px;" />
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
	  <tr bgcolor="#FAFAFA">
		<td colspan="2">
		   <p align="center">
			<input type="submit" value="Sign In" class="button" />
			<input type="hidden" name="signin" value="Sign In" />
			<input type="hidden" name="login" value="" />
			<input type="hidden" name="lab_id" value="<?php echo  $lab_id ?>" />
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
		<td align="center">
		  <input type="password" name="password" class="textbox" style="height:16px;width:151px;" />
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
	  <tr bgcolor="#FAFAFA" style="border:none;">
		<td colspan="2" style="border-top: solid 1px #CCCCCC;">
		   <p align="center">
			<input type="submit" value="Sign Out" class="button" />
			<input type="hidden" name="signout" value="Sign Out" />
			<input type="hidden" name="login" value="" />
			<input type="hidden" name="lab_id" value="<?php echo  $lab_id ?>" />
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
<?php
}
