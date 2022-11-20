<?php
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>

<nav>
    <ul id="topnav">
        <li><a href="<?= UriFactory::build('{/lang}/{/app}'); ?>"><?= $this->getHtml('Home', '0', '0'); ?></a></li>
        <li><a href="<?= UriFactory::build('{/lang}/{/app}/features'); ?>"><?= $this->getHtml('Features', '0', '0'); ?></a></li>
        <li><a href="<?= UriFactory::build('{/lang}/{/app}/pricing'); ?>"><?= $this->getHtml('Pricing', '0', '0'); ?></a></li>
    </ul>
    <ul id="toplogin">
        <li><a id="signinButton" href="<?= UriFactory::build('{/lang}/{/app}/signin'); ?>"><?= $this->getHtml('SignIn', '0', '0'); ?></a></li>
        <li><a id="signupButton" href="<?= UriFactory::build('{/lang}/{/app}/signup'); ?>"><?= $this->getHtml('SignUp', '0', '0'); ?></a></li>
    </ul>
</nav>
