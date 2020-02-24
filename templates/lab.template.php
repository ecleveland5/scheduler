<?php
/**
* This file provides the output functions for
*  an interface for reserving resources,
*  viewing other reservations and modifying their own.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-18-05
* @package Templates
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/

// Get Link object
$link = CmnFns::getNewLink();

/**
* Print out week being viewed above lab tables
* @param array $d array of date information about this lab
* @param string $title title of lab
*/
function print_date_span($d, $title) {
    // Print out current week being viewed
    echo '<h3 align="center">' . $title . '<br/>' . CmnFns::formatDate($d['firstDayTs']) . ' - ' . CmnFns::formatDate($d['lastDayTs']) . '</h3>';
}

/**
* Prints out a jump menu for the labs
* @param array $links array of lab links
*/
function print_lab_list($links, $currentid) {
?>
<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 5px;">
<tr><td style="text-align: center; width: 100%;">
<p style="font-weight: bold; text-align: center;"><?php echo translate('View lab')?>
<select name="choose_lab" class="textbox" onchange="javascript: changeLab(this);">
<?php
if (isset($_GET['date'])) {
	$dateStr = '&date='.filter_input(INPUT_GET, 'date',FILTER_SANITIZE_STRING);
} else {
	$dateStr = '';
}
for ($i = 0; $i < count($links); $i++){
    echo '<option value="' . $links[$i]['lab_id'] . $dateStr . '"' . ($links[$i]['lab_id'] == $currentid ? ' selected="selected"' : '') . '>';
	if ($links[$i]['nickname'] != "") echo $links[$i]['nickname'] . " - ";
	echo $links[$i]['labTitle'] ."</option>\n";
}?>
</select>
</p>
</td></tr>
</table>
<?php
}

/**
* Print out a key to identify what the colors mean
* @param none
*/
function print_color_key() {
    global $conf;
?>
<table align="center" cellpadding="5" cellspacing="10">
  <tr style="font-size: 10px; font-weight: bold; text-align: center; vertical-align: center;">
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['my_res'][0]['color']?>; color:#<?php echo $conf['ui']['my_res'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('My Reservations')?></td>
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['my_past_res'][0]['color']?>; color:#<?php echo $conf['ui']['my_past_res'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('My Past Reservations')?></td>
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['other_res'][0]['color']?>; color:#<?php echo $conf['ui']['other_res'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('Other Reservations')?></td>
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['other_past_res'][0]['color']?>; color:#<?php echo $conf['ui']['other_past_res'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('Other Past Reservations')?></td>
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['pending'][0]['color']?>; color:#<?php echo $conf['ui']['pending'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('Pending Approval')?></td>
    <td style="width: 75px; height: 38px; background-color:#<?php echo $conf['ui']['blackout'][0]['color']?>; color:#<?php echo $conf['ui']['blackout'][0]['text']?>; border: 2px #000000 solid;"><?php echo translate('Blacked Out Time')?></td>  </tr>
</table>
<?php
}


/**
* Start table for one day on lab
* This function starts the table for each day
* on the lab, printing out it's date
* and the time value cells
* @param string $displayDate date string to print
*/
function start_day_table($displayDate, $hour_header) {

?><div id="<?php echo $displayDate ?>">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
     <tr class="tableBorder">
      <td>
       <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr class="labTimes">
         <td rowspan="2" width="140px" class="labDate"><?php echo $displayDate ?></td>
<?php
    echo $hour_header ."</tr>\n";
}

/**
* Prints out the navigational calendars
* @param Calendar $prev previous month calendar
* @param Calendar $curr current month calendar
* @param Calendar $next next month calendar
*/
function print_calendars(&$prev, &$curr, &$next) {
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><?php $prev->printCalendar()?></td>
    <td align="center" valign="top"><?php $curr->printCalendar()?></td>
    <td align="center" valign="top"><?php $next->printCalendar()?></td>
  </tr>
</table>
<?php
}

/**
* Formats and returns the time header of the table (it is the same for every one)
* @param array $th array of time values and their rowspans
* @param int $startDay starting time of day
* @param int $endDay ending time of day
* @param int $timeSpan time intervals
* @global $conf
*/
function get_hour_header($th, $startDay, $endDay, $timeSpan) {
    global $conf;
    $header = '';

    // Write out the available times
    foreach ($th as $time => $cols) {
        $header .= "<td colspan=\"$cols\" style=\"width:60px\">$time</td>";
    }

    // Close row, start next
    $header .= "</tr>\n<tr class=\"labTimes\">\n";

    // Compute total # of cols
    $totCol = intval(($endDay - $startDay) / $timeSpan);
    // Create the fraction hour minute marks
    for ($x = 0; $x < $totCol; $x++)
        $header .= '<td>&nbsp;</td>';

    return $header;

}


