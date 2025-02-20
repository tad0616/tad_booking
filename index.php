<?php

use Xmf\Request;
use XoopsModules\Tadtools\Bootstrap3Editable;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tools;

/*-----------引入檔案區--------------*/

require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
Utility::test($_SESSION, 'session', 'dd');
/*-----------變數過濾----------*/
$op           = Request::getString('op');
$item_id      = Request::getInt('item_id');
$booking_date = Request::getString('booking_date');
$booking_id   = Request::getInt('booking_id');
$section_id   = Request::getInt('section_id');
$week         = Request::getInt('week');
$uid          = Request::getInt('uid');

/*-----------執行動作判斷區----------*/
switch ($op) {

    default:
        $xoopsTpl->assign('cates', Tad_booking_cate::get_all(['enable' => 1], ['items'], [], ['sort' => 'ASC'], 'id'));
        if (! empty($item_id)) {
            $xoopsTpl->assign('item', Tad_booking_item::get(['id' => $item_id], ['sections', 'week_dates']));
        }
        $Bootstrap3Editable     = new Bootstrap3Editable();
        $Bootstrap3EditableCode = $Bootstrap3Editable->render('.editable', 'ajax.php');
        $xoopsTpl->assign('Bootstrap3EditableCode', $Bootstrap3EditableCode);
        $SweetAlert = new SweetAlert();
        $SweetAlert->render("delete_booking", "ajax.php?op=delete_booking&", ['item_id', 'booking_date', 'section_id', 'booking_id', 'uid']);
        $op = 'tad_booking_section_index';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoopsTpl->assign('max_booking_week', $xoopsModuleConfig['max_booking_week']);
$xoopsTpl->assign('end_date_ts', Tools::end_date($xoopsModuleConfig['max_booking_week'], true));

$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
$xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/
