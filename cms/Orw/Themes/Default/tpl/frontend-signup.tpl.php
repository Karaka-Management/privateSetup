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
<div id="login-container">
    <div id="login-form">
        <h1><?= $this->getHtml('SignUp', '0', '0'); ?></h1>
        <form id="login" method="POST" action="<?= UriFactory::build('api/signup?{?}'); ?>">
            <label for="iName"><?= $this->getHtml('Username', '0', '0'); ?>:</label>
            <div class="inputWithIcon">
                <input id="iName" type="text" name="user" tabindex="1" value="" autocomplete="off" spellcheck="false" autofocus require>
                <i class="frontIcon fa fa-user fa-lg fa-fw" aria-hidden="true"></i>
                <i class="endIcon fa fa-times fa-lg fa-fw" aria-hidden="true"></i>
            </div>
            <label for="iEmail"><?= $this->getHtml('Email', '0', '0'); ?>:</label>
            <div class="inputWithIcon">
                <input id="iEmail" type="email" name="email" tabindex="2" value="" autocomplete="off" spellcheck="false" require>
                <i class="frontIcon fa fa-envelope fa-lg fa-fw" aria-hidden="true"></i>
                <i class="endIcon fa fa-times fa-lg fa-fw" aria-hidden="true"></i>
            </div>
            <label for="iPassword"><?= $this->getHtml('Password', '0', '0'); ?>:</label>
            <div class="inputWithIcon">
                <input id="iPassword" type="password" name="pass" tabindex="3" value="">
                <i class="frontIcon fa fa-lock fa-lg fa-fw" aria-hidden="true"></i>
                <i class="endIcon fa fa-times fa-lg fa-fw" aria-hidden="true"></i>
            </div>
            <input id="iSignupButton" name="signupButton" type="submit" value="<?= $this->getHtml('SignUp', '0', '0'); ?>" tabindex="4">
        </form>
    </div>
</div>