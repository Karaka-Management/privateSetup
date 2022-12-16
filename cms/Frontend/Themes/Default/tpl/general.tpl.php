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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var array $dispatch */
$dispatch = $this->getData('dispatch') ?? [];
?>
<!DOCTYPE HTML>
<html lang="<?= $this->printHtml($this->response->getLanguage()); ?>">
    <head>
        <?php include __DIR__ . '/head.tpl.php'; ?>
    </head>
    <body>
        <header>
            <?php include __DIR__ . '/nav.tpl.php'; ?>
        </header>
        <main>
            <?php
                $c = 0;
                foreach ($dispatch as $view) {
                    if (!($view instanceof \phpOMS\Views\NullView)
                        && $view instanceof \phpOMS\Contract\RenderableInterface
                    ) {
                        ++$c;
                        echo $view->render();
                    }
                }
            ?>
        </main>

        <?php include __DIR__ . '/footer.tpl.php'; ?>
    </body>
</html>