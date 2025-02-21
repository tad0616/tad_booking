<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\Bootstrap3Editable;
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\FormValidator;
use XoopsModules\Tadtools\My97DatePicker;
use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\TadUpFiles;
use XoopsModules\Tadtools\Tmt;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tools;

class Tad_booking_item
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int'     => ['id', 'cate_id', 'sort'], //數字類的欄位
        'html'    => ['desc'],                  //含網頁語法的欄位（所見即所得的內容）
        'text'    => [],                        //純大量文字欄位
        'json'    => ['info'],                  //內容為 json 格式的欄位
        'pass'    => [],                        //不予過濾的欄位
        'explode' => ['approval'],              //用分號隔開的欄位
    ];

    //取得tad_booking_item所有資料陣列
    public static function get_all($where_arr = [], $other_arr = [], $view_cols = [], $order_arr = [], $key_name = false, $get_value = '', $filter = 'read', $amount = '')
    {
        global $xoopsDB;

        $and_sql   = Tools::get_and_where($where_arr);
        $view_col  = Tools::get_view_col($view_cols);
        $order_sql = Tools::get_order($order_arr);
        $order     = $amount ? '' : $order_sql;

        $sql = "SELECT {$view_col} FROM `" . $xoopsDB->prefix("tad_booking_item") . "` WHERE 1 {$and_sql} {$order}";
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

            if (in_array('sections', $other_arr) || in_array('all', $other_arr)) {
                $data['sections'] = Tad_booking_section::get_all(['item_id' => $data['id']], ['week_arr'], [], ['sort' => 'ASC'], 'id');
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

    //以流水號秀出某筆 tad_booking_item 資料內容 Tad_booking_item::show()
    public static function show($where_arr = [], $other_arr = [], $mode = '')
    {
        global $xoopsTpl;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $all = self::get($where_arr, $other_arr);
        // Utility::dd($all, 'all');
        if (empty($all)) {
            return false;
        }

        foreach ($all as $key => $value) {
            $value     = Tools::filter($key, $value, 'read', self::$filter_arr);
            $all[$key] = $value;
            $$key      = $value;
        }

        //取得分類資料(tad_booking_cate)
        $cate = Tad_booking_cate::get(['id' => $cate_id]);
        $xoopsTpl->assign('cate', $cate);

        $SweetAlert = new SweetAlert();
        $SweetAlert->render('tad_booking_item_destroy_func', "manager.php?op=tad_booking_item_destroy&id=", "id");

        $SweetAlert2 = new SweetAlert();
        $SweetAlert2->render('delete_tad_booking_section_func', "manager.php?op=tad_booking_section_destroy&item_id=$id&id=", "id");

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        $Bootstrap3Editable     = new Bootstrap3Editable();
        $Bootstrap3EditableCode = $Bootstrap3Editable->render('.editable', 'ajax.php');
        $xoopsTpl->assign('Bootstrap3EditableCode', $Bootstrap3EditableCode);

        if ($mode == "return") {
            return $all;
        } elseif ($mode == "assign_all") {
            $xoopsTpl->assign('tad_booking_item', $all);
        } else {
            foreach ($all as $key => $value) {
                $xoopsTpl->assign($key, $value);
            }
        }
    }

    //以流水號取得某筆 tad_booking_item 資料
    public static function get($where_arr = [], $other_arr = [], $filter = 'read', $only_key = '')
    {
        global $xoopsDB;

        if (empty($where_arr)) {
            redirect_header($_SERVER['HTTP_REFERER'], 3, "無查詢條件：" . __FILE__ . __LINE__);
        }

        $and_sql = Tools::get_and_where($where_arr);

        $sql    = "SELECT * FROM `" . $xoopsDB->prefix("tad_booking_item") . "` WHERE 1 $and_sql";
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);
        $data   = $xoopsDB->fetchArray($result);
        $data   = Tools::filter_all_data($filter, $data, self::$filter_arr);

        //上傳工具
        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->set_col("tad_booking_item_id", $data['id']);
        $data['files'] = $TadUpFiles->show_files('tad_booking_item_files', true, 'thumb', false, false, null, null, false);

        foreach ($data['info'] as $approval_uid => $approver) {
            $data['approval_name_arr'][$approval_uid] = $approver['name'];
        }

        if (in_array('cate', $other_arr) || in_array('all', $other_arr)) {
            $data['cate'] = Tad_booking_cate::get(['id' => $data['cate_id']]);
        }

        if (in_array('sections', $other_arr) || in_array('all', $other_arr)) {
            $data['sections'] = Tad_booking_section::get_all(['item_id' => $data['id']], ['week_arr'], [], ['sort' => 'ASC'], 'id');
        }

        if (in_array('item_section_count', $other_arr) || in_array('all', $other_arr)) {
            $all_item           = self::get_all([], [], [], [], 'id');
            $item_section_count = Tad_booking_section::count_arr();
            foreach ($all_item as $id => $item) {
                if ($item_section_count[$id] > 0) {
                    $data['item_section_count'][$id]['count'] = $item_section_count[$id];
                    $data['item_section_count'][$id]['title'] = $item['title'];
                    $data['item_section_count'][$id]['item']  = $item;
                }
            }
        }

        if (in_array('week_dates', $other_arr) || in_array('all', $other_arr)) {
            $week_data = Tools::findDatesByTargetDay($_GET['date']);
            foreach ($week_data as $key => $value) {
                $data[$key] = $value;
            }

            // 找出該週的預約
            list($data['booking_arr'], $data['ok_booking']) = Tools::booking_arr($data['week_dates'][0], $data['week_dates'][6]);
        }

        if (in_array('booking_arr', $other_arr) || in_array('all', $other_arr)) {
            if ($_REQUEST['start_date'] and $_REQUEST['end_date']) {
                list($data['booking_arr'], $data['ok_booking']) = Tools::booking_arr($_REQUEST['start_date'], $_REQUEST['end_date']);
            } else {
                $data['booking_arr'] = $data['ok_booking'] = [];
            }
            $data['chinese_week'] = Tad_booking_section::$chinese_week;
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

    //tad_booking_item 編輯表單
    public static function create($id = '', $cate_id = '')
    {
        global $xoopsTpl, $xoopsDB;
        Tools::chk_is_adm('can_booking', '', __FILE__, __LINE__);

        //抓取預設值
        $tad_booking_item = (! empty($id)) ? self::get(['id' => $id]) : [];

        //預設值設定

        $def['id']      = $id;
        $def['cate_id'] = $cate_id;
        $def['sort']    = self::max_sort();
        $def['start']   = date("Y-m-d");
        $def['enable']  = '1';

        if (empty($tad_booking_item)) {
            $tad_booking_item = $def;
        }

        foreach ($tad_booking_item as $key => $value) {
            $value = Tools::filter($key, $value, 'edit', self::$filter_arr);
            $$key  = isset($tad_booking_item[$key]) ? $tad_booking_item[$key] : $def[$key];
            $xoopsTpl->assign($key, $value);
        }

        $op = (! empty($id)) ? "tad_booking_item_update" : "tad_booking_item_store";
        $xoopsTpl->assign('next_op', $op);

        //套用formValidator驗證機制
        $formValidator = new FormValidator("#myForm", true);
        $formValidator->render();

        //類別編號
        $sql              = "SELECT `id`, `title` FROM `" . $xoopsDB->prefix("tad_booking_cate") . "` ORDER BY sort";
        $result           = Utility::query($sql);
        $i                = 0;
        $id_options_array = [];
        while (list($cate_id, $cate_title) = $xoopsDB->fetchRow($result)) {
            $id_options_array[$i]['id']    = $cate_id;
            $id_options_array[$i]['title'] = $cate_title;
            $i++;
        }
        $xoopsTpl->assign("id_options", $id_options_array);

        //上傳表單
        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->set_col("tad_booking_item_id", $id);
        //$TadUpFiles->set_dir('subdir', "");
        //$TadUpFiles->set_var("require", true);  //必填
        //$TadUpFiles->set_var("show_tip", false); //不顯示提示
        $tad_booking_item_files_create = $TadUpFiles->upform(true, "tad_booking_item_files", "");
        $xoopsTpl->assign('tad_booking_item_files_create', $tad_booking_item_files_create);

        //加入Token安全機制
        Utility::token_form();

        $sql      = 'SELECT `uid`,`name`,`uname` FROM `' . $xoopsDB->prefix('users') . '` ORDER BY `uname`';
        $result   = Utility::query($sql);
        $from_arr = $to_arr = [];
        $i        = 0;
        while (list($uid, $name, $uname) = $xoopsDB->fetchRow($result)) {
            $approval_arr = explode(",", $approval);
            if (in_array($uid, $approval_arr)) {
                $to_arr[$uid] = "{$name} ({$uname})";
            } else {
                $from_arr[$uid] = "{$name} ({$uname})";
            }

            $i++;
        }
        $hidden_arr = ['op' => $op, 'id' => $id];
        $tmt_box    = Tmt::render('approval', $from_arr, $to_arr, $hidden_arr);
        $xoopsTpl->assign('tmt_box', $tmt_box);

        My97DatePicker::render();

        $CkEditor = new CkEditor("tad_booking", "desc", $desc);
        $CkEditor->setHeight(150);
        $CkEditor->setToolbarSet('tadSimple');
        $editor = $CkEditor->render();
        $xoopsTpl->assign('editor', $editor);
    }

    //新增資料到 tad_booking_item Tad_booking_item::store()
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

        if (! empty($approval)) {
            $approval_arr   = explode(',', $approval);
            $member_handler = xoops_gethandler('member');
            $approval_info  = [];
            foreach ($approval_arr as $approval_uid) {

                $user = $member_handler->getUser($approval_uid);

                $approval_info[$approval_uid]['name']  = $user->name();
                $approval_info[$approval_uid]['name']  = empty($approval_info[$approval_uid]['name']) ? $user->uname() : $approval_info[$approval_uid]['name'];
                $approval_info[$approval_uid]['email'] = $user->email();
            }
            $info = \json_encode($approval_info, 256);
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

        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->set_col("tad_booking_item_id", $id);
        //$TadUpFiles->set_dir('subdir', "");
        $TadUpFiles->upload_file('tad_booking_item_files', '', '', '', '', true, false);
        return $id;
    }

    //更新 tad_booking_item 某一筆資料 Tad_booking_item::update()
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
            $sql         = "UPDATE `" . $xoopsDB->prefix("tad_booking_item") . "` SET
            $update_cols WHERE 1 $and";
        } else {
            //XOOPS表單安全檢查
            Utility::xoops_security_check(__FILE__, __LINE__);
            foreach ($_POST as $key => $value) {
                $$key = Tools::filter($key, $value, 'write', self::$filter_arr);
            }

            if (! empty($approval)) {
                $approval_arr   = explode(',', $approval);
                $member_handler = xoops_gethandler('member');
                $approval_info  = [];
                foreach ($approval_arr as $approval_uid) {

                    $user = $member_handler->getUser($approval_uid);

                    $approval_info[$approval_uid]['name']  = $user->name();
                    $approval_info[$approval_uid]['name']  = empty($approval_info[$approval_uid]['name']) ? $user->uname() : $approval_info[$approval_uid]['name'];
                    $approval_info[$approval_uid]['email'] = $user->email();
                }
                $info = \json_encode($approval_info, 256);
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
        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->set_col("tad_booking_item_id", $id);
        //$TadUpFiles->set_dir('subdir', "");
        $TadUpFiles->upload_file('tad_booking_item_files', '', '', '', '', true, false);

        return $where_arr['id'];
    }

    //刪除tad_booking_item某筆資料資料
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

        $sql = "DELETE FROM `" . $xoopsDB->prefix("tad_booking_item") . "`
        WHERE 1 $and";
        $xoopsDB->queryF($sql) or Utility::web_error($sql);

        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->set_col("id", $id);
        $TadUpFiles->del_files();
    }

    //自動取得tad_booking_item的最新排序
    public static function max_sort()
    {
        global $xoopsDB;
        $sql        = "select max(`sort`) from `" . $xoopsDB->prefix("tad_booking_item") . "`";
        $result     = $xoopsDB->query($sql) or Utility::web_error($sql);
        list($sort) = $xoopsDB->fetchRow($result);
        return ++$sort;
    }
}
