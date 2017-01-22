<?php session_start(); 
include "../functions.php";
include "createmenu.php";

if(!isset($_SESSION['loginuser'])) {
    header('Location: login');
}
$menu_html = "";
$user_info = get_user_info($_SESSION['loginuser']);
$user_info_name = $user_info[$db_user_name];
$user_info_permissions = $user_info[$db_user_permissions];

if( $user_info_permissions == PERMISSION_PARTICIPANT ) {
    header('Location: deelnemen');
} else {
    header('Location: secure/index');
}

?>
