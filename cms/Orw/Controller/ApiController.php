<?php

declare(strict_types=1);

namespace Web\{APPNAME}\Controller;

use Models\AccountMapper;
use phpOMS\Auth\LoginReturnType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\Notify;
use phpOMS\Model\Message\NotifyType;
use phpOMS\Model\Message\Reload;
use phpOMS\System\MimeType;
use WebApplication;

class ApiController
{
    private WebApplication $app;

    public function __construct(WebApplication $app = null)
    {
        $this->app = $app;
    }

    public function apiLogin(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $login = AccountMapper::login((string) ($request->getData('user') ?? ''), (string) ($request->getData('pass') ?? ''));

        if ($login >= LoginReturnType::OK) {
            $this->app->sessionManager->set('UID', $login, true);
            $this->app->sessionManager->save();
            $response->set($request->uri->__toString(), new Reload());
        } else {
            $response->set($request->uri->__toString(), new Notify(
                'Login failed due to wrong login information',
                NotifyType::INFO
            ));
        }
    }

    public function apiLogout(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);

        $this->app->sessionManager->remove('UID');
        $this->app->sessionManager->save();

        $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
        $response->set($request->uri->__toString(), [
            'status'   => NotificationLevel::OK,
            'title'    => 'Logout successfull',
            'message'  => 'You are redirected to the login page',
            'response' => null,
        ]);
    }
}
