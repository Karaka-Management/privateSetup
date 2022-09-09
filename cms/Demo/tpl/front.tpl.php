<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Web\{APPNAME}
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

?>

<header>
    <div class="floater">
       <img src="<?= UriFactory::build('Web/{APPNAME}/img/logo.png'); ?>">
        <h1>Demo Application</h1>
        <h2>Simple application example for the CMS module.</h2>

        <hr>
    </div>
</header>

<div class="content">
    <div class="floater">
        <h1>Introduction</h1>
        <p>This is a simple demo application with the purpose of introducing some of the features the CMS module provides and how to use them. The content and implementation of this application can be inspected and modified in the backend. Feel free to <a href="<?= UriFactory::build('{/prefix}/backend/cms/application/list'); ?>">login</a> as admin in the backend and check out the CMS module.</p>

        <p>In this demo application you will learn in particular how to:</p>

        <ul>
           <li>Create an application</li>
           <li>Install an application </li>
           <li>Create and modify templates</li>
           <li>Create and modify content</li>
           <li>Create and modify the localization</li>
           <li>Create and modify navigation elements</li>
        </ul>

        <blockquote>A full documentation including guidelines for designers, users and software developers can be found in the CMS help documentation.</blockquote>

        <p>This application is designed to be simple in order to focus on the core features and functionality. More complex applications are also available and are sometimes even provided by some of the modules (e.g. Support, HumanResourceTimeRecording, ...)</p>
    </div>
</div>