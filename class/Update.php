<?php
namespace XoopsModules\Tad_booking;



/**
 * Class Update
 */
class Update
{

    /*
public static function chk_1()
{
global $xoopsDB;
$sql = 'SELECT count(`tag`) FROM ' . $xoopsDB->prefix('tad_booking_files_center');
$result = $xoopsDB->query($sql);
if (empty($result)) {
return true;
}

return false;
}

public static function go_1()
{
global $xoopsDB;
$sql = 'ALTER TABLE ' . $xoopsDB->prefix('tad_booking_files_center') . "
ADD `upload_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上傳時間',
ADD `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '上傳者',
ADD `tag` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '註記'
";
$xoopsDB->queryF($sql) or redirect_header(XOOPS_URL . '/modules/system/admin.php?fct=modulesadmin', 30, $xoopsDB->error());
}
 */

}
