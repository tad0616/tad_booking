<?php


defined('XOOPS_ROOT_PATH') || die('Restricted access.');

/**
 * Class Tad_bookingCorePreload
 */
class Tad_bookingCorePreload extends XoopsPreloadItem
{
    // to add PSR-4 autoloader

    /**
     * @param $args
     */
    public static function eventCoreIncludeCommonEnd($args)
    {
        require __DIR__ . '/autoloader.php';
    }
}
