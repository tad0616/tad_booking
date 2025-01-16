<?php
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Update;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}
if (!class_exists('XoopsModules\Tad_booking\Update')) {
    require dirname(__DIR__) . '/preloads/autoloader.php';
}



function xoops_module_update_tad_booking($module, $old_version)
{
    global $xoopsDB;

    //if(Update::chk_1()) Update::go_1();

    return true;
}
