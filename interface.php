<?php

use XoopsModules\Tad_booking\Tools;
if (!class_exists('XoopsModules\Tad_booking\Tools')) {
    require XOOPS_ROOT_PATH . '/modules/tad_booking/preloads/autoloader.php';
}

Tools::get_session();
$interface_menu[_TAD_TO_MOD]="index.php";
$interface_icon[_TAD_TO_MOD]="fa-chevron-right";
$interface_menu[_MD_TADBOOKING_PAGE_1]="approval.php";
$interface_icon[_MD_TADBOOKING_PAGE_1]="fa-chevron-right";
$interface_menu[_MD_TADBOOKING_PAGE_2]="batch.php";
$interface_icon[_MD_TADBOOKING_PAGE_2]="fa-chevron-right";
$interface_menu[_MD_TADBOOKING_PAGE_3]="list.php";
$interface_icon[_MD_TADBOOKING_PAGE_3]="fa-chevron-right";
$interface_menu[_MD_TADBOOKING_PAGE_4]="manager.php";
$interface_icon[_MD_TADBOOKING_PAGE_4]="fa-chevron-right";


if ($_SESSION['tad_booking_adm']) {
    $interface_menu[_TAD_TO_ADMIN] = "admin/main.php";
    $interface_icon[_TAD_TO_ADMIN] = "fa-sign-in";
}
