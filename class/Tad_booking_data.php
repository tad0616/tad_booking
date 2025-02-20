<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;

class Tad_booking_data
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int'     => ['booking_id', 'item_id', 'section_id', 'waiting', 'approver'], //數字類的欄位
        'html'    => [],                                                             //含網頁語法的欄位（所見即所得的內容）
        'text'    => [],                                                             //純大量文字欄位
        'json'    => [],                                                             //內容為 json 格式的欄位
        'pass'    => [],                                                             //不予過濾的欄位
        'explode' => [],                                                             //用分號隔開的欄位
    ];

    //取得 Tad_booking_data::get_all 所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql   = Tools::get_and_where($where_arr);
        $view_col  = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order     = $amount ? '' : $order_sql;

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_data") . "` WHERE 1 {$and_sql} {$order}";

        // Utility::getPageBar($原sql語法, 每頁顯示幾筆資料, 最多顯示幾個頁數選項);
        if ($amount) {
            $PageBar = Utility::getPageBar($sql, $amount, 10, '', '', $_SESSION['bootstrap'], 'g2p', $order_sql);
            $bar     = $PageBar['bar'];
            $sql     = $PageBar['sql'];
            $total   = $PageBar['total'];
        }

        $result   = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data_arr = [];
        $i        = 0;
        while ($data = $xoopsDB->fetchArray($result)) {

            //將 uid 編號轉換成使用者姓名（或帳號）

            $data = Tools::filter_all_data($filter, $data, self::$filter_arr);

            foreach (self::$filter_arr['explode'] as $item) {
                $data[$item . '_arr'] = explode(',', $data[$item]);
            }

            if (in_array('week', $other_arr) || in_array('all', $other_arr)) {
                $data['week'] = date('w', strtotime($data['booking_date']));
            }
            $new_key            = $key_name ? $data[$key_name] : $i;
            $data_arr[$new_key] = $get_value ? $data[$get_value] : $data;
            $i++;
        }

        if (in_array('who', $other_arr) || in_array('all', $other_arr)) {
            $booking_id_arr = array_column($data_arr, 'booking_id');
            if ($booking_id_arr) {
                $booking_ids = implode(',', $booking_id_arr);
                $booking     = Tad_booking::get_all(["`id` IN($booking_ids)"], [], [], [], 'id');
                foreach ($data_arr as $key => $value) {
                    $data_arr[$key]['who'] = $booking[$value['booking_id']];
                }
            }
        }

        if ($amount) {
            return [$data_arr, $total, $bar];
        } else {
            return $data_arr;
        }
    }

    //以流水號取得某筆 Tad_booking_data::get 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql    = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_data") . "` WHERE 1 $and_sql";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data   = $xoopsDB->fetchArray($result);
        $data   = Tools::filter_all_data($filter, $data, self::$filter_arr);

        // if (in_array('xxx', $other_arr) || in_array('all', $other_arr)) {
        //     $data['xxx'] = ooo::get_all();
        // }

        foreach (self::$filter_arr['explode'] as $item) {
            $data[$item . '_arr'] = explode(',', $data[$item]);
        }

        if ($only_key) {
            return $data[$only_key];
        } else {
            return $data;
        }
    }

    //新增資料到 tad_booking_data Tad_booking_data::store()
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

        //取得使用者編號
        $approver = ($xoopsUser) ? $xoopsUser->uid() : 0;
        if ($status == 1) {
            $pass_date = date("Y-m-d H:i:s", xoops_getUserTimestamp(time()));
        }

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_data") . "` (
            `booking_id`,
            `booking_date`,
            `item_id`,
            `section_id`,
            `waiting`,
            `status`,
            `approver`,
            `pass_date`
        ) VALUES(
            '{$booking_id}',
            '{$booking_date}',
            '{$item_id}',
            '{$section_id}',
            '{$waiting}',
            '{$status}',
            '{$approver}',
            '{$pass_date}'
        )";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        //取得最後新增資料的流水編號

    }

    //更新 tad_booking_data 某一筆資料 Tad_booking_data::update()
    public static function update($where_arr = [], $data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        $and = Tools::get_and_where($where_arr);

        if (! empty($data_arr)) {
            $col_arr = [];

            foreach ($data_arr as $key => $value) {
                $value     = Tools::filter($key, $value, 'write', self::$filter_arr);
                $col_arr[] = "`$key` = '{$value}'";
            }
            $update_cols = implode(', ', $col_arr);
            $sql         = "UPDATE `" . $xoopsDB->prefix("tad_booking_data") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);

            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }

            //取得使用者編號
            $approver  = ($xoopsUser) ? $xoopsUser->uid() : 0;
            $pass_date = date("Y-m-d H:i:s", xoops_getUserTimestamp(time()));

            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_data") . "` SET
            `booking_id` = '{$booking_id}',
            `booking_date` = '{$booking_date}',
            `item_id` = '{$item_id}',
            `section_id` = '{$section_id}',
            `waiting` = '{$waiting}',
            `status` = '{$status}',
            `approver` = '{$approver}',
            `pass_date` = '{$pass_date}'
            WHERE 1 $and";
        }
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
    }

    //刪除 Tad_booking_data::destroy 某筆資料資料
    public static function destroy($booking_date = '', $section_id = '', $booking_id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        if (empty($booking_date) and empty($booking_id) and empty($section_id)) {
            return;
        }

        $and = '';
        if ($booking_date) {
            $and .= "and `booking_date` = '$booking_date'";
        }
        if ($booking_id) {
            $and .= "and `booking_id` = '$booking_id'";
        }
        if ($section_id) {
            $and .= "and `section_id` = '$section_id'";
        }

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_data") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
    }

    //自動取得 Tad_booking_data::max_waiting 的最新排序
    public static function max_waiting($section_id = '', $booking_date = '')
    {
        global $xoopsDB;
        $sql           = "SELECT MAX(`waiting`) FROM `" . $xoopsDB->prefix("tad_booking_data") . "` WHERE `section_id` = '{$section_id}' AND `booking_date` = '{$booking_date}'";
        $result        = $xoopsDB->query($sql) or Utility::web_error($sql);
        list($waiting) = $xoopsDB->fetchRow($result);
        return ++$waiting;
    }

    //
    public static function count($booking_id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        if (empty($booking_id)) {
            return;
        }

        $sql = "SELECT COUNT(*) FROM `" . $xoopsDB->prefix("tad_booking_data") . "`
        WHERE `booking_id`='{$booking_id}'";
        $result      = $xoopsDB->queryF($sql) or Utility::web_error($sql);
        list($count) = $xoopsDB->fetchRow($result);
        return $count;
    }
}
