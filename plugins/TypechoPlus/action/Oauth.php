<?php

require_once __DIR__ . '/oauth/Github.php';

use Typecho\Cookie;
use Utils\Helper;
use Widget\Options;

trait TypechoPlus_Action_Oauth
{
    use TypechoPlus_Action_Oauth_Github;

    /**
     * 鉴权 url
     * @return string
     */
    public static function getOauthUrl($type)
    {
        $security = Helper::security();

        return $security->getIndex('/oauth?type=' . $type);
    }

    /**
     * 回调 url
     * @param $type
     */
    public static function getCallbackUrl($type)
    {
        $security = Helper::security();
        $security->protect();

        return $security->getIndex('/callback?type=' . $type);
    }

    /**
     * 鉴权
     */
    public static function oauth()
    {
        $options = Options::alloc();

        $type = $options->request->get('type');
        switch ($type) {
            case 'github':
                self::oauthGithub();
                break;
            default:
                $options->response->redirect($options->adminUrl);
                break;
        }
    }

    /**
     * 回调
     */
    public static function callback()
    {
        $options = Options::alloc();

        $type = $options->request->get('type');
        switch ($type) {
            case 'github':
                self::callbackGithub();
                break;
            default:
                $options->response->redirect($options->adminUrl);
                break;
        }
    }


    /**
     * 跳转
     * @param bool $hasLogin
     * @param string $name
     * @param string $emial
     */
    public static function redirect($hasLogin = false, $name = '', $emial = '')
    {
        $options = Options::alloc();

        if ($hasLogin) {
            /** 跳转验证后地址 */
            if (!empty($options->request->referer)) {
                /** fix #952 & validate redirect url */
                if (
                    0 === strpos($options->request->referer, $options->adminUrl)
                    || 0 === strpos($options->request->referer, $options->siteUrl)
                ) {
                    $options->response->redirect($options->request->referer);
                }
            } elseif (!$options->user->pass('contributor', true)) {
                /** 不允许普通用户直接跳转后台 */
                $options->response->redirect($options->profileUrl);
            }

            $options->response->redirect($options->adminUrl);
        }

        if (!$options->allowRegister) {
            self::msgNotice(_t('禁止注册'));
        }

        Cookie::set('__typecho_remember_name', $name);
        Cookie::set('__typecho_remember_mail', $emial);

        $options->response->redirect($options->registerUrl);
    }
}
