<?php
declare(strict_types=1);

namespace Web\{APPNAME}\Controller;

use Modules\Auditor\Models\AuditMapper;
use phpOMS\Asset\AssetType;
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

final class FrontendController extends ModuleAbstract
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

    public function frontView(RequestAbstract $request, ResponseAbstract $response, $data = null): void
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Web/{APPNAME}/css/front.css?v=1.0.0');
    }

    public function solutionsView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/solutions');

        return $view;
    }

    public function servicesView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/services');

        return $view;
    }

    public function shopView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Web/{APPNAME}/css/shop.css?v=1.0.0');

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/shop');

        return $view;
    }

    public function infoView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/info');

        return $view;
    }

    public function contactView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/contact');

        return $view;
    }

    public function imprintView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/markdown');

        return $view;
    }

    public function privacyView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/markdown');

        return $view;
    }

    public function termsView(RequestAbstract $request, ResponseAbstract $response, $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/markdown');

        return $view;
    }
}
