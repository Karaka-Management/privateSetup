<?php

declare(strict_types=1);

namespace Web\{APPNAME}\Controller;

use Modules\Auditor\Models\AuditMapper;
use Modules\OnlineResourceWatcher\Models\ResourceMapper;
use Modules\Profile\Models\ProfileMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Views\View;
use phpOMS\Utils\Parser\Markdown\Markdown;
use Web\Backend\Views\TableView;
use WebApplication;

final class BackendController extends ModuleAbstract
{
	/**
     * Providing.
     *
     * @var string[]
     * @since 1.0.0
     */
    protected static array $providing = [];

    /**
     * Dependencies.
     *
     * @var string[]
     * @since 1.0.0
     */
    protected static array $dependencies = [];

    public function dashboardView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Web/{APPNAME}/tpl/backend-user-dashboard');

		return $view;
    }

    public function userResourceView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-user-resource');

        $resource = ResourceMapper::get()
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->setData('resource', $resource);

        return $view;

        return $view;
    }

    public function adminOrganizationsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-admin-organizations');

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminUsersView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-admin-users');

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-admin-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('organizationUserList-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('organizationUserList-f-' . $member . '-f1'),
                    'logic1' => $request->getData('organizationUserList-f-' . $member . '-o1'),
                    'value2' => $request->getData('organizationUserList-f-' . $member . '-f2'),
                    'logic2' => $request->getData('organizationUserList-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = ResourceMapper::getAll()
            ->with('owner')
            ->with('organization');

        $list   = ResourceMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('resources', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminBillsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-admin-bills');

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminLogsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-admin-logs');

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationSettingsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-organization-settings');

        return $view;
    }

    public function organizationUsersEditView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-organization-users-edit');

        return $view;
    }

    public function organizationUsersView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-organization-users');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('organizationUserList-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('organizationUserList-f-' . $member . '-f1'),
                    'logic1' => $request->getData('organizationUserList-f-' . $member . '-o1'),
                    'value2' => $request->getData('organizationUserList-f-' . $member . '-f2'),
                    'logic2' => $request->getData('organizationUserList-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = ProfileMapper::getAll()
            ->with('account')
            ->leftJoin('account/id', ResourceMapper::class, 'owner')
            ->execute();

        $list   = ProfileMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('users', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'OnlineResourceWatcher';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-organization-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationBillsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-organization-bills');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }


    public function userSettingsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-user-settings');

        return $view;
    }

    public function userResourcesCreateView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-user-resources-create');

        return $view;
    }

    public function userResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-user-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function userReportsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/backend-user-reports');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Web/{APPNAME}/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Web/{APPNAME}/Templates/header-element-table');
        $tableView->setFilterTemplate('/Web/{APPNAME}/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Web/{APPNAME}/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }
}
