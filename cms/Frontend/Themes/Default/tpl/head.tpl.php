<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Web\Frontend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var phpOMS\Model\Html\Head $head */
$head = $this->getData('head');
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#343a40">
<meta name="msapplication-navbutton-color" content="#343a40">
<meta name="apple-mobile-web-app-status-bar-style" content="#343a40">
<meta name="description" content="<?= $this->getHtml(':meta', '0', '0'); ?>">
<?= $head->meta->render(); ?>

<base href="<?= UriFactory::build('{/base}'); ?>/">

<title><?= $this->printHtml($head->title); ?></title>

<?= $head->renderAssets(); ?>

<style><?= $head->renderStyle(); ?></style>
<script><?= $head->renderScript(); ?></script>