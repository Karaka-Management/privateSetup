<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Auditor
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View            $this
 * @var \Modules\Audit\Models\Audit[] $resources
 */
$resources = $this->getData('resources') ?? [];

$tableView            = $this->getData('tableView');
$tableView->id        = 'resourceList';
$tableView->baseUri   = '{/prefix}admin/audit/list';
$tableView->setObjects($resources);

$previous = $tableView->getPreviousLink(
    $this->request,
    empty($this->objects) || !$this->getData('hasPrevious') ? null : \reset($this->objects)
);

$next = $tableView->getNextLink(
    $this->request,
    empty($this->objects) ? null : \end($this->objects),
    $this->getData('hasNext') ?? false
);

?>
<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head">
                <?= $tableView->renderTitle(
                    $this->getHtml('Resources', '0', '0')
                ); ?>
            </div>
            <div class="slider">
            <table id="<?= $tableView->id; ?>" class="default sticky">
                <thead>
                <tr>
                    <td><?= $tableView->renderHeaderElement(
                        'id',
                        $this->getHtml('ID', '0', '0'),
                        'number'
					); ?>
					<td class="wf-100"><?= $tableView->renderHeaderElement(
                        'resource',
                        $this->getHtml('Resource', '0', '0'),
                        'text'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'user_org',
                        $this->getHtml('User', '0', '0'),
                        'number'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'user_name',
                        $this->getHtml('User', '0', '0'),
                        'text'
					); ?>
					<td><?= $tableView->renderHeaderElement(
                        'status',
                        $this->getHtml('Status', '0', '0'),
                        'text'
					); ?>
					<td><?= $tableView->renderHeaderElement(
                        'lastChecked',
                        $this->getHtml('Checked', '0', '0'),
                        'date'
                    ); ?>
                    <td><?= $tableView->renderHeaderElement(
                        'createdAt',
                        $this->getHtml('Date', '0', '0'),
                        'date'
                    ); ?>
                <tbody>
                <?php
                $count = 0;
                foreach ($resources as $key => $resource) : ++$count;
                    $url = UriFactory::build('{/lang}/{/app}/user/resource?id=' . $resource->getId()); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $resource->getId()); ?></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($resource->title); ?></a>
                        <td><a class="content" href="<?= UriFactory::build('{/lang}/{/app}/profile/single?{?}&for=' . $resource->organization->getId()); ?>"><?= $this->printHtml($this->renderUserName('%3$s %2$s %1$s', [$resource->organization->name1, $resource->organization->name2, $resource->organization->name3, $resource->organization->login ?? ''])); ?></a>
                        <td><a class="content" href="<?= UriFactory::build('{/lang}/{/app}/profile/single?{?}&for=' . $resource->owner->getId()); ?>"><?= $this->printHtml($this->renderUserName('%3$s %2$s %1$s', [$resource->owner->name1, $resource->owner->name2, $resource->owner->name3, $resource->owner->login ?? ''])); ?></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml((string) $resource->status); ?></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml(($resource->checkedAt?->format('Y-m-d H:i')) ?? ''); ?></a>
                        <td><a href="<?= $url; ?>"><?= $this->printHtml($resource->createdAt->format('Y-m-d H:i')); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                    <tr><td colspan="8" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <?php if ($this->getData('hasPrevious') || $this->getData('hasNext')) : ?>
            <div class="portlet-foot">
                <?php if ($this->getData('hasPrevious')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><i class="fa fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($this->getData('hasNext')) : ?>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><i class="fa fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>