/**
* Close off table for each day
* This function simply prints out the HTML to close off
* the rows and tables for each day
* @param none
*/
function end_day_table() {
?>
    </table>
   </td>
  </tr>
</table>
</div>
<p>&nbsp;</p>

<?php
}


/**
* Prints out the cell containing all the resource information
* @param int $ts timestamp for the current day
* @param string $id id of this resource
* @param string $name name of this resource
* @param boolean $show whether this resource can be reserved
* @param boolean $is_blackout whether this is a blackout lab or not
* @param string $lab_id id of the current lab
* @param boolean $pending is reservation pending approval
* @param string $color background color of row
*/
function print_name_cell($ts, $id, $name, $show, $is_blackout, $lab_id, $pending = false, $color = '', $hide = false) {
    global $link;

    $color = (empty($color)) ? 'cellColor': $color;

    // Start a new row and print out resource name
    echo "<tr class=\"$color $id\"";
    if ($hide) {
        echo ' style="display:none;"';
    }
    echo ">\n"
           . '<td class="resourceName">';

    if ($is_blackout) {
        $link->doLink("javascript: reserve('" . RES_TYPE_ADD . "', '$id','$ts', '', '$lab_id', '1', '0', '$pending');", $name, '', '', translate("Set blackout times", array($name, CmnFns::formatDate($ts))));
    }
    else {
        // If the user is allowed to make reservations on this resource
        // then provide a link
        // Else do not
        if ($show)
            $link->doLink("javascript: reserve('" . RES_TYPE_ADD . "','$id','$ts','', '$lab_id', '0', '$pending');", $name, '', '', translate('Reserve on', array($name, CmnFns::formatDate($ts))));
        else
            echo '<span class="inact">' . $name . '</span>';
    }
    // Close cell
    echo "</td>";
}

/**
* Prints out blank columns
* @param int $cols number of columns to print out
* @param int $start starting time of the first column printed out
* @param int $span time span of the lab
* @param int $ts timestamp for the reservation start date
* @param string $machid id of the resource on this table row
* @param string $lab_id id of the current lab
* @param int $labType type of the current lab
* @param bool $clickable if this row can be clicked
* @param string $color class of column background
*/
function print_blank_cols($cols, $start, $span, $ts, $machid, $lab_id, $labType, $clickable, $class = '') {
    $is_blackout = intval($labType == BLACKOUT_ONLY);
    //$color = empty($color) ? '' : "#" . $color;
	  //$color_str = empty($color) ? '': "style=\"background-color: $color;\"";

    $js = '';
    $tstart = 0;
    for ($i = 0; $i <= $cols; $i++) {
        if ($labType != READ_ONLY && ($clickable || $is_blackout)) {
            $tstart = $start + ($i * $span);
            $tend = $tstart + $span;
            $js = "onmouseover=\"blankOver(this);\" onmouseout=\"blankOut(this, '$class');\" onclick=\"reserve('".RES_TYPE_ADD."','$machid','$ts','','$lab_id',$is_blackout,'','',$tstart,$tend);\"";
        }
        echo "<td $js style=\"min-width:10px;\" title=".CmnFns::formatTime($tstart).">&nbsp;</td>";

        /*
        if ($span == 30) {
        	echo "<td $js style=\"width:24px;\">&nbsp;</td>";
        } else if ($span == 15) {
        	echo "<td $js style=\"width:12px;\">&nbsp;</td>";
        }
        */
        //echo "<td $js>&nbsp;</td>";
    }
}

/**
* Prints the closing tr tag
* @param none
*/
function print_closing_tr() {
    echo "</tr>\n";
}

