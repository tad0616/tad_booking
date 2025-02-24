<?php
namespace XoopsModules\Tad_booking;

use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\Wcag;
use XoopsModules\Tad_booking\Tad_booking_item;

/**
 * Class Tools
 */
class Tools
{
    // 變數過濾
    public static function filter($key, $value, $mode = "read", $filter_arr = [])
    {
        global $xoopsDB;
        $myts = \MyTextSanitizer::getInstance();

        if (isset($filter_arr['pass']) && in_array($key, $filter_arr['pass'])) {
            return $value;
        }

        if ($mode == 'write' && in_array($key, $filter_arr['json'])) {
            $value = json_encode($value, 256);
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (isset($filter_arr['json']) and is_array($filter_arr['json']) and in_array($key, $filter_arr['json'])) {
                    $v = self::filter($k, $v, $mode, $filter_arr);
                } else {
                    $v = self::filter($key, $v, $mode, $filter_arr);
                }
                $value[$k] = $v;
            }
        } else {
            if (isset($filter_arr['int']) && in_array($key, $filter_arr['int'], true)) {
                $value = (int) $value;
            } elseif (isset($filter_arr['html']) && in_array($key, $filter_arr['html'], true)) {
                if ($mode == 'edit') {
                    $value = $myts->htmlSpecialChars($value);
                } else {
                    $value = ($mode == 'write') ? $xoopsDB->escape(Wcag::amend(trim($value))) : $myts->displayTarea($value, 1, 1, 1, 1, 0);
                }
            } elseif (isset($filter_arr['text']) && in_array($key, $filter_arr['text'], true)) {
                if ($mode == 'edit') {
                    $value = $myts->htmlSpecialChars($value);
                } else {
                    $value = ($mode == 'write') ? $xoopsDB->escape(trim($value)) : $myts->displayTarea($value, 0, 0, 0, 1, 1);
                }
            } elseif (isset($filter_arr['json']) && in_array($key, $filter_arr['json'], true)) {

                if ($mode == 'write') {
                    $value = $xoopsDB->escape(trim($value));
                } else {
                    $value = json_decode($value, true);
                    foreach ($value as $k => $v) {
                        $value[$k] = self::filter($k, $v, $mode);
                    }
                }
            } elseif (! isset($filter_arr['pass']) || ! in_array($key, $filter_arr['pass'], true)) {
                if ($mode == 'edit') {
                    $value = $myts->htmlSpecialChars($value);
                } else {
                    $value = ($mode == 'write') ? $xoopsDB->escape(trim($value)) : $myts->htmlSpecialChars($value);
                }
            }
        }

