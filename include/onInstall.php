<?php
use XoopsModules\Tadtools\Utility;
if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}



function xoops_module_install_tad_booking(&$module)
{

    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_booking");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_booking/file");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_booking/image");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_booking/image/.thumbs");

    return true;
}
