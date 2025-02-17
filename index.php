<?php
use Xmf\Request;
use XoopsModules\Tadtools\Bootstrap3Editable;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_item;

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

    // //新增資料
    // case 'tad_booking_store':
    //     $id = Tad_booking::store();
    //     header("location: {$_SERVER['PHP_SELF']}?id=$id");
    //     exit;

    // //更新資料
    // case 'tad_booking_update':
    //     $where_arr['id'] = $id;
    //     Tad_booking::update($where_arr);
    //     header("location: {$_SERVER['PHP_SELF']}?id=$id");
    //     exit;

    // //新增用表單
    // case 'tad_booking_create':
    //     Tad_booking::create();
    //     break;

    // //修改用表單
    // case 'tad_booking_edit':
    //     Tad_booking::create($id);
    //     $op = 'tad_booking_create';
    //     break;

    // //刪除資料
    // case 'tad_booking_destroy':
    //     Tad_booking::destroy($id);
    //     header("location: {$_SERVER['PHP_SELF']}");
    //     exit;

    // //列出所資料
    // case 'tad_booking_index':
    //     $where_arr = [];
    //     Tad_booking::index($where_arr, [], [], [], 20);
    //     break;

    // //顯示某筆資料
    // case 'tad_booking_show':
    //     $where_arr['id'] = $id;
    //     Tad_booking::show($where_arr);
    //     break;

    // //新增資料
    // case 'tad_booking_data_store':
    //     Tad_booking_data::store($tad_booking_data);
    //     header("location: {$_SERVER['PHP_SELF']}?booking_date=$booking_date&booking_id=$booking_id&section_id=$section_id");
    //     exit;

    // //新增用表單
    // case 'tad_booking_data_create':
    //     Tad_booking_data::create();
    //     break;

    // //修改用表單
    // case 'tad_booking_data_edit':
    //     Tad_booking_data::create($booking_date, $booking_id, $section_id);
    //     $op = 'tad_booking_data_create';
    //     break;

    // //列出所資料
    // case 'tad_booking_data_index':
    //     $where_arr['status'] = '1';
    //     Tad_booking_data::index($where_arr, [], [], [], 20);
    //     break;

    // //顯示某筆資料
    // case 'tad_booking_data_show':
    //     $where_arr['booking_date'] = $booking_date;
    //     $where_arr['booking_id']   = $booking_id;
    //     $where_arr['section_id']   = $section_id;

    //     Tad_booking_data::show($where_arr);
    //     break;

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
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
$xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/
