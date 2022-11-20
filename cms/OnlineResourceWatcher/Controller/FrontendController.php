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

		return $view;
	}

	public function featureView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Web/{APPNAME}/tpl/frontend-features');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_scrum_board_re_wk7v.svg');

		return $view;
	}

	public function pricingView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Web/{APPNAME}/tpl/frontend-pricing');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_discount_d-4-bd.svg');

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

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../content/imprint.' . $lang . '.md')
			? __DIR__ . '/../content/imprint.' . $lang . '.md'
			: __DIR__ . '/../content/imprint.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

		$view->setData('text', $markdown);

		return $view;
	}

	public function termsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Web/{APPNAME}/tpl/frontend-default');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_agreement_re_d4dv.svg');

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../content/terms.' . $lang . '.md')
			? __DIR__ . '/../content/terms.' . $lang . '.md'
			: __DIR__ . '/../content/terms.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

		$view->setData('text', $markdown);

		return $view;
	}

	public function privacyView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Web/{APPNAME}/tpl/frontend-default');

        $pageView = $response->get('Content');
        $pageView->setData('headerSplash', 'undraw_gdpr_-3-xfb.svg');

		$lang = $request->getLanguage();

		$path = \is_file(__DIR__ . '/../content/privacy.' . $lang . '.md')
			? __DIR__ . '/../content/privacy.' . $lang . '.md'
			: __DIR__ . '/../content/privacy.en.md';

		$markdown = Markdown::parse(\file_get_contents($path));

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
