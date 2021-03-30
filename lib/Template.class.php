<?php
/**
* This file provides output functions
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author Richard Cantzler <rmcii@users.sourceforge.net>
* @version 07-12-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
//@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* Include Auth class
*/
//include_once('Auth.class.php');

/**
* Provides functions for outputting template HTML
*/
class Template {
	var $title;
	var $link;
	var $dir_path;
	
	/**
	* Set the page's title
	* @param string $title Title of page
	* @param int $depth Depth of the current page relative to root
	*/
	function __construct($title = '', $depth = 0) {
		global $conf;
		
		$this->title = (!empty($title)) ? $title : $conf['ui']['welcome'];
		$this->dir_path = str_repeat('../', $depth);
		$this->link = CmnFns::getNewLink();
	}
	
	/**
	* Print all XHTML headers
	* This function prints the HTML header code, CSS link, and JavaScript link
	*
	* DOCTYPE is XHTML 1.0 Transitional
	* @param string $title Title of page.
	*/
	function printHTMLHeader($title='') {
		global $conf;
		global $languages;
		global $lang;
		global $charset;
		
		$path = $this->dir_path;
	?>
	<!DOCTYPE html>
	<html>
        <head>
        <title><?php echo $this->title?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="shortcut icon" href="/favicon.gif">
        <script language="JavaScript" type="text/javascript" src="<?php echo $path?>functions.js"></script>
        <style type="text/css">
        @import url(<?php echo $path?>css.css);
        </style>
        <script>
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-85113-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
            <script src="<?php echo $path;?>/lib/js/jquery/js/jquery-3.1.1.min.js" language="JavaScript" type="text/javascript"></script>
            <script src="<?php echo $path;?>/lib/js/jquery/jquery-ui-1.12.1/jquery-ui.min.js" language="JavaScript" type="text/javascript"></script>
            <link href="<?php echo $path;?>/lib/js/jquery/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet" type="text/css"/>
	</head>
	<body>
	<h1 style="margin: 0;text-align: center;"><?php if (!empty($title)) { echo $title; } else { echo $conf['app']['title']; }?></h1>
	<?php
	}
	
	
	/**
	* Print welcome header message
	* This function prints out a table welcoming
	*  the user.  It prints links to My Control Panel,
	*  Log Out, Help, and Email Admin.
	* If the user is the admin, an admin banner will
	*  show up
	* @global $conf
	*/
	function printWelcome() {
		global $conf;
		global $auth;
		
		// Print out notice for administrator
		echo $auth->isAdmin() ? '<h3 align="center">' . translate('Administrator') . '</h3>' : '';
		
		// Print out logoImage if it exists
		echo (!empty($conf['ui']['logoImage']))
			? '<div align="left"><img src="' . $conf['ui']['logoImage'] . '" alt="logo" vspace="5" /></div>'
			: '';
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="5" class="mainBorder">
	  <tr>
		<td class="mainBkgrdClr">
		  <h4 class="welcomeBack"><?php echo translate('Welcome Back', array($_SESSION['sessionUsername'], 1))?></h4>
		  <p>
			<?php $this->link->doLink($this->dir_path . 'index.php?logout=true', translate('Log Out')) ?>
			|
			<?php $this->link->doLink($this->dir_path . 'ctrlpnl.php', translate('My Control Panel')) ?>
		  </p>
		</td>
		<td class="mainBkgrdClr" valign="top">
		  <div align="right">
		    <p>
			<?php echo translateDate('header', time());?>
			</p>
			<p>
			  <?php $this->link->doLink('javascript: help();', translate('Help')) ?>
			</p>
		  </div>
		</td>
	  </tr>
	</table>
	<?php
	}
	
	
	/**
	* Start main HTML table
	* @param none
	*/
	function startMain() {
	?>
	<p>&nbsp;</p>
	<table width="100%" border="0" cellspacing="0" cellpadding="10" style="border: solid #CCCCCC 1px;">
	  <tr>
		<td bgcolor="#FAFAFA">
		  <?php
	}
	
	
	/**
	* End main HTML table
	* @param none
	*/
	function endMain() {
	?>
		</td>
	  </tr>
	</table>
	<?php
	}
	
	
	/**
	* Print HTML footer
	* This function prints out a tech email
	* link and closes off HTML page
	* @global $conf
	*/
	function printHTMLFooter() {
		global $conf;
	?>
	<br /><br />
	<p align="center">
		Please help us make this system as useful to you as possible by<br />
		reporting <strong>problems</strong> and sending us <strong>suggestions</strong>.<br /><br />
		<a href="mailto:nanocenter@umd.edu?subject=Scheduler Problem"><strong>Problems</strong></a> or <a href="mailto:nanocenter@umd.edu?subject=Scheduler Suggestion"><strong>Suggestions</strong></a></p>
		<script language="JavaScript" type="text/javascript" src="wz_tooltip.js"></script>
	</body>
	</html>
	<?php
	}
	
