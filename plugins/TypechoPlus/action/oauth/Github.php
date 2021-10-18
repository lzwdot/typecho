<?php

use Typecho\Plugin;
use Typecho\Common;
use Typecho\Cookie;
use Typecho\Http\Client;
use Typecho\Http\Client\Exception;

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
        $state = Common::randString(8);
        Cookie::set('__typecho_github_state', $state);

        $this->response->redirect($this->github_authorize_url . '?' . http_build_query([
                'client_id' => self::myOptions()->githubClientId,
                'redirect_uri' => $this->options->siteUrl . '/callback?type=github',
                'state' => $state
            ]));
        exit();
    }

    /**
     * callback
     */
    public function callbackGithub()
    {
        $loginUrl = $this->options->loginUrl;
        $httpClient = Client::get();

        $state = Cookie::get('__typecho_github_state');
        if ($state !== $this->request->state) {
            self::msgNotice(_t('非法请求'));
        }

        try {
            $result = $httpClient->setTimeout(30)
                ->setHeader('Accept', 'application/json')
                ->setData(['client_id' => self::myOptions()->githubClientId,
                    'client_secret' => self::myOptions()->githubClientSecret,
                    'code' => $this->request->code
                ])->send($this->github_access_token_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error'], $loginUrl);
            }

            $result = $httpClient->setMethod('GET')
                ->setTimeout(30)
                ->setHeader('Accept', 'application/json')
                ->setHeader('User-Agent', 'localhost')
                ->setHeader('Authorization', 'token  ' . $result['access_token'])
                ->send($this->github_api_user_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error'], $loginUrl);
            }

            $this->loginGithub($result);
        } catch (Exception $e) {
            self::msgNotice($e->getMessage(), $loginUrl);
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
            ->where('name = ?', $data['login'])
            ->limit(1);
        $user = $this->db->fetchRow($select);

        //暂时禁用插件，跳过插件执行
        Plugin::deactivate(self::$pluginName);
        Plugin::factory('User')->hashValidate = function ($password, $userPwd) {
            return $password == $userPwd;
        };
        $this->redirect($user && $this->user->login($user['name'], $user['password']), $data['login'], $data['email']);
    }
}
