<?php


function xoops_module_uninstall_tad_booking($module)
{
    global $xoopsDB;
    $date = date("Ymd");

    rename(XOOPS_ROOT_PATH . "/uploads/tad_booking", XOOPS_ROOT_PATH . "/uploads/tad_booking_bak_{$date}");

    return true;
}
