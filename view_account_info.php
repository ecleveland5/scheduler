<?php
/**
* Interface form for placing/modifying/viewing payment accounts
* This file will present a form for a user to
*  make a new account or modify/delete an old one.
* It will also allow other users to view this account.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 01-29-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Account.class.php');
include_once('lib/User.class.php');
include_once('lib/Auth.class.php');
include_once('templates/cpanel.template.php');

global $link;

$t = new Template();
$auth = new Auth();
$user = new User($auth->getCurrentID());
$account_id = filter_input(INPUT_GET, 'account_id');
$account = new Account($account_id);
$view = filter_input(INPUT_POST, 'view');

// To Do: set up translate
$t->set_title("Account Info");
$t->printHTMLHeader();

// Do modify/add


$t->printWelcome();
$t->startMain();
startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

// Following function checks user permissions
print_account_details($account);

// Get billing data
$billing_data = $account->get_billing_data();
?>
<!--
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="billing_view">
	<select name="view" onchange="this.form.submit();">
		<option value="by_accounts" <?php if ($view=='by_accounts') echo 'selected'; ?>>By Accounts</option>
		<option value="by_users" <?php if ($view=='by_users') echo 'selected'; ?>>By Users</option>
	</select>
</form>
-->
<style>
	table.billing_details {
		width: 100%;
	}

	td.rowHeaders {
		width: 200px;
	}
</style>

<?php
	$month_totals = array();
	if (!empty($billing_data)) {
        foreach ($billing_data as $bill) {
            $month_totals[$bill['billed']] = array();
	        $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']] = array();
	        $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']] = array();
	        $month_totals[$bill['billed']]['total'] = 0;
	        $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']]['total'] = 0;
	        $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']]['total'] = 0;
            $month_totals[$bill['billed']]['total'] += $bill['Amount Billed'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']]['total'] += $bill['Amount Billed'];
            $count = count($month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']]);
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['id'] = $bill['id'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['lab'] = $bill['Lab'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['equipment'] = $bill['Equipment'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['rate'] = $bill['Rate'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['hours'] = $bill['Amt Used'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['amount'] = $bill['Amount Billed'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['resid'] = $bill['Transaction ID'];
            $month_totals[$bill['billed']][$bill['User Last Name'].', '.$bill['User First Name']][$bill['date']][$count+1]['notes'] = $bill['notes'];
        }
	}
?>

<table width="100%">
	<tr>
		<td class="tableBorder">

			<table class="billing_details">
				<tr><td class="tableTitle">Previous Month(s) Billing Totals
					<span style="float:right;">
					<a href="help_view_account_info.php" target="_blank" style="vertical-align:top">Help</a>
					<img src="img/icon.question.png" style="border:0; margin:0; padding:0;">
					</span>
					</td>
				</tr>

				<tr><td class="cellColor">

				<?php
					if (sizeof($month_totals) > 0) {
				?>
<!-- By Billing Month -->

						<table width="100%" cellpadding=0 cellspacing=0>
						<?php
							$count = 0;
							foreach ($month_totals as $month=>$label) {
								$count++;
								if ($month!='total') {

						?>
							<tr>
								<td width=15><?php echo $count; ?></td>
								<td width=175><a href="javascript: showHide('<?php echo $month; ?>');"><?php echo $month; ?></a></td>
								<td><a href="javascript: showHide('<?php echo $month; ?>');">$<?php echo number_format($label['total'], 2, '.', ','); ?></a></td>
							</tr>
							<tr><td style="border-bottom: solid 1px #000;"></td>
								<td colspan=2 style="border-bottom: solid 1px #000;">


	<!-- By User -->


									<div id="<?php echo $month; ?>" style="display: none;">
									<table width="100%" cellpadding=0 cellspacing=0>
									<?php
									foreach ($label as $name=>$user) {
										if ($name!='total') {
										?>

										<tr>
											<td width=171 style="border-bottom: solid 1px #000;" valign="top" nowrap><a href="javascript: showHide('<?php echo $month.'_'.$name; ?>');"><?php echo $name;?></a></td>
											<td style="border-bottom: solid 1px #000;"><a href="javascript: showHide('<?php echo $month.'_'.$name; ?>');">$<?php echo number_format($user['total'], 2, '.', ','); ?></a><br>


		<!-- By Date -->

												<div id="<?php echo $month.'_'.$name; ?>" style="display: none;">
													<table width="100%" cellpadding=0 cellspacing=0>
														<?php
															foreach ($user as $date=>$res) {
																if ($date!='total') {
														?>

														<tr>
															<td style="border-top: solid 1px #000;" width="100" valign="top" nowrap><a href="javascript: showHide('<?php echo $month.'_'.$date.'_'.$name; ?>');"><?php echo $date; ?></a></td>
															<td style="border-top: solid 1px #000;">
																<a href="javascript: showHide('<?php echo $month.'_'.$date.'_'.$name; ?>');">
																<?php
																	$date_total = 0;
																	foreach ($res as $bill=>$charge) {
																		$date_total += $charge['amount'];
																	}

																	echo "$".number_format($date_total, 2,'.',',');
																?>
																</a>


			<!-- By Charge -->

																<div id="<?php echo $month.'_'.$date.'_'.$name; ?>" style="display: none;">
																	<table width="100%" cellpadding=0 cellspacing=0>
																<?php
																	foreach ($res as $a=>$desc) {
																		//var_dump($desc);
																?>
																		<tr style="border-bottom: solid 1px #000;">
																			<td width="250" style="border-bottom: solid 1px #000;<?php if ($desc['equipment']=='NanoCenter Member Discount') echo 'background-color:#9bf'; ?>">
																				<?php echo $link->getLink("javascript: reserve('".RES_TYPE_VIEW."','','','" . $desc['resid']. "');", ($desc['equipment']=='NanoCenter Member Discount' ? ' * * 20% NC Member Discount' : $desc['equipment']), '', '', translate('View this reservation')); ?>
																			</td>
																			<td width="75"  style="border-bottom: solid 1px #000;<?php if ($desc['equipment']=='NanoCenter Member Discount') echo 'background-color:#9bf'; ?>">$<?php echo $desc['rate']; ?>/hr</td>
																			<td width="75"  style="border-bottom: solid 1px #000;<?php if ($desc['equipment']=='NanoCenter Member Discount') echo 'background-color:#9bf'; ?>"><?php echo $desc['hours']; ?> hrs</td>
																			<td width="75"  style="border-bottom: solid 1px #000;<?php if ($desc['equipment']=='NanoCenter Member Discount') echo 'background-color:#9bf'; ?>">$<?php echo number_format($desc['amount'], 2, '.', ','); ?></td>
																			<td 			style="border-bottom: solid 1px #000;<?php if ($desc['equipment']=='NanoCenter Member Discount') echo 'background-color:#9bf'; ?>"><?php echo $desc['notes']; ?></td>
																		</tr>
																<?php
																	}
																?>
																	</table>
																</div>
															</td>
														</tr>
														<?php
																}
															}
														?>

													</table>
												</div>
											</td>
										</tr>
						<?php
									}
								}
						?>
									</table>
									</div>
								</td>
							</tr>
						<?php
								}
							}
						?>
							<tr><td colspan="2">
									<script>
									function download() {
										window.open('downloadAsExcel.php?account_id=<?php echo $_GET['account_id']; ?>');
									}
									</script>
									<a href="javascript: download();">Download Excel File</a>
								</td>
							</tr>
						</table>
					<?php
						} else {
							echo "No Billing Data";
						}
					?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();


?>