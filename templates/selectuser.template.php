<?php
/**
* Provide the output functions for the SelectUser class
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 02-05-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

$link = CmnFns::getNewLink();	// Get Link object

/**
* Prints out the user management table
* @param Object $pager pager object
* @param mixed $users array of user data
* @param string $err last database error
*/
function print_user_list(&$pager, $users, $err, $javascript) {
	global $link;
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
  <tr>
    <td class="tableBorder">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td colspan="7" class="tableTitle">&#8250; <?php echo translate('All Users')?> </td>
        </tr>
        <tr class="rowHeaders">
          <td width="40%"><?php echo translate('Name')?></td>
          <td width="60%"><?php echo translate('Email')?></td>
        </tr>
        <?php
	
	if (!$users)
		echo '<tr class="cellColor0"><td colspan="2" style="text-align: center;">' . $err . '</td></tr>' . "\n";
	
	for ($i = 0; is_array($users) && $i < count($users); $i++) {
		$cur = $users[$i];
		$first_name = $cur['first_name'];
		$last_name = $cur['last_name'];
		$email = $cur['email'];
		
		$first_name_last_name = array($first_name, $last_name);
        
		echo "<tr class=\"cellColor" . ($i%2) . "\" align=\"center\" onmouseover=\"this.className='SelectUserRowOver';\" onmouseout=\"this.className='cellColor" . ($i%2) . "';\" onclick=\"" . sprintf("$javascript('%s','%s','%s','%s');", $cur['user_id'], $first_name, str_replace("'", "\'",$last_name), $email) . ";\">\n"
               . "<td style=\"text-align:left;\">$first_name $last_name</td>\n"
               . "<td style=\"text-align:left;\">$email</td>\n"
               . "</tr>\n";
    }
    // Close users table
    ?>
      </table>
    </td>
  </tr>
</table>
<br />
<form name="name_search" action="<?php echo $_SERVER['PHP_SELF']?>" method="get">
	<p align="center">
	<?php print_last_name_links(); ?>
	</p>
	<br />
	<p align="center">
	<?php echo translate('First Name')?> <input type="text" name="firstName" class="textbox" />
	<?php echo translate('Last Name')?> <input type="text" name="lastName" class="textbox" />	
	<input type="hidden" name="searchUsers" value="true" />
	<input type="hidden" name="<?php echo $pager->getLimitVar()?>" value="<?php echo $pager->getLimit()?>" />
	<?php if (isset($_GET['order'])) { ?>
		<input type="hidden" name="order" value="<?php echo filter_input(INPUT_GET, 'order', FILTER_SANITIZE_SPECIAL_CHARS); ?>" />
	<?php } ?>
	<?php if (isset($_GET['vert'])) { ?>
		<input type="hidden" name="vert" value="<?php echo filter_input(INPUT_GET, 'vert', FILTER_SANITIZE_SPECIAL_CHARS); ?>" />
	<?php } ?>
	<input type="submit" name="searchUsersBtn" value="<?php echo translate('Search Users'); ?>" class="button" />
	</p>
</form>
<?php
}

/**
* Prints out the links to select last names
* @param none
*/
function print_last_name_links() {
	global $letters;
	echo '<a href="javascript: search_user_last_name(\'\');">' . translate('All Users') . '</a>';
	foreach($letters as $letter) {
		echo '<a href="javascript: search_user_last_name(\''. $letter . '\');" style="padding-left: 10px; font-size: 12px;">' . $letter . '</a>';
	}
}
?>