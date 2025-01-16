<?php


$modversion = [];
global $xoopsConfig;

//---模組基本資訊---//
$modversion['name']        = _MI_TADBOOKING_NAME;
$modversion['version']     = $_SESSION['xoops_version'] >= 20511 ? '1.0.0-Stable' : '1.0';
$modversion['description'] = _MI_TADBOOKING_DESC;
$modversion['author']      = _MI_TADBOOKING_AUTHOR;
$modversion['credits']     = _MI_TADBOOKING_CREDITS;
$modversion['help']        = 'page=help';
$modversion['license']     = 'GPL see LICENSE';
$modversion['image']       = "images/logo.png";
$modversion['dirname']     = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date']        = '2025-01-23';
$modversion['module_website_url']  = 'https://tad0616.net';
$modversion['module_website_name'] = _MI_TADBOOKING_AUTHOR_WEB;
$modversion['module_status']       = 'release';
$modversion['author_website_url']  = 'https://tad0616.net';
$modversion['author_website_name'] = _MI_TADBOOKING_AUTHOR_WEB;
$modversion['min_php']             = '5.4';
$modversion['min_xoops']           = '2.5';

//---paypal資訊---//
$modversion['paypal'] = [
    'business' => 'tad0616@gmail.com',
    'item_name' => 'Donation : ' . _MI_TADBOOKING_AUTHOR,
    'amount' => 0,
    'currency_code' => 'USD',
];

//---安裝設定---//
$modversion['onInstall']   = "include/onInstall.php";
$modversion['onUpdate']    = "include/onUpdate.php";
$modversion['onUninstall'] = "include/onUninstall.php";



//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][] = "tad_booking";
$modversion['tables'][] = "tad_booking_cate";
$modversion['tables'][] = "tad_booking_data";
$modversion['tables'][] = "tad_booking_item";
$modversion['tables'][] = "tad_booking_section";
$modversion['tables'][] = "tad_booking_week";


//---後台使用系統選單---//
$modversion['system_menu'] = 1;

//---後台管理介面設定---//
$modversion['hasAdmin']   = 0;
$modversion['adminindex'] = 'admin/main.php';
$modversion['adminmenu']  = 'admin/menu.php';

//---前台主選單設定---//
$modversion['hasMain'] = 1;
$modversion['sub'][] = array('name' => _MI_TADBOOKING_PAGE_1, 'url'=> 'approval.php');
$modversion['sub'][] = array('name' => _MI_TADBOOKING_PAGE_2, 'url'=> 'batch.php');
$modversion['sub'][] = array('name' => _MI_TADBOOKING_PAGE_3, 'url'=> 'list.php');
$modversion['sub'][] = array('name' => _MI_TADBOOKING_PAGE_4, 'url'=> 'manager.php');


//---樣板設定---//
$modversion['templates'][] = ['file' => 'tad_booking_index.tpl', 'description' => 'tad_booking_index.tpl'];
$modversion['templates'][] = ['file' => 'tad_booking_approval.tpl', 'description' => 'tad_booking_approval.tpl'];
$modversion['templates'][] = ['file' => 'tad_booking_batch.tpl', 'description' => 'tad_booking_batch.tpl'];
$modversion['templates'][] = ['file' => 'tad_booking_list.tpl', 'description' => 'tad_booking_list.tpl'];
$modversion['templates'][] = ['file' => 'tad_booking_manager.tpl', 'description' => 'tad_booking_manager.tpl'];



//---區塊設定---//
$modversion['blocks'] = [
    [
        'file' => 'tad_booking_today.php',
        'name' => _MI_TAD_BOOKING_TODAY_BLOCK_NAME,
        'description' => _MI_TAD_BOOKING_TODAY_BLOCK_DESC,
        'show_func' => 'tad_booking_today',
        'template' => 'tad_booking_today.tpl',
        'edit_func' => 'tad_booking_today_edit',
        'options' => '水平頁籤',
    ],
    [
        'file' => 'tad_booking_week.php',
        'name' => _MI_TAD_BOOKING_WEEK_BLOCK_NAME,
        'description' => _MI_TAD_BOOKING_WEEK_BLOCK_DESC,
        'show_func' => 'tad_booking_week',
        'template' => 'tad_booking_week.tpl',
        'edit_func' => 'tad_booking_week_edit',
        'options' => '水平頁籤',
    ],
];

