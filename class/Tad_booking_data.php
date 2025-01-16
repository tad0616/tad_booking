<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;
use XoopsModules\Tadtools\FormValidator;



class Tad_booking_data
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int' => ['booking_id','section_id','waiting','approver'],   //數字類的欄位
        'html' => [], //含網頁語法的欄位（所見即所得的內容）
        'text' => [], //純大量文字欄位
        'json' => [], //內容為 json 格式的欄位
        'pass' => [], //不予過濾的欄位
        'explode' => [],   //用分號隔開的欄位
    ];

    //列出所有 tad_booking_data 資料
    public static function index($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $amount = '')
    {
        global $xoopsTpl;

        if ($amount) {
            list($all_tad_booking_data, $total, $bar) = self::get_all($where_arr, $other_arr, $view_cols, $order_arr, null, null, 'read', $amount);
            $xoopsTpl->assign('bar', $bar);
            $xoopsTpl->assign('total', $total);
        } else {
            $all_tad_booking_data = self::get_all($where_arr, $other_arr, $view_cols, $order_arr);
        }

        $xoopsTpl->assign('all_tad_booking_data', $all_tad_booking_data);
        Utility::test($all_tad_booking_data, 'all_tad_booking_data');

        //刪除確認的JS
        $SweetAlert   = new SweetAlert();
        $SweetAlert->render('tad_booking_data_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_data_destroy&booking_date={$booking_date}&booking_id={$booking_id}&section_id=", "section_id");

        
    }


    //取得tad_booking_data所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql = Tools::get_and_where($where_arr);
        $view_col = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order = $amount ? '' : $order_sql;

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_data") . "` WHERE 1 {$and_sql} {$order}";

        // Utility::getPageBar($原sql語法, 每頁顯示幾筆資料, 最多顯示幾個頁數選項);
        if ($amount) {
            $PageBar = Utility::getPageBar($sql, $amount, 10, '', '', $_SESSION['bootstrap'], 'none', $order_sql);
            $bar = $PageBar['bar'];
            $sql = $PageBar['sql'];
            $total = $PageBar['total'];
        }

        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data_arr = [];
        $i = 0;
        while ($data = $xoopsDB->fetchArray($result)) {
            
            //將 uid 編號轉換成使用者姓名（或帳號）
            $data['approver_name'] = Utility::get_name_by_uid($data['approver']);

            $data = Tools::filter_all_data($filter, $data, self::$filter_arr);

            foreach (self::$filter_arr['explode'] as $item) {
                $data[$item . '_arr'] = explode(';', $data[$item]);
            }

            // if (in_array('xxx', $other_arr) || in_array('all', $other_arr)) {
            //     $data['xxx'] = ooo::get_all();
            // }

            $new_key = $key_name ? $data[$key_name] : $i;
            $data_arr[$new_key] = $get_value ? $data[$get_value] : $data;
            $i++;
        }

        if ($amount) {
            return [$data_arr, $total, $bar];
        }else{
            return $data_arr;
        }
    }


    //以流水號秀出某筆 tad_booking_data 資料內容 Tad_booking_data::show()
    public static function show($where_arr = [], $other_arr = [], $mode = '')
    {
        global $xoopsTpl, $xoTheme;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $all = self::get($where_arr, $other_arr);
        if (empty($all)) {
            return false;
        }

        foreach ($all as $key => $value) {
            $value = Tools::filter($key, $value, 'read', self::$filter_arr);
            $all[$key] = $value;
            $$key = $value;
        }

        
        //將 uid 編號轉換成使用者姓名（或帳號）
        $approver_name = Utility::get_name_by_uid($approver);
        $xoopsTpl->assign('approver_name', $approver_name);
    

        $SweetAlert   = new SweetAlert();
        $SweetAlert->render('tad_booking_data_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_data_destroy&booking_date={$booking_date}&booking_id={$booking_id}&section_id=", "section_id");

        if ($mode == "return") {
            return $all;
        } elseif ($mode == "assign_all") {
            $xoopsTpl->assign('tad_booking_data', $all);
        } else {
            foreach ($all as $key => $value) {
                $xoopsTpl->assign($key, $value);
            }
        }
        $xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
    }


    //以流水號取得某筆 tad_booking_data 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_data") . "` WHERE 1 $and_sql";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data = $xoopsDB->fetchArray($result);
        $data = Tools::filter_all_data($filter, $data, self::$filter_arr);

        // if (in_array('xxx', $other_arr) || in_array('all', $other_arr)) {
        //     $data['xxx'] = ooo::get_all();
        // }

        foreach (self::$filter_arr['explode'] as $item) {
            $data[$item . '_arr'] = explode(';', $data[$item]);
        }

        if ($only_key) {
            return $data[$only_key];
        } else {
            return $data;
        }
    }


    //tad_booking_data 編輯表單
    public static function create( )
    {
        global $xoopsTpl;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        //抓取預設值
        $tad_booking_data = (!empty($booking_date) and !empty($booking_id) and !empty($section_id)) ? self::get(['booking_date' = >$booking_date, 'booking_id' = >$booking_id, 'section_id' = >$section_id]) : [];

        //預設值設定
        
        $def['booking_id'] = $booking_id;
        $def['booking_date'] = $booking_date;
        $def['section_id'] = $section_id;
        $user_uid = $xoopsUser ? $xoopsUser->uid() : "";
        $def['approver'] = $user_uid;
        $def['pass_date'] = date("Y-m-d H:i:s");

        if (empty($tad_booking_data)) {
            $tad_booking_data = $def;
        }

        foreach ($tad_booking_data as $key => $value) {
            $value = Tools::filter($key, $value, 'edit', self::$filter_arr);
            $$key = isset($tad_booking_data[$key]) ? $tad_booking_data[$key] : $def[$key];
            $xoopsTpl->assign($key, $value);
        }

        $op = (!empty($booking_date) and !empty($booking_id) and !empty($section_id)) ? "tad_booking_data_update" : "tad_booking_data_store";
        $xoopsTpl->assign('next_op', $op);

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        
    
        //加入Token安全機制
        Utility::token_form();
    }


    //新增資料到 tad_booking_data Tad_booking_data::store()
    public static function store($data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

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
            $pass_date = date("Y-m-d H:i:s",xoops_getUserTimestamp(time()));

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_data") . "` (
            `booking_id`, 
            `booking_date`, 
            `section_id`, 
            `waiting`, 
            `status`, 
            `approver`, 
            `pass_date`
        ) VALUES(
            '{$booking_id}', 
            '{$booking_date}', 
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
    public static function update($where_arr=[], $data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        $and = Tools::get_and_where($where_arr);

        if (!empty($data_arr)) {
            $col_arr = [];

            foreach ($data_arr as $key => $value) {
                $value = Tools::filter($key, $value, 'write', self::$filter_arr);
                $col_arr[] = "`$key` = '{$value}'";
            }
            $update_cols = implode(', ', $col_arr);
            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_data") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);

            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }
            
            //取得使用者編號
            $approver = ($xoopsUser) ? $xoopsUser->uid() : 0;
            $pass_date = date("Y-m-d H:i:s",xoops_getUserTimestamp(time()));

            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_data") . "` SET 
            `booking_id` = '{$booking_id}', 
            `booking_date` = '{$booking_date}', 
            `section_id` = '{$section_id}', 
            `waiting` = '{$waiting}', 
            `status` = '{$status}', 
            `approver` = '{$approver}', 
            `pass_date` = '{$pass_date}'
            WHERE 1 $and";
        }
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        
    }


    //刪除tad_booking_data某筆資料資料
    public static function destroy($booking_date = '', $booking_id = '', $section_id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        if(empty($booking_date) and empty($booking_id) and empty($section_id)) {
            return;
        }

        $and = '';
        if($booking_date){
            $and .= "and `booking_date` = '$booking_date'";
        }
        if($booking_id){
            $and .= "and `booking_id` = '$booking_id'";
        }
        if($section_id){
            $and .= "and `section_id` = '$section_id'";
        }
        

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_data") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
        
    }





}
