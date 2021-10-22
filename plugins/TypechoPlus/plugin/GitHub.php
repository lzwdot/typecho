<?php

use Typecho\Db;
use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Request;
use Widget\Register;
use Typecho\Cookie;

trait TypechoPlus_Plugin_GitHub
{
    /**
     * 激活
     */
    public static function githubActivate()
    {
        // 添加 github 字段
        self::addTableColumn('github', 'users');

        // 激活
        Plugin::factory('admin/footer.php')->end = [get_class(), 'githubRender'];
        Plugin::factory(Register::class)->register = [get_class(), 'githubRegister'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function githubConfig(Form $form)
    {
        $clientId = new Text('githubClientId', null, null, _t('GitHub 登录注册'));
        $clientId->input->setAttribute('placeholder', _t('Client ID'));
        $form->addInput($clientId);

        $clientSecret = new Text('githubClientSecret', null, null, '', _t('使用 GitHub 登录注册，需要申请 Client ID 和 Client Secret 才能使用'));
        $clientSecret->input->setAttribute('placeholder', _t('Client Secret'));
        $form->addInput($clientSecret);
    }

    /**
     * 渲染
     */
    public static function githubRender()
    {
        $request = Request::getInstance();
        $requestUrl = $request->getRequestUrl();
        $githubClientId = self::myOptions()->githubClientId;
        $githubClientSecret = self::myOptions()->githubClientSecret;

        if ($githubClientId && $githubClientSecret && preg_match('/\/login\.php/i', $requestUrl)) {
            ?>
            <script>
                const githubRender = `<p><a href='<?php echo self::myAction()->getOauthUrl('github') ?>'">
                        <img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" width="30" height="30" alt="GitHub 登录" title="GitHub 登录" style="border-radius:50%">
                    </a></p>`
                $('.typecho-login').append(githubRender)
            </script>
            <?php
        }
    }

    /**
     * 注册 github ID
     * @param $dataStruct
     * @return mixed
     */
    public static function githubRegister($dataStruct)
    {
        $dataStruct['github'] = Cookie::get('__typecho_github_id');

        Cookie::delete('__typecho_remember_mail');

        return $dataStruct;
    }
}
