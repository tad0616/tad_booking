<?php
namespace XoopsModules\Tad_booking;

use XoopsModules\Tadtools\SweetAlert;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tools;
use XoopsModules\Tadtools\FormValidator;



class Tad_booking_week
{
    // 過濾用變數的設定
    public static $filter_arr = [
        'int' => ['booking_id','week','section_id'],   //數字類的欄位
        'html' => [], //含網頁語法的欄位（所見即所得的內容）
        'text' => [], //純大量文字欄位
        'json' => [], //內容為 json 格式的欄位
        'pass' => [], //不予過濾的欄位
        'explode' => [],   //用分號隔開的欄位
    ];




















}
