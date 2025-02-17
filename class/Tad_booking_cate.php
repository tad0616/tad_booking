<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\CategoryHelper;
use XoopsModules\Tadtools\EasyResponsiveTabs;
use XoopsModules\Tadtools\FormValidator;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\Ztree;
use XoopsModules\Tad_booking\Tools;

class Tad_booking_cate
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int'     => ['id', 'sort'], //數字類的欄位
        'html'    => [],             //含網頁語法的欄位（所見即所得的內容）
        'text'    => [],             //純大量文字欄位
        'json'    => [],             //內容為 json 格式的欄位
        'pass'    => [],             //不予過濾的欄位
        'explode' => [],             //用分號隔開的欄位
    ];

    //列出所有 tad_booking_cate 資料
    public static function index($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $amount = '')
    {
        global $xoopsTpl, $xoTheme;

        if ($amount) {
            list($all_tad_booking_cate, $total, $bar) = self::get_all($where_arr, $other_arr, $view_cols, $order_arr, null, null, 'read', $amount);
            $xoopsTpl->assign('bar', $bar);
            $xoopsTpl->assign('total', $total);
        } else {
            $all_tad_booking_cate = self::get_all($where_arr, $other_arr, $view_cols, $order_arr);
        }

        $xoopsTpl->assign('all_tad_booking_cate', $all_tad_booking_cate);
        Utility::test($all_tad_booking_cate, 'all_tad_booking_cate');

        //刪除確認的JS
        $SweetAlert = new SweetAlert();
        $SweetAlert->render('tad_booking_cate_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_cate_destroy&id=", "id");

        Utility::get_jquery(true);
        $xoTheme->addStylesheet('modules/tadtools/css/vtb.css');

        $EasyResponsiveTabs = new EasyResponsiveTabs('#cateTab');
        $EasyResponsiveTabs->render();
    }

    //取得tad_booking_cate所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql   = Tools::get_and_where($where_arr);
        $view_col  = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order     = $amount ? '' : $order_sql;

        $today = date("Y-m-d");

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_cate") . "` WHERE 1 {$and_sql} {$order}";

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

            if (in_array('items', $other_arr) || in_array('all', $other_arr)) {
                $data['items'] = Tad_booking_item::get_all(['cate_id' => $data['id'], 'enable' => 1, "`start` <= '$today'", "(`end` >= '$today' OR `end` = '0000-00-00')"], [], ['id', 'title', 'approval', 'info'], ['sort' => 'ASC']);
            }

            if (in_array('approval_items', $other_arr) || in_array('all', $other_arr)) {
                // 勿改為 $data['approval_items']，如此選單才能共用
                $cate_where_arr['cate_id'] = $data['id'];
                $cate_where_arr['enable']  = 1;
                $cate_where_arr[]          = 'approval!=""';
                $cate_where_arr[]          = "`start` <= '$today'";
                $cate_where_arr[]          = "(`end` >= '$today' OR `end` = '0000-00-00')";
                if ($_SESSION['can_approve'] && ! $_SESSION['tad_booking_adm']) {
                    $cate_where_arr[] = 'id IN(' . implode(',', $_SESSION['can_approve']) . ')';
                }
                $data['items'] = Tad_booking_item::get_all($cate_where_arr, [], ['id', 'title', 'approval', 'info'], ['sort' => 'ASC'], 'id');

            }

            if (in_array('item_arr', $other_arr) || in_array('all', $other_arr)) {
                $all_item = Tad_booking_item::get_all(['cate_id' => $data['id']], [], [], ['sort' => 'ASC'], 'id');
                $item_arr = Tad_booking_section::count_arr();
                foreach ($all_item as $id => $item) {
                    $data['item_arr'][$id]['count'] = $item_arr[$id];
                    $data['item_arr'][$id]['item']  = $item;

                }
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

    //以流水號秀出某筆 tad_booking_cate 資料內容 Tad_booking_cate::show()
    public static function show($where_arr = [], $other_arr = [], $mode = '')
    {
        global $xoopsTpl;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $all = self::get($where_arr, $other_arr);
        if (empty($all)) {
            return false;
        }

        foreach ($all as $key => $value) {
            $value     = Tools::filter($key, $value, 'read', self::$filter_arr);
            $all[$key] = $value;
            $$key      = $value;
        }

        $SweetAlert = new SweetAlert();
        $SweetAlert->render('tad_booking_cate_destroy_func', "{$_SERVER['PHP_SELF']}?op=tad_booking_cate_destroy&id=", "id");

        if ($mode == "return") {
            return $all;
        } elseif ($mode == "assign_all") {
            $xoopsTpl->assign('tad_booking_cate', $all);
        } else {
            foreach ($all as $key => $value) {
                $xoopsTpl->assign($key, $value);
            }
        }
    }

    //以流水號取得某筆 tad_booking_cate 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql    = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_cate") . "` WHERE 1 $and_sql";
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

    //tad_booking_cate 編輯表單
    public static function create($id = '')
    {
        global $xoopsTpl, $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        //抓取預設值
        $tad_booking_cate = (! empty($id)) ? self::get(['id' => $id]) : [];

        //預設值設定

        $def['id']     = $id;
        $def['sort']   = self::max_sort();
        $def['enable'] = '1';

        if (empty($tad_booking_cate)) {
            $tad_booking_cate = $def;
        }

        foreach ($tad_booking_cate as $key => $value) {
            $value = Tools::filter($key, $value, 'edit', self::$filter_arr);
            $$key  = isset($tad_booking_cate[$key]) ? $tad_booking_cate[$key] : $def[$key];
            $xoopsTpl->assign($key, $value);
        }

        $op = (! empty($id)) ? "tad_booking_cate_update" : "tad_booking_cate_store";
        $xoopsTpl->assign('next_op', $op);

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        //加入Token安全機制
        Utility::token_form();
    }

    //新增資料到 tad_booking_cate Tad_booking_cate::store()
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

        $sql = "INSERT INTO `" . $xoopsDB->prefix("tad_booking_cate") . "` (
            `title`,
            `sort`,
            `enable`
        ) VALUES(
            '{$title}',
            '{$sort}',
            '{$enable}'
        )";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        //取得最後新增資料的流水編號
        $id = $xoopsDB->getInsertId();

        return $id;
    }

    //更新 tad_booking_cate 某一筆資料 Tad_booking_cate::update()
    public static function update($where_arr = [], $data_arr = [])
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        $and = Tools::get_and_where($where_arr);

        if (! empty($data_arr)) {
            $col_arr = [];

            foreach ($data_arr as $key => $value) {
                $value     = Tools::filter($key, $value, 'write', self::$filter_arr);
                $col_arr[] = "`$key` = '{$value}'";
            }
            $update_cols = implode(', ', $col_arr);
            $sql         = "UPDATE `" . $xoopsDB->prefix("tad_booking_cate") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);

            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }

            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_cate") . "` SET
            `title` = '{$title}',
            `sort` = '{$sort}',
            `enable` = '{$enable}'
            WHERE 1 $and";
        }
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        return $where_arr['id'];
    }

    //刪除tad_booking_cate某筆資料資料
    public static function destroy($id = '')
    {
        global $xoopsDB;
        Tools::chk_is_adm('', '', __FILE__, __LINE__);

        if (empty($id)) {
            return;
        }

        $and = '';
        if ($id) {
            $and .= "and `id` = '$id'";
        }

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_cate") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

    }

    //自動取得tad_booking_cate的最新排序
    public static function max_sort()
    {
        global $xoopsDB;
        $sql        = "select max(`sort`) from `" . $xoopsDB->prefix("tad_booking_cate") . "`";
        $result     = $xoopsDB->query($sql) or Utility::web_error($sql);
        list($sort) = $xoopsDB->fetchRow($result);
        return ++$sort;
    }

    //列出所有tad_booking_cate資料
    public static function list_tree($def_id = "")
    {
        global $xoopsDB, $xoopsTpl;

        $sql        = "select count(*),id from " . $xoopsDB->prefix("tad_booking") . " group by id";
        $result     = $xoopsDB->query($sql) or Utility::web_error($sql);
        $cate_count = [];
        while (list($count, $id) = $xoopsDB->fetchRow($result)) {
            $cate_count[$id] = $count;
        }

        $categoryHelper = new CategoryHelper('tad_booking_cate', 'id', 'tad_booking_cate_parent_sn', 'title');
        $path           = $categoryHelper->getCategoryPath($def_id);
        $path_arr       = array_keys($path);
        $data[]         = "{ id:0, pId:0, name:'All', url:'main.php', target:'_self', open:true}";

        $sql    = "select id, tad_booking_cate_parent_sn, title from " . $xoopsDB->prefix("tad_booking_cate") . " ORDER BY sort";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        while (list($id, $tad_booking_cate_parent_sn, $title) = $xoopsDB->fetchRow($result)) {
            $font_style      = $def_id == $id ? ", font:{'background-color':'yellow', 'color':'black'}" : '';
            $open            = in_array($id, $path_arr) ? 'true' : 'false';
            $display_counter = empty($cate_count[$id]) ? "" : " ({$cate_count[$id]})";
            $data[]          = "{ id:{$id}, pId:{$tad_booking_cate_parent_sn}, name:'{$title}{$display_counter}', url:'main.php?id={$id}', open: {$open} ,target:'_self' {$font_style}}";
        }

        $json = implode(",\n", $data);

        $ztree      = new Ztree("cate_tree", $json, "tad_booking_cate_save_drag.php", "tad_booking_cate_save_sort.php", "tad_booking_cate_parent_sn", "id");
        $ztree_code = $ztree->render();
        $xoopsTpl->assign('ztree_code', $ztree_code);
        $xoopsTpl->assign('cate_count', $cate_count);

        return $data;
    }

    //取得分類下的文件數
    public static function get_count()
    {
        global $xoopsDB;
        $sql       = "select `id`, count(*) from `" . $xoopsDB->prefix("tad_booking") . "` group by `id`";
        $result    = $xoopsDB->query($sql) or Utility::web_error($sql);
        $count_arr = [];
        while (list($id, $count) = $xoopsDB->fetchRow($result)) {
            $count_arr[$id] = $count;
        }
        return $count_arr;
    }

    //取得所有tad_booking_cate分類選單的選項（模式 = edit or show,目前分類編號,目前分類的所屬編號）
    public static function get_options($page = '', $mode = 'edit', $default_id = "0", $default_tad_booking_cate_parent_sn = "0", $unselect_level = "", $start_search_sn = "0", $level = 0)
    {
        global $xoopsDB, $xoopsModule;

        $post_cate_arr = chk_cate_power('tad_booking_post');

        // $mod_id             = $xoopsModule->mid();
        // $moduleperm_handler = xoops_gethandler('groupperm');
        $count = self::get_count();

        $sql    = "select `id`, `title` from `" . $xoopsDB->prefix("tad_booking_cate") . "` where `tad_booking_cate_parent_sn` = '{$start_search_sn}' ORDER BY `sort`";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);

        $prefix = str_repeat("&nbsp;&nbsp;", $level);
        $level++;

        $unselect = explode(",", $unselect_level);

        $main = "";
        while (list($id, $title) = $xoopsDB->fetchRow($result)) {

            if (! $_SESSION['tad_booking_adm'] and ! in_array($id, $post_cate_arr)) {
                continue;
            }

            if ($mode == "edit") {
                $selected = ($id == $default_tad_booking_cate_parent_sn) ? "selected=selected" : "";
                $selected .= ($id == $default_id) ? "disabled=disabled" : "";
                $selected .= (in_array($level, $unselect)) ? "disabled=disabled" : "";
            } else {
                if (is_array($default_id)) {
                    $selected = in_array($id, $default_id) ? "selected=selected" : "";
                } else {
                    $selected = ($id == $default_id) ? "selected=selected" : "";
                }
                $selected .= (in_array($level, $unselect)) ? "disabled=disabled" : "";
            }
            if ($page == "none" or empty($count[$id])) {
                $counter = "";
            } else {
                $counter = " (" . $count[$id] . ") ";
            }
            $main .= "<option value=$id $selected>{$prefix}{$title}{$counter}</option>";
            $main .= self::get_options($page, $mode, $default_id, $default_tad_booking_cate_parent_sn, $unselect_level, $id, $level);

        }

        return $main;
    }

//更新排序
    function update_tad_booking_cate_sort()
    {
        global $xoopsDB;
        $sort = 1;
        foreach ($_POST['tr'] as $id) {
            $sql = "UPDATE `" . $xoopsDB->prefix("tad_booking_cate") . "` SET `sort`='{$sort}' WHERE `id`='{$id}'";
            $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . " (" . date("Y-m-d H:i:s") . ")");
            $sort++;
        }
        return _TAD_SORTED . " (" . date("Y-m-d H:i:s") . ")";
    }

}