/**
* Writes out the reservation cell
* @param int $colspan column span of this reservation
* @param string $color_select array identifier for which color to use
* @param string $mod_view indentifying character for javascript reserve function to mod or view reservation
* @param string $resid id of this reservation
* @param string $summary summary for this reservation
* @param string $viewable whether the user can click on this reservation and bring up a details box
* @param int $show_summary whether to show the summary or not
* @param int $read_only whether this is a read only lab
* @param boolean $pending is this reservation pending approval
*/
function write_reservation($colspan, $color_select, $mod_view, $resid, $summary = '', $viewable = false, $show_summary = 0, $read_only = false, $pending = 0) {
    global $conf;
    $js = '';
    $color = '#' . $conf['ui'][$color_select][0]['color'];
    $hover = '#' . $conf['ui'][$color_select][0]['hover'];
    $text  = '#' . $conf['ui'][$color_select][0]['text'];
    $chars = 4 * $colspan;
    $read_only = intval($read_only);

    if ($viewable) {
        $js = "onclick=\"reserve('$mod_view','','','$resid','','0','$read_only','$pending');\" ";
        if ($show_summary && $summary != '')
            $js .= "onmouseover=\"resOver(this, '$hover'); showSummary('summary', event, '" . preg_replace("/[\n\r]+/", '<br/>', addslashes($summary)) . "');\" onmouseout=\"resOut(this, '$color'); hideSummary('summary');\" onmousemove=\"moveSummary('summary', event);\"";
        else
            $js .="onmouseover=\"resOver(this, '$hover');\" onmouseout=\"resOut(this, '$color');\"";
    }
    else {
        if ($show_summary && $summary != '')
            $js = "onmouseover=\"showSummary('summary', event, '" . preg_replace("/[\n\r]+/", '<br/>', addslashes($summary)) . "');\" onmouseout=\"hideSummary('summary');\" onmousemove=\"moveSummary('summary', event);\"";
    }

    if ($show_summary) {
    	$summary_text = ($summary != '' && $colspan > 1) ? substr($summary, 0, $chars) . ((strlen($summary) > $chars) ? '...' : '') : '&nbsp;';
    	//$summary_text = $summary;
    }
    else
        $summary_text = '&nbsp;';

    // Write reserved time cell
    echo "<td colspan=\"$colspan\" style=\"overflow:hidden;color: $text; background-color: $color;\" $js>"
    	. "<div style=\"overflow:hidden;width:".($colspan*13)."px \">$summary_text</div></td>";
}
	
	/**
	 * Writes out the blackout cell
	 * @param int $colspan column span of the blackout
	 * @param $viewable
	 * @param string $blackoutid id of this blackout
	 * @param string $summary blackout summary text
	 * @param int $show_summary whether to show the summary or not
	 * @param string $lab_id
	 */
function write_blackout($colspan, $viewable, $blackoutid, $summary = '', $show_summary = 0, $lab_id = null) {
    global $conf;
    $color = '#' . $conf['ui']['blackout'][0]['color'];
    $hover = '#' . $conf['ui']['blackout'][0]['hover'];
    $text  = '#' . $conf['ui']['blackout'][0]['text'];
    $chars = 4 * $colspan;
    $js = '';

    if ($viewable) {
        $js = "onclick=\"reserve('".RES_TYPE_MODIFY."','','','$blackoutid','$lab_id','1');\" ";
        if ($show_summary && $summary != '')
            $js .= "onmouseover=\"resOver(this, '$hover'); showSummary('summary', event, '" . preg_replace("/[\n\r]+/", '<br/>', addslashes($summary)) . "');\" onmouseout=\"resOut(this, '$color'); hideSummary('summary');\" onmousemove=\"moveSummary('summary', event);\"";
        else
            $js .="onmouseover=\"resOver(this, '$hover');\" onmouseout=\"resOut(this, '$color');\"";
    }
    else {
        if ($show_summary != 0 && $summary != '')
            $js = "onmouseover=\"showSummary('summary', event, '" . preg_replace("/[\n\r]+/", '<br/>', addslashes($summary)) . "');\" onmouseout=\"hideSummary('summary');\" onmousemove=\"moveSummary('summary', event);\"";
    }

    if ($show_summary) {
        $summary_text = ($summary != '' && $colspan > 1) ? substr($summary, 0, $chars) . ((strlen($summary) > $chars) ? '...' : '') : '&nbsp;';
    }
    else
        $summary_text = '&nbsp;';

    echo "<td colspan=\"$colspan\" style=\"color: $text; background-color: $color;\" $js>$summary_text</td>\n";
}

/**
* Writes out a div to be used for reservation summary mouseovers
* @param none
*/
function print_summary_div() {
?>
<div id="summary" class="summary_div" style="width: 150px;"></div>
<?php
}
	
	/**
	 * Print links to jump to new dates
	 * This function prints out the HTML links to allow
	 *  users to navigate back/forward one week.
	 * It also prints the form for users to jump to
	 *  any given week.
	 * @param int $_date timestamp of first day of week on lab
	 * @param $viewDays
	 * @param bool $printAllCols whether or not to print the 5 column jump
	 * @param $lab_id
	 */
