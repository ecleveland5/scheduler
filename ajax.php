<?php
include_once('lib/Auth.class.php');
include_once('lib/User.class.php');

switch ($_GET['a']) {
    case 'gua' :
        $user_id = filter_input(INPUT_GET,'i');
        $user = new User($user_id);
        $accts = $user->get_accounts_list();
        echo json_encode($accts);
        break;
    case 'addUserResourceFilter' :
        $user_id = filter_input(INPUT_GET,'i');
        $user = new User($user_id);
        $user->add_user_resource_filter(filter_input(INPUT_GET,'machid'));
        echo 'add completed';
        break;
    case 'removeUserResourceFilter' :
        $user_id = filter_input(INPUT_GET,'i');
        $user = new User($user_id);
        $user->remove_user_resource_filter(filter_input(INPUT_GET,'machid'));
        echo 'remove completed';
        break;
    case 'getUserAccounts' :
        $user_id = filter_input(INPUT_GET,'user_id');
        $user = new User($user_id);
        $accounts = $user->get_accounts_list();
        echo json_encode($accounts);
        //var_dump($accounts);
        break;
    default :
        echo 'No Action Requested';
        break;
}