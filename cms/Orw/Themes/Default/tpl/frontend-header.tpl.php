<?php
declare(strict_types=1);

use phpOMS\Uri\UriFactory;
?>

<header>
    <div class="floater">
        <a id="toplogo" href="<?= UriFactory::build('{/lang}/{/app}/{/lang}'); ?>">
            <img alt="Logo" src="Web/{APPNAME}/img/logo.png" width="40px">
            <span>Jingga</span>
        </a>

        <?php include __DIR__ . '/frontend-nav.tpl.php'; ?>
    </div>
</header>