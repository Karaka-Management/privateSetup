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
<nav id="nav-side">
    <div id="nav-side-outer" class="oms-ui-state">
        <ul id="nav-side-inner" class="nav" role="navigation">
            <li>
                <ul>
                    <li>
                        <label for="nav-admin">
                            <i class=""></i>
                            <span><?= $this->getHtml('Admin', '0', '0'); ?></span>
                        </label>
                    </li>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'admin'
                        && $this->request->uri->getPathElement(1) === 'organizations'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>admin/organizations"><?= $this->getHtml('Organizations', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'admin'
                        && $this->request->uri->getPathElement(1) === 'users'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>admin/users"><?= $this->getHtml('Users', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'admin'
                        && $this->request->uri->getPathElement(1) === 'resources'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>admin/resources"><?= $this->getHtml('Resources', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'admin'
                        && $this->request->uri->getPathElement(1) === 'bills'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>admin/bills"><?= $this->getHtml('Bills', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'admin'
                        && $this->request->uri->getPathElement(1) === 'logs'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>admin/logs"><?= $this->getHtml('Logs', '0', '0'); ?></a>
                </ul>
            </li>
            <li>
                <ul>
                    <li>
                        <label for="nav-org">
                            <i class=""></i>
                            <span><?= $this->getHtml('Organization', '0', '0'); ?></span>
                        </label>
                    </li>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'organization'
                        && $this->request->uri->getPathElement(1) === 'settings'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>organization/settings"><?= $this->getHtml('Settings', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'organization'
                        && $this->request->uri->getPathElement(1) === 'users'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>organization/users"><?= $this->getHtml('Users', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'organization'
                        && $this->request->uri->getPathElement(1) === 'resources'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>organization/resources"><?= $this->getHtml('Resources', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'organization'
                        && $this->request->uri->getPathElement(1) === 'bills'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>organization/bills"><?= $this->getHtml('Bills', '0', '0'); ?></a>
                </ul>
            </li>
            <li>
                <ul>
                    <li>
                        <label for="nav-home">
                            <i class=""></i>
                            <span><?= $this->getHtml('Home', '0', '0'); ?></span>
                        </label>
                    </li>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === ''
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/');
                            ?>dashboard"><?= $this->getHtml('Dashboard', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'user'
                        && $this->request->uri->getPathElement(1) === 'settings'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>user/settings"><?= $this->getHtml('Settings', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'user'
                        && $this->request->uri->getPathElement(1) === 'resources'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>user/resources"><?= $this->getHtml('Resources', '0', '0'); ?></a>
                    <li><a class="<?= $this->request->uri->getPathElement(0) === 'user'
                        && $this->request->uri->getPathElement(1) === 'reports'
                            ? 'active' : '';
                        ?>" href="<?= UriFactory::build('{/lang}/{/app}/'); ?>user/reports"><?= $this->getHtml('Reports', '0', '0'); ?></a>
                </ul>
            </li>
            <li>
                <ul>
                    <li>
                        <label for="nav-legal">
                            <i class=""></i>
                            <span><?= $this->getHtml('Legal', '0', '0'); ?></span>
                        </label>
                    </li>
                    <li><a href="<?= UriFactory::build('{/lang}/{/app}/'); ?>privacy"><?= $this->getHtml('PrivacyPolicy', '0', '0'); ?></a>
                    <li><a href="<?= UriFactory::build('{/lang}/{/app}/'); ?>terms"><?= $this->getHtml('Terms', '0', '0'); ?></a>
                    <li><a href="<?= UriFactory::build('{/lang}/{/app}/'); ?>imprint"><?= $this->getHtml('Imprint', '0', '0'); ?></a>
                </ul>
            </li>
        </ul>
    </div>
</nav>