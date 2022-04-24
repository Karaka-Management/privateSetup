<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Workflow\Controller
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Workflow\Controller;

use Modules\Admin\Models\NullAccount;
use Modules\Workflow\Models\NullWorkflowTemplate;
use Modules\Workflow\Models\WorkflowControllerInterface;
use Modules\Workflow\Models\WorkflowInstanceAbstract;
use Modules\Workflow\Models\WorkflowTemplate;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * OMS export class
 *
 * @package Modules\Workflow\Controller
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class WorkflowController implements WorkflowControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createInstanceFromRequest(RequestAbstract $request, WorkflowTemplate $template) : WorkflowInstanceAbstract
    {
        $instance            = new WorkflowInstanceAbstract();
        $instance->createdBy = new NullAccount($request->header->account);
        $instance->title     = $template->name . ': ' . ($request->getData('title') ?? ''); // @todo: create based on format
        $instance->template  = new NullWorkflowTemplate($request->getData('template') ?? 0);

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstanceListFromRequest(RequestAbstract $request) : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function createTemplateViewFromRequest(View $view, RequestAbstract $request, ResponseAbstract $response) : void
    {
        $includes = 'Modules/Media/Files/';
        $tpl = $view->getData('template')->source->findFile('template-profile.tpl.php');

        $start = \stripos($tpl->getPath(), $includes);

        $view->setTemplate('/' . \substr($tpl->getPath(), $start, -8));
    }

    /**
     * {@inheritdoc}
     */
    public function createInstanceViewFromRequest(View $view, RequestAbstract $request, ResponseAbstract $response) : void
    {
        $includes = 'Modules/Media/Files/';
        $tpl = $view->getData('template')->source->findFile('instance-profile.tpl.php');

        $start = \stripos($tpl->getPath(), $includes);

        $view->setTemplate('/' . \substr($tpl->getPath(), $start, -8));
    }

    /**
     * {@inheritdoc}
     */
    public function apiChangeState(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createInstanceDbModel(WorkflowInstanceAbstract $instance) : void
    {

    }
}