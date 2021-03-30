<?php
/**
* This file provides output functions for userinfo.php
* No data manipulation is done in this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-13-04
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

/**
* Prints out information about a user
* @param object $user current user
*/
function printUI(User $user) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" align="center">
	<tr>
	  <td class="tableBorder">
		<table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr> 
            <td class="rowHeaders" width="25%"><?php echo translate('Name')?></td>
            <td class="cellColor"><?php echo  $user->getFullName() ?></td>
          </tr>
          <tr> 
            <td class="rowHeaders"><?php echo translate('Member ID')?></td>
            <td class="cellColor"><?php echo  $user->getId() ?></td>
          </tr>
          <tr>
            <td class="rowHeaders"><?php echo translate('Email')?></td>
            <td class="cellColor"><?php echo  '<a href="mailto:' . $user->getEmail() . '">' . $user->getEmail() . '</a>'?></td>
          </tr>
          <tr>
            <td class="rowHeaders"><?php echo translate('Phone')?></td>
            <td class="cellColor"><?php echo  $user->getWorkPhone() ?></td>
          </tr>
          <tr>
            <td class="rowHeaders"><?php echo translate('Institution')?></td>
            <td class="cellColor"><?php echo  $user->getInstitution() ?></td>
          </tr>
                    <tr>
            <td class="rowHeaders"><?php echo 'User Type';?></td>
            <td class="cellColor"><?php echo  $user->getUserType(); ?></td>
          </tr>
                    <tr>
          	<td class="rowHeaders"><?php echo "Advisor";?></td>
          	<td class="cellColor">
          		<?php 
          		$advisor = $user->getAdvisor();
          		if ($advisor !== false && is_array($advisor)) {
          			echo (!empty($advisor['first_name'])) ? $advisor['first_name'] : '';
          			echo (!empty($advisor['last_name'])) ? ' ' . $advisor['last_name'] : '';
          			echo (!empty($advisor['email'])) ? ', <a href="mailto:' . $advisor['email'] . '">' . $advisor['email'] . '</a>' : '';
          		}
          		?>
          <tr>
            <td class="rowHeaders"><?php echo translate('Position')?></td>
            <td class="cellColor"><?php echo  $user->getPosition() ?></td>
          </tr>
          <tr>
            <td class="rowHeaders" valign="top"><?php echo translate('Permissions')?></td>
            <td class="cellColor">
				<?php
				$training = $user->getResourcePermissions();
				foreach ($training as $machid => $name)
					echo $name . '<br />';
				?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
<?php
}


/**
* Print previous/next user and close window links
* @param string $prev previous user_id
* @param string $next next user_id
*/
function printLinks($prev, $next) {
	global $link;
    
    echo "<p align=\"center\">\n"
      . $link->getLink("javascript: viewUser('" . $prev . "');", translate('Previous User')) . "\n"
      . "&nbsp;&nbsp;&nbsp;"
      . $link->getLink("javascript: viewUser('" . $next . "');", translate('Next User')) . "\n"
      . "</p>\n"
      . "<p>&nbsp;</p>\n"
      . "<p align=\"center\"><input type=\"button\" name=\"close\" value=\"" . translate('Close Window') . "\" class=\"button\" onclick=\"window.close();\" />"
      . "</p>\n";
}
?>