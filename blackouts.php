<?php
    /**
     * Blackout Scheduler Application
     * Manage blackout times from this file
     * @author Nick Korbel <lqqkout13@users.sourceforge.net>
     * @version 06-24-04
     * @package phpScheduleIt
     *
     * Copyright (C) 2003 - 2005 phpScheduleIt
     * License: GPL, see LICENSE
     */
    list($s_sec, $s_msec) = explode(' ', microtime());	// Start execution timer
    /**
     * Include Template class
     */
    include_once('bootstrap.php');
    include_once(BASE_DIR . '/lib/Lab.class.php');
    include_once(BASE_DIR . '/templates/cpanel.template.php');
    global $auth;
    
    $t = new Template(translate('Manage Blackout Times'));
    $t->printHTMLHeader();
    
    if ($auth->isLoggedIn()) {
        $user = new User($auth->getCurrentID());
        $lab_id = filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_STRING);
    
        if ($lab_id === null) {
            $lab_id = $user->getLabPref();
        }
        $s = new Lab($lab_id, BLACKOUT_ONLY);
    
        // Check that the admin is logged in
        if (!$auth->isAdmin()) {
            CmnFns::do_error_box(translate('This is only accessible to the administrator') . '<br />'
                . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
        }
    
        // Print welcome box
        $t->printWelcome();
    
        // Begin main table
        $t->startMain();
        startQuickLinksCol();
        showQuickLinks();        // Print out My Quick Links
        startDataDisplayCol();
        $filter = array();
        echo "<h2>" . translate('Manage Blackout Times') . "</h2>";
        $s->printJumpLinks();
        $s->printLab($filter);
    
        // Print out links to jump to new date
        $s->printJumpLinks();
    
        // End main table
        $t->endMain();
    }
    
    list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
    $tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
    echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
    // Print HTML footer
    $t->printHTMLFooter();