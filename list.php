<?php
use Xmf\Request;
use XoopsModules\Tadtools\My97DatePicker;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_data;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tad_booking_section;
use XoopsModules\Tad_booking\Tools;
/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__, 'index.php');
/*-----------變數過濾----------*/
$op            = Request::getString('op');
$item_id       = Request::getInt('item_id');
$booking_date  = Request::getString('booking_date');
$booking_id    = Request::getInt('booking_id');
$section_id    = Request::getInt('section_id');
$week          = Request::getInt('week');
$uid           = Request::getInt('uid');
$batch_booking = Request::getArray('batch_booking');
$start_date    = Request::getString('start_date', Tools::last_Sunday());
$end_date      = Request::getString('end_date', Tools::next_Saturday());

/*-----------執行動作判斷區----------*/
switch ($op) {

    default:
        $booking_data = $booking = $section_arr = $item_arr = $cate_arr = [];
        $xoopsTpl->assign('cates', Tad_booking_cate::get_all(['enable' => 1], ['items'], [], ['sort' => 'ASC'], 'id'));
        if (! empty($item_id)) {
            $item     = Tad_booking_item::get(['id' => $item_id], ['sections', 'cate']);
            $sections = implode(',', array_keys($item['sections']));

            if ($sections) {
                $booking_data_arr = Tad_booking_data::get_all(["`section_id` IN($sections)", "`booking_date` BETWEEN '$start_date' AND '$end_date'"], ['week'], [], ['booking_date' => 'DESC', 'section_id' => 'ASC', 'waiting' => 'ASC']);
                $booking_id_arr   = array_column($booking_data_arr, 'booking_id');
                if ($booking_id_arr) {
                    $booking_ids = implode(',', $booking_id_arr);
                    $booking_arr = Tad_booking::get_all(['uid' => $_SESSION['now_user']['uid'], "`id` IN($booking_ids)"], [], [], [], 'id');
                }
            }

            $xoopsTpl->assign('item_id', $item_id);
            $xoopsTpl->assign('item', $item);

        } else {
            $booking_arr      = Tad_booking::get_all(['uid' => $_SESSION['now_user']['uid']], [], [], ['start_date' => 'DESC'], 'id');
            $booking_ids      = implode(',', array_keys($booking_arr));
            $booking_data_arr = Tad_booking_data::get_all(["`booking_id` IN($booking_ids)", "`booking_date` BETWEEN '$start_date' AND '$end_date'"], ['week'], [], ['booking_date' => 'DESC', 'section_id' => 'ASC', 'waiting' => 'ASC']);
            $section_id_arr   = array_column($booking_data_arr, 'section_id');
            if ($section_id_arr) {
                $section_arr = Tad_booking_section::get_all(['`id` IN(' . implode(',', $section_id_arr) . ')'], [], [], [], 'id');
                $item_id_arr = array_column($section_arr, 'item_id');
                $item_arr    = Tad_booking_item::get_all(['`id` IN(' . implode(',', $item_id_arr) . ')'], [], [], [], 'id');
                $cate_id_arr = array_column($item_arr, 'cate_id');
                $cate_arr    = Tad_booking_cate::get_all(['`id` IN(' . implode(',', $cate_id_arr) . ')'], [], [], [], 'id');
            }

            $xoopsTpl->assign('section_arr', $section_arr);
            $xoopsTpl->assign('item_arr', $item_arr);
            $xoopsTpl->assign('cate_arr', $cate_arr);
        }
        $xoopsTpl->assign('booking_arr', $booking_arr);
        $xoopsTpl->assign('booking_data_arr', $booking_data_arr);

        $xoopsTpl->assign('start_date', $start_date);
        $xoopsTpl->assign('end_date', $end_date);
        My97DatePicker::render();
        $SweetAlert = new SweetAlert();
        $SweetAlert->render("delete_booking", "ajax.php?op=delete_booking&", ['item_id', 'booking_date', 'section_id', 'booking_id', 'uid']);
        $op = 'tad_booking_list_index';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/
