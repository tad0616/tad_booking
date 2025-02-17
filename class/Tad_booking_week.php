<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;

class Tad_booking_week
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int'     => ['booking_id', 'week', 'section_id'], //數字類的欄位
        'html'    => [],                                   //含網頁語法的欄位（所見即所得的內容）
        'text'    => [],                                   //純大量文字欄位
        'json'    => [],                                   //內容為 json 格式的欄位
        'pass'    => [],                                   //不予過濾的欄位
        'explode' => [],                                   //用分號隔開的欄位
    ];

    //新增資料到 Tad_booking_week::store()
    public static function store($data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        //XOOPS表單安全檢查
        if (empty($data_arr)) {
            Utility::xoops_security_check();
            $data_arr = $_POST;
        }

        foreach ($data_arr as $key => $value) {
            $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
        }

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_week") . "` (
            `booking_id`,
            `week`,
            `section_id`,
            `start_date`,
            `end_date`
        ) VALUES(
            '{$booking_id}',
            '{$week}',
            '{$section_id}',
            '{$start_date}',
            '{$end_date}'
        )";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        //取得最後新增資料的流水編號

    }
    //刪除 Tad_booking_week::destroy() 某筆資料資料
    public static function destroy($section_id = '', $booking_id = '', $start_date = '', $end_date = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        if (empty($booking_id) and empty($section_id)) {
            return;
        }

        $and = '';

        if ($booking_id) {
            $and .= "and `booking_id` = '$booking_id'";
        }
        if ($section_id) {
            $and .= "and `section_id` = '$section_id'";
        }
        if ($start_date) {
            $and .= "and `start_date` = '$start_date'";
        }
        if ($end_date) {
            $and .= "and `end_date` = '$end_date'";
        }

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_week") . "` WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

    }
}
