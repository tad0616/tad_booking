<?php

use Xmf\Request;
use XoopsModules\Tadtools\BootstrapTable;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_data;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tad_booking_section;
use XoopsModules\Tad_booking\Tad_booking_week;
use XoopsModules\Tad_booking\Tools;

/*-----------引入檔案區--------------*/

require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

Tools::chk_is_adm('can_approve', '', __FILE__, __LINE__, 'index.php');
/*-----------變數過濾----------*/
$op            = Request::getString('op');
$item_id       = Request::getInt('item_id');
$booking_date  = Request::getString('booking_date');
$booking_id    = Request::getInt('booking_id');
$section_id    = Request::getInt('section_id');
$week          = Request::getInt('week');
$uid           = Request::getInt('uid');
$batch_booking = Request::getArray('batch_booking');

/*-----------執行動作判斷區----------*/
switch ($op) {

    //批次通過
    case 'batch_update_booking_pass':
        batch_update_booking_status($batch_booking, 1);
        header("location: {$_SERVER['PHP_SELF']}?item_id=$item_id");
        exit;
    //批次不通過
    case 'batch_update_booking_deny':
        batch_update_booking_status($batch_booking, 0);
        header("location: {$_SERVER['PHP_SELF']}?item_id=$item_id");
        exit;

    //批次刪除
    case "batch_delete_booking":
        batch_delete_booking($batch_booking);
        header("location: {$_SERVER['HTTP_REFERER']}");
        exit;

    //刪除資料
    case 'tad_booking_data_destroy':
        Tad_booking_data::destroy($booking_date, $booking_id, $section_id);
        header("location: {$_SERVER['PHP_SELF']}?item_id=$item_id");
        exit;

    //更新資料
    case 'tad_booking_data_update':
        $where_arr['booking_date'] = $booking_date;
        $where_arr['booking_id']   = $booking_id;
        $where_arr['section_id']   = $section_id;

        Tad_booking_data::update($where_arr);
        header("location: {$_SERVER['PHP_SELF']}?booking_date=$booking_date&booking_id=$booking_id&section_id=$section_id");
        exit;

    default:
        $xoopsTpl->assign('cates', Tad_booking_cate::get_all(['enable' => 1], ['approval_items'], [], ['sort' => 'ASC'], 'id'));
        if (! empty($item_id)) {
            $booking_data   = $booking   = [];
            $item           = Tad_booking_item::get(['id' => $item_id], ['sections']);
            $booking_data   = Tad_booking_data::get_all(['item_id' => $item_id, 'approver' => 0, 'pass_date' => '0000-00-00', '`status`!=1'], ['week'], [], ['booking_date' => 'ASC', 'section_id' => 'ASC', 'waiting' => 'ASC']);
            $booking_id_arr = array_column($booking_data, 'booking_id');
            if ($booking_id_arr) {
                $booking_ids = implode(',', $booking_id_arr);
                $booking     = Tad_booking::get_all(["`id` IN($booking_ids)"], [], [], [], 'id');
            }

            $xoopsTpl->assign('item', $item);
            $xoopsTpl->assign('booking_data', $booking_data);
            $xoopsTpl->assign('booking', $booking);
        } else {
            $booking_data   = Tad_booking_data::get_all(['approver' => 0, 'pass_date' => '0000-00-00', '`status`!=1'], ['week'], [], ['booking_date' => 'ASC', 'section_id' => 'ASC', 'waiting' => 'ASC']);
            $booking_id_arr = array_column($booking_data, 'booking_id');
            if ($booking_id_arr) {
                $booking_ids = implode(',', $booking_id_arr);
                $booking     = Tad_booking::get_all(["`id` IN($booking_ids)"], [], [], [], 'id');
            }
            $item_id_arr = array_column($booking_data, 'item_id');
            if ($item_id_arr) {
                $item_ids = implode(',', $item_id_arr);
                $items    = Tad_booking_item::get_all(["`id` IN($item_ids)"], [], [], [], 'id');
            }
            $xoopsTpl->assign('booking_data', $booking_data);
            $xoopsTpl->assign('booking', $booking);
            $xoopsTpl->assign('items', $items);

        }
        $BootstrapTable = BootstrapTable::render();

        $SweetAlert = new SweetAlert();
        $SweetAlert->render("delete_booking", "ajax.php?op=delete_booking&", ['item_id', 'booking_date', 'section_id', 'booking_id', 'uid']);
        $op = 'tad_booking_approval_index';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/

function batch_delete_booking($batch_booking_arr)
{
    foreach ($batch_booking_arr as $item_id => $item_booking) {
        if (in_array($item_id, $_SESSION['can_approve']) || $_SESSION['tad_booking_adm']) {
            $booking_id_arr = array_unique(array_merge(...array_map('array_keys', $item_booking)));
            if ($booking_id_arr) {
                $all_booking = Tad_booking::get_all(['`id` IN(' . implode(',', $booking_id_arr) . ')'], [], [], [], 'id');
            } else {
                $all_booking = [];
            }

            foreach ($item_booking as $section_id => $section_booking) {
                foreach ($section_booking as $booking_id => $dates) {
                    $booking = $all_booking[$booking_id];
                    foreach ($dates as $booking_date) {
                        if ($section_id && $booking_date) {
                            //取得用了該日期時段的uid
                            list($booking_arr, $ok_booking) = Tools::booking_arr($booking_date, $booking_date, $section_id);
                            $del_waiting                    = $booking_arr[$booking_date][$section_id][$booking['uid']]['waiting'];

                            Tad_booking_data::destroy($booking_date, $section_id, $booking_id);
                            Tad_booking_week::destroy($section_id, $booking_id, $booking_date, $booking_date);
                            Tad_booking::destroy($booking_id, $booking_date);

                            if (count($booking_arr) > 1) {
                                foreach ($booking_arr[$booking_date][$section_id] as $uid => $booking) {
                                    if ($booking['waiting'] > $del_waiting) {
                                        $new_waiting = $booking['waiting'] - 1;
                                        Tad_booking_data::update([$booking_date, $section_id, $booking_id], ['waiting' => $new_waiting]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// 審核通過
function batch_update_booking_status($batch_booking_arr, $status)
{
    foreach ($batch_booking_arr as $item_id => $item_booking) {
        if (in_array($item_id, $_SESSION['can_approve']) || $_SESSION['tad_booking_adm']) {
            $item = Tad_booking_item::get(['id' => $item_id]);
            // 提取所有 section_id
            $section_id_arr = array_keys($item_booking);
            if ($section_id_arr) {
                $section_arr = Tad_booking_section::get_all(['`id` IN(' . implode(',', $section_id_arr) . ')'], [], [], [], 'id');
            } else {
                $section_arr = [];
            }

            // 提取所有 booking_id
            $booking_id_arr = array_unique(array_merge(...array_map('array_keys', $item_booking)));
            if ($booking_id_arr) {
                $all_booking = Tad_booking::get_all(['`id` IN(' . implode(',', $booking_id_arr) . ')'], [], [], [], 'id');
            } else {
                $all_booking = [];
            }

            $email_arr   = $email_content_arr   = [];
            $status_text = $status ? _MD_TADBOOKING_APPROVE . _MD_TADBOOKING_PASS : _MD_TADBOOKING_APPROVE . _MD_TADBOOKING_DENY;

            foreach ($item_booking as $section_id => $section_booking) {
                $section = $section_arr[$section_id];
                foreach ($section_booking as $booking_id => $dates) {
                    $booking = $all_booking[$booking_id];
                    foreach ($dates as $booking_date) {
                        Tad_booking_data::update(['booking_id' => $booking_id, 'booking_date' => $booking_date, 'section_id' => $section_id], ['status' => (int) $status, 'approver' => $_SESSION['now_user']['uid'], 'pass_date' => date('Y-m-d')]);
                        $email_arr[$booking['info']['email']]           = $booking['info']['name'];
                        $email_content_arr[$booking['info']['email']][] = sprintf(_MD_TADBOOKING_BATCH_APPROVE_ITEM, $booking['booking_time'], $item['title'], $booking_date, $section['title'], $status_text);
                    }
                }
            }
        }
    }

    foreach ($email_arr as $email => $name) {
        $mail_title   = sprintf(_MD_TADBOOKING_BATCH_APPROVE_TITLE, $name, $item['title']);
        $mail_content = sprintf(_MD_TADBOOKING_BATCH_APPROVE_CONTENT, $item['title'], implode('', $email_content_arr[$email]), $item_id);
        if ($email) {
            Tools::send_now($$email, $mail_title, $mail_content);
        }
        foreach ($item['info'] as $approval) {
            if ($approval['email']) {
                Tools::send_now($approval['email'], $mail_title, $mail_content);
            }
        }
    }
}
