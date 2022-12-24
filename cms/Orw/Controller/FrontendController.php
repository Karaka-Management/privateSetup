<?php

declare(strict_types=1);

namespace Web\{APPNAME}\Controller;

use Modules\Auditor\Models\AuditMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Views\View;
use phpOMS\Utils\Parser\Markdown\Markdown;
use Web\Backend\Views\TableView;
use Modules\CMS\Models\PageMapper;
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

    public function frontView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-front');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_data_processing_yrrv.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'frontpage')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->setData('content', $page->getL11n('front')->content);

        return $view;
    }

    public function featureView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-features');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_scrum_board_re_wk7v.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'features')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->setData('content', $page->getL11n('features')->content);

        return $view;
    }

    public function pricingView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-pricing');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_discount_d-4-bd.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'pricing')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $view->setData('content', $page->getL11n('pricing')->content);

        return $view;
    }

    public function signupView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-signup');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_access_account_re_8spm.svg');

        return $view;
    }

    public function signinView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-signin');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_access_account_re_8spm.svg');

        return $view;
    }

    public function imprintView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-default');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_team_spirit_re_yl1v.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'imprint')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $markdown = Markdown::parse($page->getL11n('imprint')->content);

        $view->setData('text', $markdown);

        return $view;
    }

    public function termsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-default');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_agreement_re_d4dv.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'terms')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $markdown = Markdown::parse($page->getL11n('terms')->content);

        $view->setData('text', $markdown);

        return $view;
    }

    public function privacyView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-default');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_gdpr_-3-xfb.svg');

        $page = PageMapper::get()
            ->with('l11n')
            ->where('app', $this->app->appId)
            ->where('name', 'privacy')
            ->where('l11n/language', $response->getLanguage())
            ->execute();

        $markdown = Markdown::parse($page->getL11n('privacy')->content);

        $view->setData('text', $markdown);

        return $view;
    }

    public function contactView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Web/{APPNAME}/tpl/frontend-contact');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_profile_details_re_ch9r.svg');

        return $view;
    }
}
