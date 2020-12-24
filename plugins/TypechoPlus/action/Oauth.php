<?php

require_once __DIR__ . '/oauth/Github.php';

trait TypechoPlus_Action_Oauth
{
    use TypechoPlus_Action_Oauth_Github;

    /**
     * url
     * @return string
     */
    public function getOauthUrl($type)
    {
        return $this->options->siteUrl . '/oauth?type=' . $type;
    }

    /**
     * 鉴权
     */
    public function oauth()
    {
        $type = $this->request->get('type');
        switch ($type) {
            case 'github':
                $this->oauthGithub();
                break;
            default:
                $this->response->redirect($this->options->adminUrl);
                break;
        }
    }

    /**
     * 回调
     */
    public function callback()
    {
        $type = $this->request->get('type');
        switch ($type) {
            case 'github':
                $this->callbackGithub();
                break;
            default:
                $this->response->redirect($this->options->adminUrl);
                break;
        }
    }


    /**
     * 跳转
     * @param bool $hasLogin
     * @param string $name
     * @param string $emial
     */
    public function redirect($hasLogin = false, $name = '', $emial = '')
    {
        if ($hasLogin) {
            /** 跳转验证后地址 */
            if (NULL != $this->request->referer) {
                $this->response->redirect($this->request->referer);
            } else if (!$this->user->pass('contributor', true)) {
                /** 不允许普通用户直接跳转后台 */
                $this->response->redirect($this->options->profileUrl);
            } else {
                $this->response->redirect($this->options->adminUrl);
            }
        }

        if (!$this->options->allowRegister) {
            self::msgNotice(_t('禁止注册'));
        }

        Typecho_Cookie::set('__typecho_remember_name', $name);
        Typecho_Cookie::set('__typecho_remember_mail', $emial);
        $this->response->redirect($this->options->registerUrl);
    }
}
