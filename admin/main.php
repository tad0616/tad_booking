<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_booking\Tad_booking;
use XoopsModules\Tad_booking\Tools;
/*-----------引入檔案區--------------*/
$GLOBALS['xoopsOption']['template_main'] = 'tad_booking_admin.tpl';
require_once __DIR__ . '/header.php';

/*-----------變數過濾----------*/
$op = Request::getString('op');

/*-----------執行動作判斷區----------*/
switch ($op) {

    case 'import_now':
        import_now();
        header('location: ../index.php');
        exit;

    default:
        check_jill_booking();
        $op = 'tad_booking_import';
        break;

}
/*-----------秀出結果區--------------*/
$xoopsTpl->assign('now_op', $op);
require_once __DIR__ . '/footer.php';

/*-----------功能函數區----------*/

//列出所有 check_jill_booking 資料
function check_jill_booking()
{
    global $xoopsDB, $xoopsTpl;

    //取得某模組編號

    $moduleHandler  = xoops_getHandler('module');
    $ThexoopsModule = $moduleHandler->getByDirname('jill_booking');

    if ($ThexoopsModule) {
        $mod_id = $ThexoopsModule->getVar('mid');
        $xoopsTpl->assign('show_error', '0');
    } else {
        $xoopsTpl->assign('show_error', '1');
        $xoopsTpl->assign('msg', _MA_TADBOOKING_NO_NEED_IMPORT);

        return;
    }

    $sql = "SELECT 'jill_booking' AS table_name, COUNT(*) AS row_count FROM `" . $xoopsDB->prefix('jill_booking') . "`
    UNION ALL
    SELECT 'jill_booking_week', COUNT(*) FROM `" . $xoopsDB->prefix('jill_booking_week') . "`
    UNION ALL
    SELECT 'jill_booking_date', COUNT(*) FROM `" . $xoopsDB->prefix('jill_booking_date') . "`
    UNION ALL
    SELECT 'jill_booking_item', COUNT(*) FROM `" . $xoopsDB->prefix('jill_booking_item') . "`
    UNION ALL
    SELECT 'jill_booking_time', COUNT(*) FROM `" . $xoopsDB->prefix('jill_booking_time') . "`";
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $jill_booking_content = [];
    while (false !== ($all = $xoopsDB->fetchArray($result))) {
        $jill_booking_content[] = $all;
    }
    $xoopsTpl->assign('jill_booking_content', $jill_booking_content);

    $sql = "SELECT 'tad_booking' AS table_name, COUNT(*) AS row_count FROM `" . $xoopsDB->prefix('tad_booking') . "`
    UNION ALL
    SELECT 'tad_booking_week', COUNT(*) FROM `" . $xoopsDB->prefix('tad_booking_week') . "`
    UNION ALL
    SELECT 'tad_booking_data', COUNT(*) FROM `" . $xoopsDB->prefix('tad_booking_data') . "`
    UNION ALL
    SELECT 'tad_booking_item', COUNT(*) FROM `" . $xoopsDB->prefix('tad_booking_item') . "`
    UNION ALL
    SELECT 'tad_booking_section', COUNT(*) FROM `" . $xoopsDB->prefix('tad_booking_section') . "`";
    $result = Utility::query($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $tad_booking_content = [];
    while (false !== ($all = $xoopsDB->fetchArray($result))) {
        $tad_booking_content[] = $all;
    }
    $xoopsTpl->assign('tad_booking_content', $tad_booking_content);
}

function import_now()
{
    global $xoopsDB;

    // 複製 jill_booking 到 tad_booking
    $sql = "
        INSERT INTO `" . $xoopsDB->prefix('tad_booking') . "` (id, uid, booking_time, content, start_date, end_date, info)
        SELECT jb_sn, jb_uid, jb_booking_time, jb_booking_content, jb_start_date, jb_end_date, CONCAT('{\"name\": \"', COALESCE(u.name, u.uname), '\", \"email\": \"', u.email, '\"}')
        FROM `" . $xoopsDB->prefix('jill_booking') . "` as jb
        LEFT JOIN `" . $xoopsDB->prefix('users') . "` as u ON jb.jb_uid = u.uid;
    ";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    // 複製 jill_booking_week 到 tad_booking_week
    $sql = "
        INSERT INTO `" . $xoopsDB->prefix('tad_booking_week') . "` (booking_id, week, section_id, start_date, end_date)
        SELECT a.jb_sn, a.jb_week, a.jbt_sn, b.jb_start_date, b.jb_end_date
        FROM `" . $xoopsDB->prefix('jill_booking_week') . "` as a LEFT JOIN `" . $xoopsDB->prefix('jill_booking') . "` as b ON a.jb_sn = b.jb_sn
    ";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    // 複製 jill_booking_item 到 tad_booking_item
    $sql = "
        INSERT INTO `" . $xoopsDB->prefix('tad_booking_item') . "` (id, cate_id, title, `desc`, sort, start, end, enable, approval, info)
        SELECT jbi_sn, 1, jbi_title, jbi_desc, jbi_sort, jbi_start, jbi_end, jbi_enable, jbi_approval, NULL
        FROM `" . $xoopsDB->prefix('jill_booking_item') . "`
    ";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    // 複製 jill_booking_time 到 tad_booking_section
    $sql = "
        INSERT INTO `" . $xoopsDB->prefix('tad_booking_section') . "` (id, item_id, title, sort, week)
        SELECT jbt_sn, jbi_sn, jbt_title, jbt_sort, jbt_week
        FROM `" . $xoopsDB->prefix('jill_booking_time') . "`
    ";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    // 複製 jill_booking_date 到 tad_booking_data
    $sql = "
        INSERT INTO `" . $xoopsDB->prefix('tad_booking_data') . "` (booking_id, booking_date, item_id, section_id, waiting, status, approver, pass_date)
        SELECT a.jb_sn, a.jb_date, b.jbi_sn, a.jbt_sn, a.jb_waiting, a.jb_status, a.approver, a.pass_date
        FROM `" . $xoopsDB->prefix('jill_booking_date') . "` as a LEFT JOIN `" . $xoopsDB->prefix('jill_booking_time') . "` as b ON a.jbt_sn = b.jbt_sn
    ";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

    $sql = "SELECT  a.`jb_sn`, b.`jb_start_date`, b.`jb_end_date`
    FROM `" . $xoopsDB->prefix('jill_booking_week') . "` as a
    LEFT JOIN `" . $xoopsDB->prefix('jill_booking') . "` as b ON a.`jb_sn`=b.`jb_sn`
    GROUP BY a.`jb_sn`
    HAVING count(a.`jb_sn`)> 1";

    $result = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    while (list($jb_sn, $jb_start_date, $jb_end_date) = $xoopsDB->fetchRow($result)) {
        if ($jb_start_date != $jb_end_date) {
            $sql          = "SELECT  `jb_week`, `jbt_sn` FROM `" . $xoopsDB->prefix('jill_booking_week') . "` WHERE `jb_sn` = '{$jb_sn}'";
            $result2      = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
            $week_section = [];
            while (list($jb_week, $jbt_sn) = $xoopsDB->fetchRow($result2)) {
                $week_section[$jb_week][$jbt_sn] = $jbt_sn;
                $jb_week_arr[$jb_week]           = $jb_week;
            }
            $dates                = Tools::findDatesByDaysOfWeekGrouped($jb_start_date, $jb_end_date, $jb_week_arr);
            $booking_week_section = [];
            foreach ($dates as $week => $date_arr) {
                foreach ($week_section[$week] as $section_id) {
                    foreach ($date_arr as $date) {
                        $booking_week_section[$week][$section_id][] = $date;
                    }
                }
            }
            Tad_booking::update(['id' => $jb_sn], ['batch' => ['dates' => $booking_week_section, 'start_date' => $jb_start_date, 'end_date' => $jb_end_date]]);
        }
    }

}