        return $value;
    }

    // 取得資料庫條件
    public static function get_and_where($where_arr = '', $prefix = '')
    {
        global $xoopsDB;
        if (is_array($where_arr)) {
            $and_where_arr = '';
            foreach ($where_arr as $col => $value) {
                $and_where_arr .= ! is_string($col) ? " and {$value}" : " and {$prefix}`{$col}` = '" . $xoopsDB->escape($value) . "'";
            }
        } else {
            $and_where_arr = $where_arr;
        }
        return $and_where_arr;
    }

    // 取得資料庫顯示欄位
    public static function get_view_col($view_cols = [], $prefix = '')
    {
        if (empty($view_cols)) {
            $view_col = $prefix . '*';
        } elseif (is_array($view_cols)) {
            $view_col = $prefix . '`' . implode("`, `{$prefix}", $view_cols) . '`';
        } else {
            $view_col = $view_cols;
        }
        return $view_col;
    }

    // 取得資料庫排序條件
    public static function get_order($order_arr = [], $prefix = '')
    {
        $before_sql   = $order_sql   = $after_sql   = '';
        $before_items = $order_items = $after_items = [];
        if ($order_arr) {
            foreach ($order_arr as $col => $asc) {
                if ($col === 'before order') {
                    $before_items[] = $asc;
                } elseif ($col === 'after order') {
                    $after_items[] = $asc;
                } elseif (! is_string($col) or empty($col)) {
                    $order_items[] = $asc;
                } else {
                    $order_items[] = "{$prefix}`{$col}` $asc";
                }
            }

            $before_sql = empty($before_items) ? '' : implode(',', $before_items);
            $order_sql  = empty($order_items) ? '' : "ORDER BY " . implode(',', $order_items);
            $after_sql  = empty($after_items) ? '' : implode(',', $after_items);
        }
        return "$before_sql $order_sql $after_sql";
    }

    // 過濾所有資料
    public static function filter_all_data($filter, $data, $filter_arr)
    {
        if ($filter) {
            foreach ($data as $key => $value) {
                $data[$key] = self::filter($key, $value, $filter, $filter_arr);
            }
        }
        return $data;
    }

    // 權限檢查
    public static function chk_is_adm($other = '', $id = '', $file = '', $line = '', $link = '')
    {
        $id   = (int) $id;
        $link = empty($link) ? $_SERVER['PHP_SELF'] : $link;
        $file = str_replace('\\', '/', $file);
        if ($_SESSION['tad_booking_adm'] || ($other != '' && $_SESSION[$other]) || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            if (! empty($id) && $_SESSION[$other]) {
                if (in_array($id, $_SESSION[$other]) || $id == $_SESSION[$other]) {
                    return true;
                } else {
                    redirect_header($link, 3, "您對筆資料 ($id) 無操作權限 {$file} ($line)");
                }
            }
        } else {
            redirect_header($link, 3, "無操作權限 {$file} ($line)");
        }
    }

    //取得session
    public static function get_session()
    {
        global $xoopsUser, $xoopsModuleConfig;

        //判斷是否對該模組有管理權限
        if (! isset($_SESSION['tad_booking_adm'])) {
            $_SESSION['tad_booking_adm'] = isset($xoopsUser) && \is_object($xoopsUser) ? $xoopsUser->isAdmin() : false;
        }

        if (! isset($_SESSION['can_booking'])) {
            $_SESSION['can_booking'] = isset($xoopsUser) && \is_object($xoopsUser) ? array_intersect($_SESSION['xoopsUserGroups'], (array) $xoopsModuleConfig['booking_group']) : false;
        }

        if (! isset($_SESSION['now_user'])) {
            $_SESSION['now_user'] = $user = ($xoopsUser) ? $xoopsUser->toArray() : [];
        }

        if (! isset($_SESSION['can_approve'])) {
            $item_approval = Tad_booking_item::get_all(['enable' => 1, 'approval !=""'], [], ['id', 'approval'], [], 'id', 'approval');
            foreach ($item_approval as $item_id => $approval) {
                $approval_arr = explode(',', $approval);
                if (in_array($user['uid'], $approval_arr)) {
                    $_SESSION['can_approve'][] = $item_id;
                }
            }
        }

        if ($_SESSION['now_user']) {
            if (! isset($_SESSION['SchoolCode'])) {
                if ($_REQUEST['SchoolCode']) {
                    $_SESSION['SchoolCode'] = Request::getString('SchoolCode');
                } else {
                    $_SESSION['SchoolCode'] = ($xoopsUser) ? $xoopsUser->user_intrest() : false;
                }
            }
        }
    }

    //產生刪除預約鈕
    public static function delete_booking_icon($item_id = "", $booking_date = "", $section_id = "", $booking_arr = [])
    {
        global $xoopsUser;
        if (! isset($xoopsUser)) {
            return;
        }
        if (empty($booking_arr)) {
            list($booking_arr, $ok_booking) = self::booking_arr($booking_date, $booking_date, $section_id);
        }

        //將 uid 編號轉換成使用者姓名（或帳號）
        $uid         = $xoopsUser->uid();
        $uid_name    = $booking_arr[$booking_date][$section_id][$uid]['info']['name'];
        $is_approval = empty($booking_arr[$booking_date][$section_id][$uid]['status']) ? '<span class="approving">' . _MD_TADBOOKING_APPROVING . '</span>' : $uid_name;

        $icon = "{$is_approval}<a href=\"javascript:delete_booking('{$item_id}', '{$booking_date}', '{$section_id}', '{$item_id}', '{$booking_arr[$booking_date][$section_id][$uid]['booking_id']}', '{$uid}');\" style='color:#D44950;' ><i class='fa fa-times' ></i></a>";
        return $icon;
    }

    //取得用了該日期時段的uid
    public static function booking_arr($start_date, $end_date, $section_id = "", $last_index = 'uid')
    {
        global $xoopsDB;

        $booking_arr = [];
        //先抓核准通過的順位
        $sql = "SELECT a.`section_id`, a.`booking_date`, a.`waiting`, a.`status`, b.`id`, b.`uid`, b.`content`, b.`info` FROM `" . $xoopsDB->prefix('tad_booking_data') . "` AS a
        LEFT JOIN `" . $xoopsDB->prefix('tad_booking') . "` AS b ON a.`booking_id` = b.`id`
        WHERE a.`booking_date` BETWEEN '{$start_date}' AND '{$end_date}' ORDER BY a.`waiting`";
        if (! empty($section_id)) {
            $sql .= " AND a.`section_id` = '{$section_id}'";
        }
        $result = $xoopsDB->query($sql) or Utility::web_error($sql);

        while (list($section_id, $booking_date, $waiting, $status, $booking_id, $uid, $content, $info) = $xoopsDB->fetchRow($result)) {
            $waiting_data['waiting']    = $waiting;
            $waiting_data['status']     = $status;
            $waiting_data['content']    = $content;
            $waiting_data['booking_id'] = $booking_id;
            $waiting_data['uid']        = $uid;
            $waiting_data['info']       = json_decode($info, true);

            if ($last_index == 'waiting') {
                $booking_arr[$booking_date][$section_id][$waiting] = $waiting_data;
            } else {
                $booking_arr[$booking_date][$section_id][$uid] = $waiting_data;
            }
            if (! isset($ok_booking[$booking_date][$section_id]) && $status == 1) {
                $ok_booking[$booking_date][$section_id] = $waiting_data;
            }
        }

        return [$booking_arr, $ok_booking];
    }
    //立即寄出
    public static function send_now($email = "", $title = "", $content = "")
    {
        global $xoopsModuleConfig;
        $msg = '';
        if ($xoopsModuleConfig['can_send_mail']) {
            $xoopsMailer = &getMailer();

            $xoopsMailer->multimailer->ContentType = 'text/html';
            $xoopsMailer->addHeaders('MIME-Version: 1.0');

            $msg .= ($xoopsMailer->sendMail($email, $title, $content, [])) ? "已寄發通知信給 {$email}" : "通知信寄發給 {$email} 失敗！";
        } else {
            $msg = '目前設定為不寄發通知，請設法告知預約者預約結果';
        }
        return $msg;
    }

    public static function last_Sunday()
    {
        $today = new \DateTime();

        // 計算最近一次的星期日
        if ($today->format('N') != 7) {
            $today->modify('last Sunday');
        }
        return $today->format('Y-m-d');
    }
    public static function next_Saturday()
    {
        $today = new \DateTime();

        // 計算接下來最近的星期六
        if ($today->format('N') != 6) {
            $today->modify('next Saturday');
        }
        return $today->format('Y-m-d');
    }

    // 計算 N 週後的日期
    public static function end_date($week = 0, $return_timestamp = false)
    {
        $today = new \DateTime();
        $today->modify("+{$week} weeks");

        return $return_timestamp ? $today->getTimestamp() : $today->format('Y-m-d');
    }

    /**
     * 找出日期範圍內符合指定星期幾的日期，並以星期幾（數字）為索引返回
     *
     * @param string $startDate 開始日期（格式：YYYY-MM-DD）
     * @param string $endDate 結束日期（格式：YYYY-MM-DD）
     * @param array $targetDays 目標星期幾的數字陣列（0=星期日，1-6=星期一至六）
     * @return array 以星期幾（數字）為索引的日期列表（格式：YYYY-MM-DD）
     */
    public static function findDatesByDaysOfWeekGrouped($startDate, $endDate, $targetDays)
    {
        // 將輸入的日期轉換為 DateTime 物件
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);
        // 初始化結果陣列，以星期幾（數字）為鍵
        $result = array_fill_keys($targetDays, []);
        // 遍歷日期範圍
        while ($start <= $end) {
            // 獲取當前日期的星期幾（w格式：0=星期日，1-6=星期一至六）
            $dayOfWeek = $start->format("w");
            // 檢查當前日期是否在目標星期幾中
            if (in_array($dayOfWeek, $targetDays)) {
                // 將日期添加到對應的星期幾鍵值下
                $result[$dayOfWeek][] = $start->format("Y-m-d");
            }
            // 增加一天
            $start->add(new \DateInterval("P1D"));
        }
        return $result;
    }

    public static function findDatesByTargetDay($targetDay)
    {
        $data = [];
        // 获取当前日期和星期几
        $selected_date = isset($targetDay) ? new \DateTime($targetDay) : new \DateTime();
        $week          = $selected_date->format('w'); // 0 (Sunday) to 6 (Saturday)
        $startOfWeek   = clone $selected_date;
        $startOfWeek->modify('-' . $week . ' days');

        // 生成本周的日期
        $week_dates = [];
        for ($i = 0; $i < 7; $i++) {
            $week_dates[] = $startOfWeek->format('Y-m-d');
            $startOfWeek->modify('+1 day');
        }

        // 計算上一週和下一週的起始日期
        $prev_week_start = clone $selected_date;
        $prev_week_start->modify('-7 days')->modify('-' . $week . ' days')->format('Y-m-d');

        $next_week_start = clone $selected_date;
        $next_week_start->modify('+7 days')->modify('-' . $week . ' days')->format('Y-m-d');

        $data['week_dates']         = $week_dates;
        $data['prev_week_start']    = $prev_week_start->format('Y-m-d');
        $data['next_week_start']    = $next_week_start->format('Y-m-d');
        $data['next_week_start_ts'] = $next_week_start->getTimestamp();
        $data['selected_date']      = $selected_date->format('Y-m-d');
        $data['tomorrow']           = strtotime('+1 day');
        $data['today']              = strtotime('today');
        $data['chinese_week']       = Tad_booking_section::$chinese_week;
        return $data;
    }
}
