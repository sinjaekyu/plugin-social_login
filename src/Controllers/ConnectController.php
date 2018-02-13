<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * PHP version 5
 *
 * @category
 * @package     Xpressengine\
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Plugins\SocialLogin\Controllers;

use App\Http\Controllers\Controller;
use XePresenter;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\SocialLogin\Plugin;

/**
 * @category    SocialLogin
 * @package     Xpressengine\Plugins\SocialLogin\Controllers
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
class ConnectController extends Controller
{
    protected $plugin = null;

    /**
     * SocialLoginController constructor.
     *
     * @param \Xpressengine\Plugins\SocialLogin\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function connect(Request $request, $provider)
    {
        $auth = $this->plugin->getAuthenticator($provider);

        $param = $auth->getCallbackParameter();

        $hasCode = $request->has($param);
        return $auth->execute($hasCode);
    }

    public function disconnect(Request $request, $provider)
    {
        // execute auth
        $namespace = 'Xpressengine\\Plugins\\SocialLogin\\Authenticators\\';
        $className = $namespace.studly_case($provider).'Auth';
        $auth = new $className($provider);
        $param = $auth->getCallbackParameter();

        $auth->disconnect();

        return redirect()->back()->with(
            'alert',
            [
                'type' => 'success',
                'message' => '연결해제 되었습니다'
            ]
        );
    }

    public function login()
    {
        return XePresenter::make('social_login::views.login', ['providers' => $this->getEnabledProviders()]);
    }

    public function register()
    {
        return XePresenter::make('social_login::views.register', ['providers' => $this->getEnabledProviders()]);
    }

    protected function getEnabledProviders()
    {
        if (!$config = app('xe.config')->get('social_login')) {
            return [];
        }

        return collect($config->get('providers'))->filter(function ($info) {
            return array_get($info, 'activate') === true;
        });
    }
}
