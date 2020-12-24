<?php
/**
 * Created by PhpStorm.
 * User: A.wei
 * Date: 2020-12-18
 * Time: 16:23
 */

trait TypechoPlus_Action_Oauth_Github
{

    private $github_authorize_url = 'https://github.com/login/oauth/authorize';
    private $github_access_token_url = 'https://github.com/login/oauth/access_token';
    private $github_api_user_url = 'https://api.github.com/user';

    /**
     * github
     */
    public function oauthGithub()
    {
        $this->response->redirect($this->github_authorize_url . '?' . http_build_query(array(
                'client_id' => self::myOptions()->githubClientId,
                'redirect_uri' => $this->options->siteUrl . '/callback?type=github'
            )));
        exit();
    }

    /**
     * github
     */
    public function callbackGithub()
    {
        $httpClient = Typecho_Http_Client::get();

        $code = $this->request->get('code');
        try {
            $result = $httpClient->setHeader('Accept', 'application/json')
                ->setData(['client_id' => self::myOptions()->githubClientId,
                    'client_secret' => self::myOptions()->githubClientSecret,
                    'code' => $code
                ])->send($this->github_access_token_url);

            $result = json_decode($result, true);
            if (isset($result['error'])) {
                self::msgNotice($result['error']);
            }

            $result = $httpClient->setMethod('GET')->setTimeout(30)
                ->setHeader('Accept', 'application/json')
                ->setHeader('User-Agent', 'localhost')
                ->setHeader('Authorization', 'token  ' . $result['access_token'])
                ->send($this->github_api_user_url);

            $result = json_decode($result, true);
            if (isset($result['error'])) {
                self::msgNotice($result['error']);
            }

            $this->loginGithub($result);
        } catch (Typecho_Http_Client_Exception $e) {
            self::msgNotice($e->getMessage());
        }
    }

    public function loginGithub($data)
    {
        $select = $this->db->select()
            ->from('table.users')
            ->where('mail = ?', $data['email'])
            ->limit(1);
        $user = $this->db->fetchRow($select);

        if (!$user) {
            Typecho_Cookie::set('__typecho_remember_name', $data['login']);
            Typecho_Cookie::set('__typecho_remember_mail', $data['email']);
            $this->response->redirect(Typecho_Common::url('register.php', $this->options->adminUrl));
        }

        if ($login = $this->user->simpleLogin($user['uid'])) {
            $this->response->redirect($this->options->adminUrl);
        }
    }
}
