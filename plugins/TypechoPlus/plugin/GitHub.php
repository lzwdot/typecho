<?php

use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Text;
use Typecho\Request;

trait TypechoPlus_Plugin_GitHub
{
    /**
     * 激活
     */
    public static function githubActivate()
    {
        Plugin::factory('admin/footer.php')->end = [get_class(), 'githubRender'];
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

        if (!empty(self::myOptions()->githubClientId) && !empty(self::myOptions()->githubClientSecret)) {
            if (preg_match('/\/login\.php/i', $request->getRequestUrl())) {
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
    }
}
