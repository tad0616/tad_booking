<?php
require_once __DIR__ . '/header.php';
header('HTTP/1.1 200 OK');
$xoopsLogger->activated = false;
$sort = 1;
foreach ($_POST['tr'] as $primary_keys) {
    list($booking_date, $booking_id, $section_id) = explode('-',$primary_keys);
    $sql = "update `" . $xoopsDB->prefix("tad_booking_data") . "` set ``='{$sort}' where `booking_date`='{$booking_date}' and `booking_id`='{$booking_id}' and `section_id`='{$section_id}'";
    $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . " (" . date("Y-m-d H:i:s") . ")");
    $sort++;
}
echo _TAD_SORTED . " (" . date("Y-m-d H:i:s") . ")";;
