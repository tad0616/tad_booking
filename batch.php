<?php
use Xmf\Request;
use XoopsModules\Tadtools\FormValidator;
use XoopsModules\Tadtools\My97DatePicker;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_data;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tad_booking_week;
use XoopsModules\Tad_booking\Tools;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__, 'index.php');
/*-----------變數過濾----------*/
$op                   = Request::getString('op');
$item_id              = Request::getInt('item_id');
$booking_date         = Request::getString('booking_date');
$booking_id           = Request::getInt('booking_id');
$section_id           = Request::getInt('section_id');
$week                 = Request::getInt('week');
$uid                  = Request::getInt('uid');
$week_section_id      = Request::getArray('week_section_id');
$start_date           = Request::getString('start_date', date('Y-m-d'));
$end_date             = Request::getString('end_date', Tools::end_date($xoopsModuleConfig['max_booking_week']));
$act                  = Request::getString('act');
$content              = Request::getString('content');
$booking_week_section = Request::getArray('booking_week_section');
$approval             = Request::getString('approval');
/*-----------執行動作判斷區----------*/
switch ($op) {
    case 'tad_booking_batch_store':
        $now_user_name = empty($_SESSION['now_user']['name']) ? $_SESSION['now_user']['uname'] : $_SESSION['now_user']['name'];
        $booking_id    = Tad_booking::store(['start_date' => $booking_date, 'end_date' => $booking_date, 'content' => $content, 'info' => ['name' => $now_user_name, 'email' => $_SESSION['now_user']['email'], 'batch' => $booking_week_section, 'start_date' => $start_date, 'end_date' => $end_date]]);

        $status    = empty($approval) ? 1 : 0;
        $pass_date = empty($approval) ? date('Y-m-d') : '0000-00-00';
        foreach ($booking_week_section as $week => $week_section_id) {
            foreach ($week_section_id as $section_id => $booking_date_arr) {
                Tad_booking_week::store(['booking_id' => $booking_id, 'section_id' => $section_id, 'week' => $week, 'start_date' => $start_date, 'end_date' => $end_date]);
                foreach ($booking_date_arr as $booking_date) {
                    $waiting = Tad_booking_data::max_waiting($section_id, $booking_date);
                    Tad_booking_data::store(['booking_id' => $booking_id, 'booking_date' => $booking_date, 'section_id' => $section_id, 'waiting' => $waiting, 'status' => $status, 'pass_date' => $pass_date]);
                }
            }
        }
        redirect_header("index.php?item_id={$item_id}", 3, _MD_TADBOOKING_BATCH_BOOKINGS_COMPLETED);
        break;

    default:
        $xoopsTpl->assign('cates', Tad_booking_cate::get_all(['enable' => 1], ['items'], [], ['sort' => 'ASC'], 'id'));
        if (! empty($item_id)) {
            $item_other_arr[] = 'sections';
            if ($_REQUEST['start_date'] and $_REQUEST['end_date']) {
                $item_other_arr[] = 'booking_arr';
            }
            $item = Tad_booking_item::get(['id' => $item_id], $item_other_arr);
            // Utility::dd($item);
            if (! empty($item['booking_arr'])) {
                $my_batch_booking = Tools::findDatesByDaysOfWeekGrouped($start_date, $end_date, array_keys($week_section_id));
                foreach ($item['booking_arr'] as $booking_date => $booking_arr) {
                    $w = date("w", strtotime($booking_date));
                    foreach ($booking_arr as $section_id => $uid_booking_arr) {
                        foreach ($uid_booking_arr as $uid => $booking) {
                            $item['week_sections'][$section_id][$w][$booking_date][$booking['waiting']] = ['name' => $booking['info']['name'], 'booking' => $booking];
                        }
                    }
                }
            }
            // Utility::dd($item);
            $xoopsTpl->assign('item', $item);
            $xoopsTpl->assign('my_batch_booking', $my_batch_booking);
            $xoopsTpl->assign('week_section_id', $week_section_id);
        }
        $xoopsTpl->assign('content', $content);
        $xoopsTpl->assign('start_date', $start_date);
        $xoopsTpl->assign('end_date', $end_date);
        $xoopsTpl->assign('minDate', date('Y-m-d'));
        $xoopsTpl->assign('maxDate', Tools::end_date($xoopsModuleConfig['max_booking_week']));
        My97DatePicker::render();
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();
        if (empty($op)) {
            $op = 'tad_booking_batch_create';
        }

        $xoopsTpl->assign('act', $act);
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoopsTpl->assign('max_booking_week', $xoopsModuleConfig['max_booking_week']);
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
$xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/
