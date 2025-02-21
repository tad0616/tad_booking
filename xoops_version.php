<?php

$modversion = [];

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
$modversion['release_date']        = '2025-02-21';
$modversion['module_website_url']  = 'https://www.tad0616.net';
$modversion['module_website_name'] = _MI_TADBOOKING_AUTHOR_WEB;
$modversion['module_status']       = 'release';
$modversion['author_website_url']  = 'https://www.tad0616.net';
$modversion['author_website_name'] = _MI_TADBOOKING_AUTHOR_WEB;
$modversion['min_php']             = '5.4';
$modversion['min_xoops']           = '2.5';

//---paypal資訊---//
$modversion['paypal'] = [
    'business'      => 'tad0616@gmail.com',
    'item_name'     => 'Donation : ' . _MI_TADBOOKING_AUTHOR,
    'amount'        => 0,
    'currency_code' => 'USD',
];

//---安裝設定---//
$modversion['onInstall']   = "include/onInstall.php";
$modversion['onUpdate']    = "include/onUpdate.php";
$modversion['onUninstall'] = "include/onUninstall.php";

//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables']           = ['tad_booking', 'tad_booking_cate', 'tad_booking_data', 'tad_booking_files_center', 'tad_booking_item', 'tad_booking_section', 'tad_booking_week'];

//---後台使用系統選單---//
$modversion['system_menu'] = 1;

//---後台管理介面設定---//
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/main.php';
$modversion['adminmenu']  = 'admin/menu.php';

//---前台主選單設定---//
$modversion['hasMain'] = 1;
$modversion['sub'][]   = [
    ['name' => _MI_TADBOOKING_INDEX, 'url' => 'index.php'],
    ['name' => _MI_TADBOOKING_APPROVAL, 'url' => 'approval.php'],
    ['name' => _MI_TADBOOKING_BATCH, 'url' => 'batch.php'],
    ['name' => _MI_TADBOOKING_LIST, 'url' => 'list.php'],
    ['name' => _MI_TADBOOKING_MANAGER, 'url' => 'manager.php'],
];

//---樣板設定---//
$modversion['templates'] = [
    ['file' => 'tad_booking_admin.tpl', 'description' => 'tad_booking_admin.tpl'],
    ['file' => 'tad_booking_index.tpl', 'description' => 'tad_booking_index.tpl'],
];

//---區塊設定---//
$modversion['blocks'] = [
    [
        'file'        => 'tad_booking_today.php',
        'name'        => _MI_TAD_BOOKING_TODAY_BLOCK_NAME,
        'description' => _MI_TAD_BOOKING_TODAY_BLOCK_DESC,
        'show_func'   => 'tad_booking_today',
        'template'    => 'tad_booking_today.tpl',
        'edit_func'   => 'tad_booking_today_edit',
        'options'     => 'default|0.8',
    ],
    [
        'file'        => 'tad_booking_week.php',
        'name'        => _MI_TAD_BOOKING_WEEK_BLOCK_NAME,
        'description' => _MI_TAD_BOOKING_WEEK_BLOCK_DESC,
        'show_func'   => 'tad_booking_week',
        'template'    => 'tad_booking_week.tpl',
        'edit_func'   => 'tad_booking_week_edit',
        'options'     => 'default|0.8',
    ],
];

$modversion['config'] = [
    [
        'name'        => 'booking_group',
        'title'       => '_MI_TAD_BOOKING_BOOKING_GROUP',
        'description' => '_MI_TAD_BOOKING_BOOKING_GROUP_DESC',
        'formtype'    => 'group_multi',
        'valuetype'   => 'array',
        'default'     => '1',
    ],
    [
        'name'        => 'max_booking_week',
        'title'       => '_MI_TAD_BOOKING_MAX_BOOKING_WEEK',
        'description' => '_MI_TAD_BOOKING_MAX_BOOKING_WEEK_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => '4',
    ],
    [
        'name'        => 'can_send_mail',
        'title'       => '_MI_TAD_BOOKING_CAN_SEND_MAIL',
        'description' => '_MI_TAD_BOOKING_CAN_SEND_MAIL_DESC',
        'formtype'    => 'yesno',
        'valuetype'   => 'int',
        'default'     => 1,
    ],
];
