<?php

use XoopsModules\Tad_booking\Tools;
if (! class_exists('XoopsModules\Tad_booking\Tools')) {
    require XOOPS_ROOT_PATH . '/modules/tad_booking/preloads/autoloader.php';
}

Tools::get_session();
$interface_menu[_MD_TADBOOKING_INDEX] = "index.php";
$interface_icon[_MD_TADBOOKING_INDEX] = "fa-calendar-plus";
if ($_SESSION['can_approve']) {
    $interface_menu[_MD_TADBOOKING_APPROVAL] = "approval.php";
    $interface_icon[_MD_TADBOOKING_APPROVAL] = "fa-check-square";
}
if ($_SESSION['can_booking']) {
    $interface_menu[_MD_TADBOOKING_BATCH] = "batch.php";
    $interface_icon[_MD_TADBOOKING_BATCH] = "fa-address-book";
    $interface_menu[_MD_TADBOOKING_LIST]  = "list.php";
    $interface_icon[_MD_TADBOOKING_LIST]  = "fa-id-card";
}
if ($_SESSION['tad_booking_adm']) {
    $interface_menu[_MD_TADBOOKING_MANAGER] = "manager.php";
    $interface_icon[_MD_TADBOOKING_MANAGER] = " fa-screwdriver-wrench";
}