function print_jump_links($_date, $viewDays, $printAllCols, $lab_id) {
    global $link;
    global $dates;
    
    $date = getdate($_date);
    $m = $date['mon'];
    $d = $date['mday'];
    $y = $date['year'];
    $boxes = $dates['jumpbox'];

    // Write out the previous, today and next links and the form to jump to a date
?>

    <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
     <tr>
      <td align="center"><h5><?php $link->doLink($_SERVER['PHP_SELF'] . '?date=' . date('m-d-Y',mktime(0,0,0,$m, $d - 7, $y)) . "&amp;lab_id=$lab_id", translate('Prev Week'), '', '', translate('Jump 1 week back')) ?></h5></td>
      <?php if ($printAllCols) { ?>
      <td align="center"><h5><?php $link->doLink($_SERVER['PHP_SELF'] . '?date=' . date('m-d-Y',mktime(0,0,0,$m, $d - $viewDays, $y)) . "&amp;lab_id=$lab_id", translate('Prev days', array($viewDays)), '', '', translate('Previous days', array($viewDays))) ?></h5></td>
      <?php } ?>
      <td align="center"><h5><?php $link->doLink($_SERVER['PHP_SELF'] . "?lab_id=$lab_id", translate('This Week'), '', '', translate('Jump to this week')) ?></h5></td>
      <?php if ($printAllCols) { ?>
      <td align="center"><h5><?php $link->doLink($_SERVER['PHP_SELF'] . '?date=' . date('m-d-Y',mktime(0,0,0,$m, $d + $viewDays, $y)) . "&amp;lab_id=$lab_id", translate('Next days', array($viewDays))) ?></h5></td>
      <?php } ?>
      <td align="center"><h5><?php $link->doLink($_SERVER['PHP_SELF'] . '?date=' . date('m-d-Y',mktime(0,0,0,$m, $d + 7, $y)) . "&amp;lab_id=$lab_id", translate('Next Week'), '', '', 'Jump 1 week ahead') ?></h5></td>
     </tr>
     <tr>
      <td align="center" colspan="<?php echo (($printAllCols) ? '5' : '3') ?>">
      <div name="jumpWeek" id="jumpWeek">
         <?php
         $boxes = str_replace('%m', '<input type="text" name="jumpMonth" id="jumpMonth" value="' . translate('mm') . '" class="textbox" size="3" maxlength="2" onclick="this.value = \'\';" />', $boxes);
         $boxes = str_replace('%d', '<input type="text" name="jumpDay" id="jumpDay" value="' . translate('dd') . '" class="textbox" size="3" maxlength="2" onclick="this.value = \'\';" />', $boxes);
         $boxes = str_replace('%Y', '<input type="text" name="jumpYear" id="jumpYear" value="' . translate('yyyy') . '" class="textbox" size="5" maxlength="4" onclick="this.value = \'\';" />', $boxes);
         echo $boxes;
         ?>
         <input name="jumpForm" type="button" value="<?php echo translate('Jump To Date')?>" class="button" onclick="checkDate();"/>
       </div>
      </td>
     </tr>
    </table>
<?php
}

function print_filter_resources(array $machids, array $filtered = array(), $user_id) {
    ?>
    <script>
        function updateUserResourceFilters(machid, obj) {
            if (obj.checked) {
                state = 'on';
            } else {
                state = 'off';
            }
            switch (state) {
                case 'on' :
                    addUserResourceFilter(machid);
                    break;
                case 'off' :
                    removeUserResourceFilter(machid);
                    break;
                default :
                    break;
            }
        }
        function addUserResourceFilter(machid) {
            console.log('adding resource filter '+machid);
            $.ajax({
                url: "ajax.php",
                method: 'GET',
                data: {a:'addUserResourceFilter', machid:machid, i:'<?php echo $user_id;?>'},
                context: document.body,
                success: function(data, status, xhr){
                    console.log(data);
                }
            });
        }

        function removeUserResourceFilter(machid) {
            console.log('removing resource filter '+machid);
            $.ajax({
                url: "ajax.php",
                method: 'GET',
                data: {a:'removeUserResourceFilter', machid:machid, i:'<?php echo $user_id;?>'},
                context: document.body,
                success: function(data, status, xhr){
                    console.log(data);
                }
            });
        }
    </script>
    <div style="width:500px;min-height:25px;max-height:150px;margin:0 auto;border:solid 1px #000;padding:4px 0 0 4px;">
        <span style="font-weight: bold;float:left;">Resource Filters</span>
        <span style="float:right;cursor: hand;margin-right:5px;"><a onclick="showHide('resource_filter', 'Collapse', 'Expand', this);" style="cursor: hand;">collapse</a></span>
        <div id="resource_filter" style="height:135px;overflow:auto;clear:both;">
            <?php
            foreach ($machids as $m) {
                echo '<input type="checkbox" id="' . $m['machid'] . '" value="' . $m['machid'] . '"
                    onclick="showHideByClass(\'' . $m['machid'] . '\');updateUserResourceFilters(\''.$m['machid'].'\', this);"';
                if (array_key_exists($m['machid'], $filtered)) {
                    echo ' checked="checked"';
                }
                echo '><label for=" ' . $m['machid'] . '">' . $m['name'] . '</label><br>';
            }
            ?>
        </div>
    </div>
    <?php
}