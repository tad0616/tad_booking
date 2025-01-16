<?php



//區塊主函式 (tad_booking_today)
function tad_booking_today($options){

    global $xoopsDB;

    //{$options[0]} : 選單呈現類型
    $block['options0'] = $options[0] ?  $options[0] : '水平頁籤';

    $sql = "SELECT * FROM `".$xoopsDB->prefix("tad_booking_week")."` ORDER BY `` DESC";
    $result = Utility::query($sql);
    $content = [];
    while($all = $xoopsDB->fetchArray($result)){
        $content[] = $all;
    }
    $block['content']=$content;

    return $block;
}

//區塊編輯函式 (tad_booking_today_edit)
function tad_booking_today_edit($options){


    //$options[0] : "選單呈現類型"預設值
    $selected_0_0 = ($options[0]=='伸縮頁籤') ? 'selected' : '';
    $selected_0_1 = ($options[0]=='水平頁籤') ? 'selected' : '';
    $selected_0_2 = ($options[0]=='垂直頁籤') ? 'selected' : '';

    $form="
    <ol class='my-form'>

        <!--選單呈現類型-->
        <li class='my-row'>
            <lable class='my-label'>選單呈現類型</lable>
            <div class='my-content'>
                <select name='options[0]' class='my-input'>
                    <option value='伸縮頁籤' $selected_0_0>伸縮頁籤</option>
                    <option value='水平頁籤' $selected_0_1>水平頁籤</option>
                    <option value='垂直頁籤' $selected_0_2>垂直頁籤</option>
                </select>
            </div>
        </li>
    </ol>
    ";
    return $form;
}
