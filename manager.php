<?php
use Xmf\Request;



/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_manager.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/*-----------變數過濾----------*/
$op = Request::getString('op');


/*-----------執行動作判斷區----------*/
switch($op){
    
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar' , Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/

//
function f1(){
    global $xoopsDB , $xoopsTpl;
    $main="Hello World!";
    $xoopsTpl->assign('main' , $main);
}

//
function f2(){
    global $xoopsDB;
}
