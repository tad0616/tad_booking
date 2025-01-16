<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;
use XoopsModules\Tadtools\FormValidator;



class Tad_booking_item
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int' => ['id','cate_id','sort'],   //數字類的欄位
        'html' => [], //含網頁語法的欄位（所見即所得的內容）
        'text' => ['desc'], //純大量文字欄位
        'json' => ['info'], //內容為 json 格式的欄位
        'pass' => [], //不予過濾的欄位
        'explode' => ['approval'],   //用分號隔開的欄位
    ];

    //列出所有 tad_booking_item 資料
    public static function index($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $amount = '')
    {
        global $xoopsTpl;

        if ($amount) {
            list($all_tad_booking_item, $total, $bar) = self::get_all($where_arr, $other_arr, $view_cols, $order_arr, null, null, 'read', $amount);
            $xoopsTpl->assign('bar', $bar);
            $xoopsTpl->assign('total', $total);
        } else {
            $all_tad_booking_item = self::get_all($where_arr, $other_arr, $view_cols, $order_arr);
        }

        $xoopsTpl->assign('all_tad_booking_item', $all_tad_booking_item);
        Utility::test($all_tad_booking_item, 'all_tad_booking_item');

        //刪除確認的JS
        $SweetAlert   = new SweetAlert();
        $SweetAlert->render('tad_booking_item_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_item_destroy&id=", "id");

        
        Utility::get_jquery(true);
    }


    //取得tad_booking_item所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql = Tools::get_and_where($where_arr);
        $view_col = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order = $amount ? '' : $order_sql;

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_item") . "` WHERE 1 {$and_sql} {$order}";

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


    //以流水號秀出某筆 tad_booking_item 資料內容 Tad_booking_item::show()
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

        

        $SweetAlert   = new SweetAlert();
        $SweetAlert->render('tad_booking_item_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_item_destroy&id=", "id");

        if ($mode == "return") {
            return $all;
        } elseif ($mode == "assign_all") {
            $xoopsTpl->assign('tad_booking_item', $all);
        } else {
            foreach ($all as $key => $value) {
                $xoopsTpl->assign($key, $value);
            }
        }
        $xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
    }


    //以流水號取得某筆 tad_booking_item 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_item") . "` WHERE 1 $and_sql";
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


    //tad_booking_item 編輯表單
    public static function create($id = '' )
    {
        global $xoopsTpl;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        //抓取預設值
        $tad_booking_item = (!empty($id)) ? self::get(['id' =>$id]) : [];

        //預設值設定
        
        $def['id'] = $id;
        $def['sort'] = self::max_sort();
        $def['start'] = date("Y-m-d");
        $def['end'] = date("Y-m-d");
        $def['enable'] = '1';

        if (empty($tad_booking_item)) {
            $tad_booking_item = $def;
        }

        foreach ($tad_booking_item as $key => $value) {
            $value = Tools::filter($key, $value, 'edit', self::$filter_arr);
            $$key = isset($tad_booking_item[$key]) ? $tad_booking_item[$key] : $def[$key];
            $xoopsTpl->assign($key, $value);
        }

        $op = (!empty($id)) ? "tad_booking_item_update" : "tad_booking_item_store";
        $xoopsTpl->assign('next_op', $op);

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        
        //類別編號
        $sql = "SELECT `id`, `title` FROM `".$xoopsDB->prefix("tad_booking_item")."` ORDER BY sort";
        $result = Utility::query($sql);
        $i=0;
        $id_options_array = [];
        while(list($id,$title) = $xoopsDB->fetchRow($result)){
            $id_options_array[$i]['id']=$id;
            $id_options_array[$i]['title']=$title;
            $i++;
        }
        $xoopsTpl->assign("id_options", $id_options_array);
    
    
        //加入Token安全機制
        Utility::token_form();
    }


    //新增資料到 tad_booking_item Tad_booking_item::store()
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

        

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_item") . "` (
            `cate_id`, 
            `title`, 
            `desc`, 
            `sort`, 
            `start`, 
            `end`, 
            `enable`, 
            `approval`, 
            `info`
        ) VALUES(
            '{$cate_id}', 
            '{$title}', 
            '{$desc}', 
            '{$sort}', 
            '{$start}', 
            '{$end}', 
            '{$enable}', 
            '{$approval}', 
            '{$info}'
        )";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        //取得最後新增資料的流水編號
        $id = $xoopsDB->getInsertId();
        
        return $id;
    }


    //更新 tad_booking_item 某一筆資料 Tad_booking_item::update()
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
            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_item") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);

            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }
            

            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_item") . "` SET 
            `cate_id` = '{$cate_id}', 
            `title` = '{$title}', 
            `desc` = '{$desc}', 
            `sort` = '{$sort}', 
            `start` = '{$start}', 
            `end` = '{$end}', 
            `enable` = '{$enable}', 
            `approval` = '{$approval}', 
            `info` = '{$info}'
            WHERE 1 $and";
        }
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        return $where_arr['id'];
    }


    //刪除tad_booking_item某筆資料資料
    public static function destroy($id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        if(empty($id)) {
            return;
        }

        $and = '';
        if($id){
        $and .= "and `id` = '$id'";
    }
    

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_item") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);
        
    }




    //自動取得tad_booking_item的最新排序
    public static function max_sort()
    {
        global $xoopsDB;
        $sql = "select max(`sort`) from `" . $xoopsDB->prefix("tad_booking_item") . "`";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        list($sort) = $xoopsDB->fetchRow($result);
        return ++$sort;
    }

}
