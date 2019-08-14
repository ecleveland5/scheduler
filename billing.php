<?
/*
*	Billing
*	manage_billing.php - page calls for update of billing table and display of top level
*	view_account_bills.php - shows monthly list of totals charged to each account
*	view_bill.php - page gets all usage data
*	edit_bill.php - adds records to billing table for adjustment to a bill
*/

?>
<html>
<head>
<title>NanoCenter Scheduler Billing</title>
</head>
<style>
#header {
	height: 100px;
	text-align: left;	
}

#date_pick {
	position: relative;
	margin: 0 auto; 
	padding: 0;
}

#date_pick ul {
	position: absolute;
	right: -5px; top: 15px;
	font: bolder 1.3em 'Trebuchet MS', sans-serif;
	color: #FFF;
	list-style: none;
	margin: 0; padding: 0;		
}

#date_pick li {
}

#date_pick li a {
	float: left;
	display: block;
	padding: 3px 12px;	
	color: #FFF;
	background-color: #333;
	text-decoration: none;
	border-right: 1px solid #272727;
}

#date_pick li a:hover {
	background: #65944A;
	color: #FFF;
}

#date_pick li a#current  {
	background: #65944A;
	color: #FFF;
}
</style>

<body>

<div id="date_pick">
	<div id="year">
		<ul><li>2001</li>
			<li>2002</li>
			<li>2003</li>
			<li>2004</li>
			<li>2005</li>
			<li>2005</li>
			<li>2006</li>
			<li>2007</li>
		</ul>
	</div>
	<div id="month">
		<ul><li>Jan</li>
			<li>Feb</li>
			<li>Mar</li>
			<li>Apr</li>
			<li>May</li>
			<li>Jun</li>
			<li>Jul</li>
			<li>Aug</li>
			<li>Sep</li>
			<li>Oct</li>
			<li>Nov</li>
			<li>Dec</li>
		</ul>
	</div>
</div>

<div id="frs_list">
	<table>
		<tr><td>FRS</td>
			<td>Name</td>
			<td>Totals</td>
			<td>Send Email</td>
			<td>Set Status</td>
			<td>Last Update</td>
			<td>Hide</td>
		</tr>
	</table>
</div>

</body>
</html>