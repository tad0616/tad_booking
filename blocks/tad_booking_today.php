<?php

use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\EasyResponsiveTabs;

if (!class_exists('XoopsModules\Tadtools\Utility')) {
    require XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';
}

use XoopsModules\Tad_booking\Tad_booking_cate;
use XoopsModules\Tad_booking\Tad_booking_data;

if (!class_exists('XoopsModules\Tad_booking\Tad_booking_cate')) {
    require XOOPS_ROOT_PATH . '/modules/tad_booking/preloads/autoloader.php';
}

//區塊主函式 (tad_booking_today)
function tad_booking_today($options)
{

    $block['today'] = date('Y-m-d');
    $block['type'] = $options[0] ? $options[0] : 'default';
    // unset($_SESSION['tad_booking_cate_arr']);
    if (!isset($_SESSION['tad_booking_cate_arr'])) {
        $_SESSION['tad_booking_cate_arr'] =   $block['cate_arr']    = Tad_booking_cate::get_all(['enable' => 1], ['items_sections'], ['title', 'id'], ['sort' => 'ASC'], 'id');
    } else {
        $block['cate_arr']    =  $_SESSION['tad_booking_cate_arr'];
    }
    $booking_data_arr = Tad_booking_data::get_all(['booking_date' =>  $block['today'], 'status' => '1'], ['who'], [], ['section_id' => 'ASC', 'waiting' => 'ASC']);
    foreach ($booking_data_arr as $key => $booking_data) {
        $block['booking_data_arr'][$booking_data['section_id']][$booking_data['waiting']] = $booking_data;
    }
    // Utility::dd($block['cate_arr']);

    $randStr = Utility::randStr();
    $responsive_tabs = new EasyResponsiveTabs('#todayBookingTab' . $randStr, $options[0]);
    $responsive_tabs->render();

    $block['randStr'] = $randStr;
    $block['w'] = date('w', strtotime($block['today']));
    $block['font_size'] = $options[1] ? $options[1] : '0.8';
    return $block;
}

//區塊編輯函式 (tad_booking_today_edit)
function tad_booking_today_edit($options)
{

    //$options[0] : "選單呈現類型"預設值
    $selected_0_0 = ($options[0] == _MB_TADBOOKING_ACCORDION) ? 'selected' : '';
    $selected_0_1 = ($options[0] == _MB_TADBOOKING_DEFAULT) ? 'selected' : '';
    $selected_0_2 = ($options[0] == _MB_TADBOOKING_VERTICAL) ? 'selected' : '';
    $options[1] = $options[1] ? $options[1] : '0.8';
    $form = "
    <ol class='my-form'>

        <!--選單呈現類型-->
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADBOOKING_TAB_TYPE . "</lable>
            <div class='my-content'>
                <select name='options[0]' class='my-input'>
                    <option value='accordion' $selected_0_0>" . _MB_TADBOOKING_ACCORDION . "</option>
                    <option value='default' $selected_0_1>" . _MB_TADBOOKING_DEFAULT . "</option>
                    <option value='vertical' $selected_0_2>" . _MB_TADBOOKING_VERTICAL . "</option>
                </select>
            </div>
        </li>
        <li class='my-row'>
            <lable class='my-label'>" . _MB_TADBOOKING_FONT_SIZE . "</lable>
            <div class='my-content'>
                <input type='text' name='options[1]' class='my-input' value='{$options[1]}'> rem
            </div>
        </li>
    </ol>
    ";
    return $form;
}
