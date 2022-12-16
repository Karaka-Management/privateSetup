<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Web\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>
<header>
    <a id="logo" href="<?= UriFactory::build('{/lang}/{/app}/{/prefix}'); ?>">
        <span><img alt="<?= $this->getHtml('Logo', '0', '0'); ?>" src="Web/{APPNAME}/img/logo.png" width="40px"></span>
        <span id="logo-name">Jingga</span>
    </a>
    <?php include __DIR__ . '/backend-nav-top.tpl.php'; ?>
</header>