<?php
use Xmf\Request;
use XoopsModules\Tad_booking\Tad_booking;
use XoopsModules\Tad_booking\Tad_booking_data;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tad_booking_section;
use XoopsModules\Tad_booking\Tad_booking_week;
use XoopsModules\Tad_booking\Tools;

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

header('HTTP/1.1 200 OK');
$xoopsLogger->activated = false;

$op           = Request::getString('op');
$id           = Request::getInt('id');
$files_sn     = Request::getInt('files_sn');
$item_id      = Request::getInt('item_id');
$week         = Request::getInt('week');
$pk           = Request::getInt('pk');
$value        = Request::getString('value');
$booking_date = Request::getString('booking_date');
$booking_id   = Request::getInt('booking_id');
$section_id   = Request::getInt('section_id');
$uid          = Request::getInt('uid');
$status       = Request::getInt('status');

if ($_SESSION['can_booking'] || $_SESSION['can_approve'] || $_SESSION['tad_booking_adm']) {
    switch ($op) {

        case "single_insert_booking":
            single_insert_booking($booking_date, $section_id, $week, $item_id);
            exit;

        case "delete_booking":
            delete_booking($section_id, $booking_date, $booking_id, $uid);
            header("location: {$_SERVER['HTTP_REFERER']}");
            exit;

        case "update_booking_status":
            update_booking_status($item_id, $booking_date, $booking_id, $section_id, $status);
            exit;

        case "tad_booking_update_content":
            $where_arr['id'] = $pk;
            Tad_booking::update($where_arr, ['content' => $value]);
            die($value);

        case "tad_booking_section_update_title":
            $where_arr['id'] = $pk;
            Tad_booking_section::update($where_arr, ['title' => $value]);
            die($value);

        case 'change_section_enable':
            $new_pic = change_section_enable($section_id, $week);
            die($new_pic);

        case 'section_sort_save':
            $sort = 1;
            foreach ($_POST['tr'] as $id) {
                Tad_booking_section::update(['id' => $id], ['sort' => $sort]);
                $sort++;
            }
            die(_TAD_SORTED . "(" . date("Y-m-d H:i:s") . ")");

    }
}

// 審核通過
function update_booking_status($item_id, $booking_date, $booking_id, $section_id, $status)
{

    $item    = Tad_booking_item::get(['id' => $item_id]);
    $section = Tad_booking_section::get(['id' => $section_id]);
    $booking = Tad_booking::get(['id' => $booking_id]);
    if (in_array($item_id, $_SESSION['can_approve'])) {
        Tad_booking_data::update(['booking_id' => $booking_id, 'booking_date' => $booking_date, 'section_id' => $section_id], ['status' => $status, 'approver' => $_SESSION['now_user']['uid'], 'pass_date' => date('Y-m-d')]);

        $status_text  = $status ? _MD_TADBOOKING_APPROVE . _MD_TADBOOKING_PASS : _MD_TADBOOKING_APPROVE . _MD_TADBOOKING_DENY;
        $mail_title   = sprintf(_MD_TADBOOKING_APPROVE_TITLE, $booking['info']['name'], $item['title']);
        $mail_content = sprintf(_MD_TADBOOKING_APPROVE_CONTENT, $booking['info']['name'], $booking['booking_time'], $item['title'], $item['title'], $booking_date, $section['title'], $status_text, $item_id);
        if ($booking['info']['email']) {
            Tools::send_now($booking['info']['email'], $mail_title, $mail_content);
        }

        foreach ($item['info'] as $key => $approval) {
            if ($approval['email']) {
                Tools::send_now($approval['email'], $mail_title, $mail_content);
            }
        }

    }

    die('1');
}

function single_insert_booking($booking_date, $section_id, $week, $item_id)
{
    $booking_id = Tad_booking::store(['start_date' => $booking_date, 'end_date' => $booking_date, 'content' => _MD_TADBOOKING_PERSONAL_BOOKING);

    Tad_booking_week::store(['booking_id' => $booking_id, 'section_id' => $section_id, 'week' => $week, 'start_date' => $booking_date, 'end_date' => $booking_date]);
    $item      = Tad_booking_item::get(['id' => $item_id]);
    $status    = empty($item['approval']) ? 1 : 0;
    $pass_date = empty($item['approval']) ? date('Y-m-d') : '0000-00-00';
    $waiting   = Tad_booking_data::max_waiting($section_id, $booking_date);
    Tad_booking_data::store(['booking_id' => $booking_id, 'booking_date' => $booking_date, 'section_id' => $section_id, 'waiting' => $waiting, 'status' => $status, 'pass_date' => $pass_date]);
    // 找出這段期間的所有預約
    list($booking_arr, $first_booking) = Tools::booking_arr($booking_date, $booking_date, $section_id);

    $icon = Tools::delete_booking_icon($item_id, $booking_date, $section_id, $booking_arr) . "<div style='font-size:0.9rem'><a href='#' class='editable' data-name='content' data-type='text' data-pk='$booking_id' data-params=\"{op: 'tad_booking_update_content'}\">"._MD_TADBOOKING_PERSONAL_BOOKING."</a></div>";
    die($icon);
}

//改變啟用狀態
function change_section_enable($section_id = "", $week = "")
{
    $section = Tad_booking_section::get(['id' => $section_id], ['week_arr']);
    if (in_array($week, $section['week_arr'])) {
        $new_week = implode(',', array_values(array_diff($section['week_arr'], [$week])));
        $new_pic  = "images/no.gif";
    } else {
        $section['week_arr'][] = $week;
        sort($section['week_arr']);
        $new_week = implode(',', $section['week_arr']);
        $new_pic  = "images/yes.gif";
    }

    Tad_booking_section::update(['id' => $section_id], ['week' => $new_week]);

    return $new_pic;
}

function delete_booking($section_id, $booking_date, $booking_id, $uid)
{
    if ($section_id && $booking_date) {
        //取得用了該日期時段的uid
        list($booking_arr, $first_booking) = Tools::booking_arr($booking_date, $booking_date, $section_id);
        $del_waiting                       = $booking_arr[$booking_date][$section_id][$uid]['waiting'];

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
