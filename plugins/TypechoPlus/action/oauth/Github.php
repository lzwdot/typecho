<?php

use Typecho\Plugin;
use Typecho\Common;
use Typecho\Cookie;
use Typecho\Http\Client;
use Typecho\Http\Client\Exception;
use Widget\Register;
use Widget\User;
use Widget\Options;

trait TypechoPlus_Action_Oauth_Github
{

    private static $github_authorize_url = 'https://github.com/login/oauth/authorize';
    private static $github_access_token_url = 'https://github.com/login/oauth/access_token';
    private static $github_api_user_url = 'https://api.github.com/user';

    /**
     * oauth
     */
    public static function oauthGithub()
    {
        $options = Options::alloc();

        $state = Common::randString(8);
        $clientId = self::myOptions()->githubClientId;

        Cookie::set('__typecho_github_state', $state);

        $options->response->redirect(self::$github_authorize_url . '?' . http_build_query([
                'client_id'    => $clientId,
                'redirect_uri' => self::getCallbackUrl('github'),
                'state'        => $state
            ]));
        exit();
    }

    /**
     * callback
     */
    public static function callbackGithub()
    {
        $options = Options::alloc();
        $httpClient = Client::get();

        $githubState = Cookie::get('__typecho_github_state');
        $state = $options->request->get('state');
        $code = $options->request->get('code');
        $loginUrl = $options->loginUrl;
        $clientId = self::myOptions()->githubClientId;
        $clientSecret = self::myOptions()->githubClientSecret;

        if ($state !== $githubState) {
            self::msgNotice(_t('非法请求'));
        }

        try {
            $result = $httpClient->setTimeout(30)
                ->setHeader('Accept', 'application/json')
                ->setData(['client_id'     => $clientId,
                           'client_secret' => $clientSecret,
                           'code'          => $code
                ])->send(self::$github_access_token_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error'], $loginUrl);
            }

            $result = $httpClient->setMethod('GET')
                ->setTimeout(60)
                ->setHeader('Accept', 'application/json')
                ->setHeader('User-Agent', 'localhost')
                ->setHeader('Authorization', 'token  ' . $result['access_token'])
                ->send(self::$github_api_user_url);

            $result = json_decode($result, true);
            if (!empty($result['error'])) {
                self::msgNotice($result['error'], $loginUrl);
            }

            self::loginGithub($result);
        } catch (Exception $e) {
            self::msgNotice($e->getMessage(), $loginUrl);
        }
    }

    /**
     * login
     * @param $data
     */
    public static function loginGithub($data)
    {
        $options = Options::alloc();

        $select = $options->db->select()
            ->from('table.users')
            ->where('github = ?', $data['id'])
            ->limit(1);
        $user = $options->db->fetchRow($select);

        $hasLogin = false;

        // 已注册
        if (isset($user['name'])) {
            Plugin::factory(User::class)->hashValidate = function ($password, $userPwd) {
                return $password === $userPwd;
            };

            $hasLogin = $options->user->login($user['name'], $user['password']);
        } else {
            // 设置注册数据
            Cookie::set('__typecho_github_id', $data['id']);
        }

        self::redirect($hasLogin, $data['login'], $data['email']);
    }
}