	/**
	* Sets the link class variable to reference a new Link object
	* @param none
	*/
	function setLink() {
		$this->link = CmnFns::getNewLink();
	}
	
	/**
	* Returns the link object
	* @param none
	* @return link object for this class 
	*/
	function getLink() {
		return $this->link;
	}
	
	/**
	* Sets a new title for the template page
	* @param string $title title of page
	*/
	function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	* Prints a link to sort in ascending order
	* @param pager $pager a pager object that keeps track of pagination
	* @param string $order a string representation of the fields by which to order
	* @param string $test the text to display for the field on which it is sorting
	*/
	function printAscLink(&$pager, $order, $text) {
		$text = translate("Sort by ascending $text");
		print_asc_desc_link($pager, $order, $text, 'ASC');
	}
	
	/**
	* Prints out a link to reorder recordset descending order
	* @param Object $pager pager object
	* @param string $order order to sort result set by
	* @param string $text link text
	* @see print_asc_desc_link()
	*/
	function printDescLink(&$pager, $order, $text) {
		$text = translate("Sort by descending $text");
		print_asc_desc_link($pager, $order, $text, 'DESC');
	}
	
	/**
	* This function extends the printAscLink and printDescLink, printing out
	*  a link to reorder a recordset in a certain order
	* This was added to keep the current printAsc/DescLink functions in place, but put
	*  all logic into one function
	* @param Object $pager pager object
	* @param string $order order to sort result set by
	* @param string $text link text
	* @param string $vert ascending or descending order
	*/
	function printAscDescLink(&$pager, $order, $text, $vert) {
		global $link;      
		
		$tool = getTool();
		$page = $pager->getPageNum();
		
		$plus_minus = ($vert == 'ASC') ? '[+]' : '[&#8211;]';		// Plus or minus box
		$limit_str = '&amp;' . $pager->getLimitVar() . '=' . $pager->getLimit();
		$page_str  = '&amp;' . $pager->getPageVar() . '=' . $pager->getPageNum();
		$vert_str  = "&amp;vert=$vert";
		
		// Fix up the query string
		$query =  $_SERVER['QUERY_STRING'];
		if (preg_match('(\?|&)' . $pager->getLimitVar() . "=[0-9]*", $query)) {
			$query = preg_replace('(\?|&)' . $pager->getLimitVar() . "=[0-9]*", $limit_str, $query);
		} else {
			$query .= $limit_str;
		}
		if (preg_match('(\?|&)' . $pager->getPageVar() . "=[0-9]*", $query)) {
			$query = preg_replace('(\?|&)' . $pager->getPageVar() . "=[0-9]*", $page_str, $query);
		} else {
			$query .= $page_str;
		}
		if (preg_match("(\?|&)vert=[a-zA-Z]*", $query)) {
			$query = preg_replace("(\?|&)vert=[a-zA-Z]*", $vert_str, $query);
		} else {
			$query .= $vert_str;
		}
		if (preg_match("(\?|&)order=[a-zA-Z]*", $query)) {
			$query = preg_replace("(\?|&)order=[a-zA-Z_]*", "&amp;order=$order", $query);
		} else {
			$query .= "&amp;order=$order";
		}
		$link->doLink($_SERVER['PHP_SELF'] . '?' . $query, $plus_minus, '', '', $text);
	}
	
	public function printPleaseLogIn() {
	    global $conf;
	    
	    echo "<p align='center'>";
	    echo "Please log in to access the " . $conf['app']['title'];
		echo "</p>";
		echo "<p align='center'>";
	    echo Link::getLink($conf['app']['weburi'] . '/?login=login', 'Log In');
	    echo "</p>";
	}
}
