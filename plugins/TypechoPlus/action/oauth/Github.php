<?php

use Typecho\Plugin as Typecho_Plugin;
use Typecho\Common as Typecho_Common;
use Typecho\Cookie as Typecho_Cookie;
use Typecho\Http\Client as Typecho_Http_Client;
use Typecho\Http\Client\Exception as Typecho_Http_Client_Exception;

trait TypechoPlus_Action_Oauth_Github
{

    private $github_authorize_url = 'https://github.com/login/oauth/authorize';
    private $github_access_token_url = 'https://github.com/login/oauth/access_token';
    private $github_api_user_url = 'https://api.github.com/user';

    /**
     * oauth
     */
    public function oauthGithub()
    {
        $state = Typecho_Common::randString(8);
        Typecho_Cookie::set('__typecho_github_state', $state);

        $this->response->redirect($this->github_authorize_url . '?' . http_build_query(array(
                'client_id' => self::myOptions()->githubClientId,
                'redirect_uri' => $this->options->siteUrl . '/callback?type=github',
                'state' => $state
            )));
        exit();
    }

    /**
     * callback
     */
    public function callbackGithub()
    {
        $httpClient = Typecho_Http_Client::get();

        $state = Typecho_Cookie::get('__typecho_github_state');
        if ($state !== $this->request->state) {
            self::msgNotice(_t('非法请求'));
        }

        try {
            $result = $httpClient->setHeader('Accept', 'application/json')
                ->setTimeout(30)
                ->setData(['client_id' => self::myOptions()->githubClientId,
                    'client_secret' => self::myOptions()->githubClientSecret,
                    'code' => $this->request->code
                ])->send($this->github_access_token_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error']);
            }

            $result = $httpClient->setMethod('GET')
                ->setTimeout(30)
                ->setHeader('Accept', 'application/json')
                ->setHeader('User-Agent', 'localhost')
                ->setHeader('Authorization', 'token  ' . $result['access_token'])
                ->send($this->github_api_user_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error']);
            }

            $this->loginGithub($result);
        } catch (Typecho_Http_Client_Exception $e) {
            self::msgNotice($e->getMessage());
        }
    }

    /**
     * login
     * @param $data
     */
    public function loginGithub($data)
    {
        $select = $this->db->select()
            ->from('table.users')
            ->where('mail = ?', $data['email'])
            ->limit(1);
        $user = $this->db->fetchRow($select);

        //暂时禁用插件，跳过插件执行
        Typecho_Plugin::deactivate(self::$pluginName);
        Typecho_Plugin::factory('Widget_User')->hashValidate = function ($password, $userPwd) {
            return $password == $userPwd;
        };
        $this->redirect($user && $this->user->login($user['name'], $user['password']), $data['login'], $data['email']);
    }
}
