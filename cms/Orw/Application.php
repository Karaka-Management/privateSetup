<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Applications\Backend
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Web\{APPNAME};

use Model\CoreSettings;
use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Admin\Models\NullAccount as ModelsNullAccount;
use phpOMS\Account\Account;
use phpOMS\Account\NullAccount;
use phpOMS\Account\AccountManager;
use phpOMS\Asset\AssetType;
use phpOMS\Auth\Auth;
use phpOMS\DataStorage\Cache\CachePool;
use phpOMS\DataStorage\Cookie\CookieJar;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Database\DatabaseStatus;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Model\Html\Head;
use phpOMS\Router\RouteStatus;
use phpOMS\Router\WebRouter;
use phpOMS\Uri\UriFactory;
use phpOMS\Utils\Parser\Markdown\Markdown;
use Web\WebApplication;
use Web\{APPNAME}\AppView;

/**
 * Application class.
 *
 * @package Web\{APPNAME}
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class Application
{
    /**
     * WebApplication.
     *
     * @var WebApplication
     * @since 1.0.0
     */
    private WebApplication $app;

    /**
     * Temp config.
     *
     * @var array{db:array{core:array{masters:array{select:array{db:string, host:string, port:int, login:string, password:string, database:string}}}}, log:array{file:array{path:string}}, app:array{path:string, default:array{id:string, app:string, org:int, lang:string}, domains:array}, page:array{root:string, https:bool}, language:string[]}
     * @since 1.0.0
     */
    private array $config;

    /**
     * Constructor.
     *
     * @param WebApplication                                                                                                                                                                                                                                                                                                                            $app    WebApplication
     * @param array{db:array{core:array{masters:array{select:array{db:string, host:string, port:int, login:string, password:string, database:string}}}}, log:array{file:array{path:string}}, app:array{path:string, default:array{id:string, app:string, org:int, lang:string}, domains:array}, page:array{root:string, https:bool}, language:string[]} $config Application config
     *
     * @since 1.0.0
     */
    public function __construct(WebApplication $app, array $config)
    {
        $this->app          = $app;
        $this->app->appName = '{APPNAME}';
        $this->config       = $config;
        UriFactory::setQuery('/app', \strtolower($this->app->appName));
    }

    public function run(HttpRequest $request, HttpResponse $response) : void
    {
        $this->app->l11nManager    = new L11nManager($this->app->appName);
        $this->app->dbPool         = new DatabasePool();
        $this->app->sessionManager = new HttpSession(0);
        $this->app->cookieJar      = new CookieJar();
        $this->app->dispatcher     = new Dispatcher($this->app);

        $this->app->dbPool->create('select', $this->config['db']['core']['masters']['select']);

        $this->app->router = new WebRouter($this->app);
        $this->app->router->importFromFile(__DIR__ . '/Routes.php');
        $this->app->router->importFromFile(__DIR__ . '/../../Routes.php');

        /* CSRF token OK? */
        if ($request->getData('CSRF') !== null
            && !\hash_equals($this->app->sessionManager->get('CSRF'), $request->getData('CSRF'))
        ) {
            $response->header->status = RequestStatusCode::R_403;

            return;
        }

        /** @var \phpOMS\DataStorage\Database\Connection\ConnectionAbstract $con */
        $con = $this->app->dbPool->get();
        DataMapperFactory::db($con);

        $this->app->cachePool      = new CachePool();
        $this->app->appSettings    = new CoreSettings();
        $this->app->eventManager   = new EventManager($this->app->dispatcher);
        $this->app->accountManager = new AccountManager($this->app->sessionManager);
        $this->app->l11nServer     = LocalizationMapper::get()->where('id', 1)->execute();
        $this->app->orgId          = $this->getApplicationOrganization($request, $this->config['app']);

        $aid                       = Auth::authenticate($this->app->sessionManager);
        $request->header->account  = $aid;
        $response->header->account = $aid;

        $account = $this->loadAccount($aid);

        if (!($account instanceof NullAccount)) {
            $response->header->l11n = $account->l11n;
        } elseif ($this->app->sessionManager->get('language') !== null) {
            $response->header->l11n
                ->loadFromLanguage(
                    $this->app->sessionManager->get('language'),
                    $this->app->sessionManager->get('country') ?? '*'
                );
        } elseif ($this->app->cookieJar->get('language') !== null) {
            $response->header->l11n
                ->loadFromLanguage(
                    $this->app->cookieJar->get('language'),
                    $this->app->cookieJar->get('country') ?? '*'
                );
        }

        if (!\in_array($response->getLanguage(), $this->config['language'])) {
            $response->header->l11n->setLanguage($this->app->l11nServer->getLanguage());
        }

        $pageView = new AppView($this->app->l11nManager, $request, $response);
        $head     = new Head();

        $pageView->setData('head', $head);
        $response->set('Content', $pageView);

        /* Backend only allows GET */
        if ($request->getMethod() !== RequestMethod::GET) {
            $this->create406Response($response, $pageView);

            return;
        }

        /* Database OK? */
        if ($this->app->dbPool->get()->getStatus() !== DatabaseStatus::OK) {
            $this->create503Response($response, $pageView);

            return;
        }

        UriFactory::setQuery('/lang', $response->getLanguage());

        $response->header->set('content-language', $response->getLanguage(), true);

        $dispatched = $this->routeDispatching($request, $response, $account, $head, $pageView);
        $pageView->addData('dispatch', $dispatched);
    }

    private function getApplicationOrganization(HttpRequest $request, array $config) : int
    {
        return (int) (
            $request->getData('u') ?? (
                $config['domains'][$request->uri->host]['org'] ?? $config['default']['org']
            )
        );
    }

    private function routeDispatching(
        HttpRequest $request,
        HttpResponse $response,
        Account $account,
        Head $head,
        AppView $pageView
    ) : array
    {
        $routes = $this->app->router->route(
            $request->uri->getRoute(),
            $request->getData('CSRF'),
            $request->getRouteVerb(),
            $this->app->appName,
            $this->app->orgId,
            $account,
            $request->getData()
        );

        if ($routes === ['dest' => RouteStatus::INVALID_CSRF]
            || $routes === ['dest' => RouteStatus::INVALID_PERMISSIONS]
            || $routes === ['dest' => RouteStatus::INVALID_DATA]
        ) {
            $this->initResponseHeadFrontend($head, $request, $response);
            $this->createDefaultPageViewFrontend($request, $response, $pageView);

            return $this->app->dispatcher->dispatch(
                $this->app->router->route(
                    '/' . \strtolower($this->app->appName) . '/e403',
                    $request->getData('CSRF'),
                    $request->getRouteVerb()
                ),
                $request, $response);
        } elseif ($routes === ['dest' => RouteStatus::NOT_LOGGED_IN]) {
            $this->initResponseHeadFrontend($head, $request, $response);
            $this->createDefaultPageViewFrontend($request, $response, $pageView);

            $this->app->loadLanguageFromPath(
                $response->getLanguage(),
                __DIR__ . '/lang/frontend.' . $response->getLanguage() . '.lang.php'
            );

            return $this->app->dispatcher->dispatch(
                ['dest' => '\Web\{APPNAME}\Controller\FrontendController:signinView'],
                $request, $response);
        } else {
            if (isset($routes[0]['dest']) && \stripos($routes[0]['dest'], '\Controller\BackendController') !== false) {
                $this->initResponseHeadBackend($head, $request, $response);
                $this->createDefaultPageViewBackend($request, $response, $pageView);

                $this->app->loadLanguageFromPath(
                    $response->getLanguage(),
                    __DIR__ . '/lang/backend.' . $response->getLanguage() . '.lang.php'
                );
            } else {
                $this->initResponseHeadFrontend($head, $request, $response);
                $this->createDefaultPageViewFrontend($request, $response, $pageView);

                $this->app->loadLanguageFromPath(
                    $response->getLanguage(),
                    __DIR__ . '/lang/frontend.' . $response->getLanguage() . '.lang.php'
                );
            }

            return $this->app->dispatcher->dispatch($routes, $request, $response);
        }
    }

    private function createDefaultPageViewFrontend(HttpRequest $request, HttpResponse $response, AppView $pageView) : void
    {
        $pageView->setTemplate('/Web/{APPNAME}/frontend');
    }

    private function createDefaultPageViewBackend(HttpRequest $request, HttpResponse $response, AppView $pageView) : void
    {
        $pageView->setTemplate('/Web/{APPNAME}/backend');
    }

    private function create406Response(HttpResponse $response, AppView $pageView) : void
    {
        $response->header->status = RequestStatusCode::R_406;
        $pageView->setTemplate('/Web/{APPNAME}/Error/406');
        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/Error/lang/' . $response->getLanguage() . '.lang.php'
        );
    }

    private function create503Response(HttpResponse $response, AppView $pageView) : void
    {
        $response->header->status = RequestStatusCode::R_503;
        $pageView->setTemplate('/Web/{APPNAME}/Error/503');
        $this->app->loadLanguageFromPath(
            $response->getLanguage(),
            __DIR__ . '/Error/lang/' . $response->getLanguage() . '.lang.php'
        );
    }

    private function loadAccount(int $uid) : Account
    {
        /** @var Account $account */
        $account = AccountMapper::getWithPermissions($uid);

        if ($account instanceof ModelsNullAccount) {
            $account = new NullAccount();
        }

        $this->app->accountManager->add($account);

        return $account;
    }

    private function initResponseHeadFrontend(Head $head, HttpRequest $request, HttpResponse $response) : void
    {
        /* Load assets */
        $head->addAsset(AssetType::CSS, 'Resources/fonts/fontawesome/css/font-awesome.min.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/linearicons/css/style.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/lineicons/css/lineicons.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'cssOMS/styles.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/Roboto/roboto.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Web/{APPNAME}/css/frontend.css?v=1.0.0');

        // Framework
        $head->addAsset(AssetType::JS, 'jsOMS/Utils/oLib.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'jsOMS/UnhandledException.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'Web/{APPNAME}/js/frontend.js?v=1.0.0', ['type' => 'module']);

        $script = '';
        $response->header->set(
            'content-security-policy',
            'base-uri \'self\'; script-src \'self\' blob: \'sha256-'
            . \base64_encode(\hash('sha256', $script, true))
            . '\'; worker-src \'self\'',
            true
        );

        if ($request->hasData('debug')) {
            $head->addAsset(AssetType::CSS, 'cssOMS/debug.css?v=1.0.0');
            \phpOMS\DataStorage\Database\Query\Builder::$log = true;
        }

        $css = \file_get_contents(__DIR__ . '/css/frontend-small.css');
        if ($css === false) {
            $css = '';
        }

        $css = \preg_replace('!\s+!', ' ', $css);
        $head->setStyle('core', $css ?? '');
        $head->title = 'Online Resource Watcher';
    }

    private function initResponseHeadBackend(Head $head, HttpRequest $request, HttpResponse $response) : void
    {
        /* Load assets */
        $head->addAsset(AssetType::CSS, 'Resources/fonts/fontawesome/css/font-awesome.min.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/linearicons/css/style.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/lineicons/css/lineicons.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'cssOMS/styles.css?v=1.0.0');
        $head->addAsset(AssetType::CSS, 'Resources/fonts/Roboto/roboto.css?v=1.0.0');

        // Framework
        $head->addAsset(AssetType::JS, 'jsOMS/Utils/oLib.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'jsOMS/UnhandledException.js?v=1.0.0');
        $head->addAsset(AssetType::JS, 'Web/{APPNAME}/js/backend.js?v=1.0.0', ['type' => 'module']);

        $script = '';
        $response->header->set(
            'content-security-policy',
            'base-uri \'self\'; script-src \'self\' blob: \'sha256-'
            . \base64_encode(\hash('sha256', $script, true))
            . '\'; worker-src \'self\'',
            true
        );

        if ($request->hasData('debug')) {
            $head->addAsset(AssetType::CSS, 'cssOMS/debug.css?v=1.0.0');
            \phpOMS\DataStorage\Database\Query\Builder::$log = true;
        }

        $css = \file_get_contents(__DIR__ . '/css/backend-small.css');
        if ($css === false) {
            $css = '';
        }

        $css = \preg_replace('!\s+!', ' ', $css);
        $head->setStyle('core', $css ?? '');
        $head->title = 'Online Resource Watcher';
    }
}
