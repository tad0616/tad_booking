<?php


require __DIR__ . '/header.php';

$adminObject = \Xmf\Module\Admin::getInstance();

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

require __DIR__ . '/footer.php';
xoops_cp_footer();
