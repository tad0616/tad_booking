<?php


require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/preloads/autoloader.php';
require_once XOOPS_ROOT_PATH . '/modules/tadtools/preloads/autoloader.php';

xoops_loadLanguage('main', $xoopsModule->getVar('dirname'));

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new \XoopsTpl();
}

xoops_cp_header();

// Define Stylesheet and JScript
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tadtools/css/iconize.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tadtools/css/font-awesome/css/font-awesome.css');
$xoTheme->addStylesheet(XOOPS_URL . "/modules/tadtools/css/xoops_adm{$_SESSION['bootstrap']}.css");
$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/css/module.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/css/admin.css');
