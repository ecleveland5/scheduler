<?php
include_once('lib/Template.class.php');
include_once('lib/Account.class.php');
include_once('lib/CmnFns.class.php');

$billing_data = null;
if ( isset($_GET['account_id']) && !empty($_GET['account_id']) ) {
	if (is_numeric($_GET['account_id'])) {
		$account = new Account($_GET['account_id']);
		$billing_data = $account->getBillingData();
		$filename = $account->getField('FRS');
	} else {
		$accounts = explode(",",$_GET['account_id']);
		if (count($accounts)>0) {
			$billing_data = array();
			foreach ($accounts as $account_id) {
				$account = new Account($account_id);
				if ($account->isAdmin($_SESSION['sessionID'])) {
          $new_data = $account->getBillingData();
          if (!empty($billing_data)) {
            $billing_data = array_merge($billing_data, $new_data);
          } else {
            $billing_data = $new_data;
          }
          //var_dump($billing_data);
				}
			}
		}
		$filename = "all_accounts";
	}
	if (!empty($billing_data)) {
		//print_r($billing_data);
    //echo "here";
		CmnFns::exportToExcel($billing_data,'nanocenter_billing_data_'.$filename.'.csv');
	} else {
    echo "No billing data found.";
  }
}