<?php

use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_item;
use XoopsModules\Tad_booking\Tad_booking_section;
use XoopsModules\Tad_booking\Tools;
/*-----------引入檔案區--------------*/

require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
Tools::chk_is_adm('', '', __FILE__, __LINE__, 'index.php');
/*-----------變數過濾----------*/
$op         = Request::getString('op');
$id         = Request::getInt('id');
$files_sn   = Request::getInt('files_sn');
$item_id    = Request::getInt('item_id');
$section_id = Request::getInt('section_id');
$week       = Request::getInt('week');
$weeks      = Request::getArray('weeks');
$cate_id    = Request::getInt('cate_id');
$type       = Request::getString('type');
$to_item_id = Request::getInt('to_item_id');

/*-----------執行動作判斷區----------*/
switch ($op) {

    //新增資料
    case 'tad_booking_cate_store':
        $id = Tad_booking_cate::store();
        header("location: {$_SERVER['PHP_SELF']}?id=$id");
        exit;

    //更新資料
    case 'tad_booking_cate_update':
        $where_arr['id'] = $id;
        Tad_booking_cate::update($where_arr);
        header("location: {$_SERVER['PHP_SELF']}?id=$id");
        exit;

    //新增用表單
    case 'tad_booking_cate_create':
        Tad_booking_cate::create();
        break;

    //修改用表單
    case 'tad_booking_cate_edit':
        Tad_booking_cate::create($id);
        $op = 'tad_booking_cate_create';
        break;

    //刪除資料
    case 'tad_booking_cate_destroy':
        Tad_booking_cate::destroy($id);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //新增資料
    case 'tad_booking_item_store':
        $item_id = Tad_booking_item::store();
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id=$item_id");
        exit;

    //更新資料
    case 'tad_booking_item_update':
        $where_arr['id'] = $id;
        Tad_booking_item::update($where_arr);
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id=$id");
        exit;

    //下載檔案
    case 'tufdl':
        $TadUpFiles = new TadUpFiles("tad_booking");
        $TadUpFiles->add_file_counter($files_sn, false);
        exit;

    //新增用表單
    case 'tad_booking_item_create':
        Tad_booking_item::create('', $cate_id);
        break;

    //修改用表單
    case 'tad_booking_item_edit':
        Tad_booking_item::create($item_id);
        $op = 'tad_booking_item_create';
        break;

    //刪除資料
    case 'tad_booking_item_destroy':
        Tad_booking_item::destroy($id);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //顯示某筆資料
    case 'tad_booking_item_show':
        $where_arr['id'] = $item_id;
        Tad_booking_item::show($where_arr, ['uid_name', 'sections', 'item_section_count']);
        break;

    //新增資料
    case 'tad_booking_section_store':
        $id = Tad_booking_section::store();
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id=$item_id");
        exit;

    //刪除資料
    case 'tad_booking_section_destroy':
        Tad_booking_section::destroy($id);
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id=$item_id");
        exit;

    case "copy_time":
        copy_time($item_id, $to_item_id);
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id={$to_item_id}");
        break;

    case "import_time":
        import_time($item_id, $type);
        header("location: {$_SERVER['PHP_SELF']}?op=tad_booking_item_show&item_id={$item_id}");
        // header("location: {$_SERVER['PHP_SELF']}?op=list_tad_booking_section&item_id={$item_id}");
        break;

    case 'tad_booking_item_update_sort':
        header('HTTP/1.1 200 OK');
        $xoopsLogger->activated = false;
        $sort                   = 1;
        foreach ($_POST['tr'] as $primary_keys) {
            list($id) = explode('-', $primary_keys);
            Tad_booking_item::update(['id' => $id], ['sort' => $sort]);
            $sort++;
        }
        die(_TAD_SORTED . " (" . date("Y-m-d H:i:s") . ")");

    default:
        $where_arr['enable'] = '1';
        Tad_booking_cate::index($where_arr, ['item_arr']);
        if (! empty($id)) {
            $where_arr['id'] = $id;
            Tad_booking_cate::show($where_arr);
        }
        $op = 'tad_booking_cate_index';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign('now_op', $op);
$xoTheme->addStylesheet('modules/tad_booking/css/module.css');
$xoTheme->addStylesheet('modules/tadtools/css/vtb.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------功能函數區----------*/

//複製時間區段
function copy_time($item_id = "", $to_item_id = "")
{
    global $xoopsDB;
    $section = Tad_booking_section::get_all(['item_id' => $item_id], [], [], ['sort' => 'asc']);
    foreach ($section as $s) {
        Tad_booking_section::store(['item_id' => $to_item_id, 'title' => $s['title'], 'sort' => $s['sort'], 'week' => $s['week']]);
    }
}

//從範本快速匯入時段設定
function import_time($item_id = "", $type = "")
{
    if ($type == '18') {
        for ($i = 1; $i <= 8; $i++) {
            $title = sprintf(_MD_TADBOOKING_N_TIME, $i);
            Tad_booking_section::store(['item_id' => $item_id, 'title' => $title, 'sort' => $i, 'week' => '1,2,3,4,5']);
        }
    } elseif ($type == 'apm') {
        $apm_arr[1] = _MD_TADBOOKING_AM;
        $apm_arr[2] = _MD_TADBOOKING_PM;
        for ($i = 1; $i <= 2; $i++) {
            $title = $apm_arr[$i];
            Tad_booking_section::store(['item_id' => $item_id, 'title' => $title, 'sort' => $i, 'week' => '1,2,3,4,5']);
        }
    }
}
