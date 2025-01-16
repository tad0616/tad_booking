<?php
namespace XoopsModules\Tad_booking;

use Xmf\Request;
use XoopsModules\Tadtools\Wcag;



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

            } elseif (!isset($filter_arr['pass']) || !in_array($key, $filter_arr['pass'], true)) {
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
                $and_where_arr .= !is_string($col) ? " and {$value}" : " and {$prefix}`{$col}` = '" . $xoopsDB->escape($value) . "'";
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
        $before_sql = $order_sql = $after_sql = '';
        $before_items = $order_items = $after_items = [];
        if ($order_arr) {
            foreach ($order_arr as $col => $asc) {
                if ($col === 'before order') {
                    $before_items[] = $asc;
                } elseif ($col === 'after order') {
                    $after_items[] = $asc;
                } elseif (!is_string($col) or empty($col)) {
                    $order_items[] = $asc;
                } else {
                    $order_items[] = "{$prefix}`{$col}` $asc";
                }
            }

            $before_sql = empty($before_items) ? '' : implode(',', $before_items);
            $order_sql = empty($order_items) ? '' : "ORDER BY " . implode(',', $order_items);
            $after_sql = empty($after_items) ? '' : implode(',', $after_items);
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
    public static function chk_is_adm($other = '', $id = '', $file = '', $line = '')
    {
        $id = (int) $id;
        $file = str_replace('\\', '/', $file);
        if ($_SESSION['tad_booking_adm'] || ($other != '' && $_SESSION[$other]) || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            if (!empty($id) && $_SESSION[$other]) {
                if (in_array($id, $_SESSION[$other]) || $id == $_SESSION[$other]) {
                    return true;
                } else {
                    redirect_header($_SERVER['PHP_SELF'], 3, "您對筆資料 ($id) 無操作權限 {$file} ($line)");
                }}
        } else {
            redirect_header($_SERVER['PHP_SELF'], 3, "無操作權限 {$file} ($line)");
        }
    }

    //取得session
    public static function get_session()
    {
        global $xoopsUser;

        //判斷是否對該模組有管理權限
        if (!isset($_SESSION['tad_booking_adm'])) {
            $_SESSION['tad_booking_adm'] = isset($xoopsUser) && \is_object($xoopsUser) ? $xoopsUser->isAdmin() : false;
        }

        if (!isset($_SESSION['now_user'])) {
            $_SESSION['now_user'] = ($xoopsUser) ? $xoopsUser->toArray() : [];
        }

        if ($_SESSION['now_user']) {
            if (!isset($_SESSION['SchoolCode'])) {
                if ($_REQUEST['SchoolCode']) {
                    $_SESSION['SchoolCode'] = Request::getString('SchoolCode');
                } else {
                    $_SESSION['SchoolCode'] = ($xoopsUser) ? $xoopsUser->user_intrest() : false;
                }
            }
        }
    }
}
