<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;

class Tad_booking_section
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int' => ['id', 'item_id', 'sort'], //數字類的欄位
        'html' => [], //含網頁語法的欄位（所見即所得的內容）
        'text' => [], //純大量文字欄位
        'json' => [], //內容為 json 格式的欄位
        'pass' => [], //不予過濾的欄位
        'explode' => [], //用分號隔開的欄位
    ];

    public static $chinese_week = ['日', '一', '二', '三', '四', '五', '六'];

    //取得tad_booking_section所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql   = Tools::get_and_where($where_arr);
        $view_col  = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order     = $amount ? '' : $order_sql;

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_section") . "` WHERE 1 {$and_sql} {$order}";

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

            $data = Tools::filter_all_data($filter, $data, self::$filter_arr);

            foreach (self::$filter_arr['explode'] as $item) {
                $data[$item . '_arr'] = explode(',', $data[$item]);
            }

            if (in_array('week_arr', $other_arr) || in_array('all', $other_arr)) {
                $data['week_arr'] = explode(',', $data['week']);
            }

            $new_key            = $key_name ? $data[$key_name] : $i;
            $data_arr[$new_key] = $get_value ? $data[$get_value] : $data;
            $i++;
        }

        if ($amount) {
            return [$data_arr, $total, $bar];
        } else {
            return $data_arr;
        }
    }

    //以流水號取得某筆 tad_booking_section 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql    = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_section") . "` WHERE 1 $and_sql";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data   = $xoopsDB->fetchArray($result);
        $data   = Tools::filter_all_data($filter, $data, self::$filter_arr);

        if (in_array('week_arr', $other_arr) || in_array('all', $other_arr)) {
            $data['week_arr'] = explode(',', $data['week']);
        }

        foreach (self::$filter_arr['explode'] as $item) {
            $data[$item . '_arr'] = explode(',', $data[$item]);
        }

        if ($only_key) {
            return $data[$only_key];
        } else {
            return $data;
        }
    }

    //新增資料到 tad_booking_section Tad_booking_section::store()
    public static function store($data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        //XOOPS表單安全檢查
        if (empty($data_arr)) {
            Utility::xoops_security_check();
            $data_arr = $_POST;
            $week     = implode(',', $_POST['weeks']);
        }

        foreach ($data_arr as $key => $value) {
            $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
        }

        $sort = self::max_sort(['item_id' => $item_id]);

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_section") . "` (
            `item_id`,
            `title`,
            `sort`,
            `week`
        ) VALUES(
            '{$item_id}',
            '{$title}',
            '{$sort}',
            '{$week}'
        )";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        //取得最後新增資料的流水編號
        $id = $xoopsDB->getInsertId();
        unset($_SESSION['tad_booking_cate_arr']);
        return $id;
    }

    //更新 tad_booking_section 某一筆資料 Tad_booking_section::update()
    public static function update($where_arr = [], $data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        $and = Tools::get_and_where($where_arr);

        if (!empty($data_arr)) {
            $col_arr = [];

            foreach ($data_arr as $key => $value) {
                $value     = Tools::filter($key, $value, 'write', self::$filter_arr);
                $col_arr[] = "`$key` = '{$value}'";
            }
            $update_cols = implode(', ', $col_arr);
            $sql         = "UPDATE `" . $xoopsDB->prefix("tad_booking_section") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);

            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }

            $week = implode(',', $tad_booking_section['week']);

            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_section") . "` SET
            `item_id` = '{$item_id}',
            `title` = '{$title}',
            `sort` = '{$sort}',
            `week` = '{$week}'
            WHERE 1 $and";
        }
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
        unset($_SESSION['tad_booking_cate_arr']);
        return $where_arr['id'];
    }

    //新增資料到 tad_booking_section Tad_booking_section::store()
    public static function count_arr()
    {

        global $xoopsDB;
        $sql    = "SELECT `item_id`,COUNT(`id`) FROM `" . $xoopsDB->prefix("tad_booking_section") . "` GROUP BY `item_id`";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        while (list($item_id, $count) = $xoopsDB->fetchRow($result)) {
            $count_arr[$item_id] = $count;
        }

        return $count_arr;
    }

    //刪除tad_booking_section某筆資料資料
    public static function destroy($id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        if (empty($id)) {
            return;
        }

        $and = '';
        if ($id) {
            $and .= "and `id` = '$id'";
        }

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_section") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
        unset($_SESSION['tad_booking_cate_arr']);
    }

    //自動取得tad_booking_section的最新排序
    public static function max_sort($where_arr = [])
    {
        global $xoopsDB;

        $and_sql    = Tools::get_and_where($where_arr);
        $sql        = "select max(`sort`) from `" . $xoopsDB->prefix("tad_booking_section") . "` WHERE 1  $and_sql";
        $result     = $xoopsDB->query($sql) or Utility::web_error($sql);
        list($sort) = $xoopsDB->fetchRow($result);
        return ++$sort;
    }
}
