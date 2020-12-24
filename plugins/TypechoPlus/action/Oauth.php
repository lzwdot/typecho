<?php

require_once __DIR__ . '/oauth/Github.php';

trait TypechoPlus_Action_Oauth
{
    use TypechoPlus_Action_Oauth_Github;

    /**
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
}
