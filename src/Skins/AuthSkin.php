<?php
/**
 *  This file is part of the Xpressengine package.
 *
 * @category    SocialLogin
 * @package     Xpressengine\Plugins\SocialLogin
 * @author      XE Developers (khongchi) <khongchi@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Crop. <http://www.navercorp.com>
 * @license     LGPL-2.1
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Plugins\SocialLogin\Skins;

use Illuminate\Contracts\Support\Renderable;
use App\Skins\Member\AuthSkin as CoreSkin;
use Xpressengine\Plugins\SocialLogin\Plugin;

/**
 * @category
 * @package     Xpressengine\Plugins\SocialLogin\Skins
 */
class AuthSkin extends CoreSkin
{
    protected static $id;
    protected static $componentInfo = [];

    /**
     * Html을 생성하여 반환한다.
     *
     * @return Renderable|string
     */
    protected function login()
    {
        /** @var Plugin $plugin */
        $plugin = app(Plugin::class);
        $providers = $plugin->getProviders();

        return view($plugin->view('views.login'), $this->data, compact('providers'));
    }

    protected function registerIndex()
    {
        return parent::registerIndex();
    }

    protected function registerCreate()
    {
        /** @var Plugin $plugin */
        $plugin = app(Plugin::class);
        $providers = $plugin->getProviders();

        $config = app('xe.config')->get('user.join');
        if ($config->get('guard_forced', false) || request()->has('token')) {
            return parent::registerCreate();
        }

        $use_email = app('request')->get('use_email', false);
        if ($use_email !== false) {
            return parent::registerCreate();
        }
        return view($plugin->view('views.create'), $this->data, compact('providers'));
    }

}
