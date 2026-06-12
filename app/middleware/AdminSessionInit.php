<?php
declare(strict_types=1);

namespace app\middleware;

use Closure;
use think\App;
use think\Request;
use think\Response;
use think\Session;

class AdminSessionInit
{
    protected $app;

    protected $session;

    public function __construct(App $app, Session $session)
    {
        $this->app = $app;
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        $cookieName = 'ADMIN_SESSID';

        $this->session->setName($cookieName);
        $this->session->forgetDriver();

        $sessionId = $request->cookie($cookieName);
        if ($sessionId) {
            $this->session->setId($sessionId);
        }

        $this->session->init();

        $request->withSession($this->session);

        $response = $next($request);

        $response->setSession($this->session);

        $this->app->cookie->set($cookieName, $this->session->getId(), $this->session->getConfig('expire'));

        return $response;
    }

    public function end(Response $response)
    {
        $this->session->save();
    }
}