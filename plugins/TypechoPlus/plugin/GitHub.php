<?php


trait TypechoPlus_Plugin_GitHub
{
    /**
     * 激活
     */
    public static function githubActivate()
    {
        Typecho_Plugin::factory('admin/footer.php')->end = array(get_class(), 'githubRender');
    }

    /**
     * 配置
     * @param Typecho_Widget_Helper_Form $form
     */
    public static function githubConfig(Typecho_Widget_Helper_Form $form)
    {
        $clientId = new Typecho_Widget_Helper_Form_Element_Text('githubClientId', null, null, _t('GitHub 登录注册'));
        $clientId->input->setAttribute('placeholder', _t('Client ID'));
        $form->addInput($clientId);

        $clientSecret = new Typecho_Widget_Helper_Form_Element_Text('githubClientSecret', null, null, '',_t('使用 GitHub 登录注册，需要申请 Client ID 和 Client Secret 才能使用'));
        $clientSecret->input->setAttribute('placeholder', _t('Client Secret'));
        $form->addInput($clientSecret);
    }

    /**
     * 渲染
     */
    public static function githubRender()
    {
        $options = Helper::options();

        if (!empty(self::myOptions()->githubClientId) && !empty(self::myOptions()->githubClientSecret)) {
            if (preg_match('/\/login\.php/i', $options->request->getRequestUrl())) {
                ?>
                <script>
                    const githubRender = `<p><button type="button" class="btn btn-l w-100" onclick="location.href='<?php echo self::myAction()->getOauthUrl('github') ?>'">GitHub 登录</button></p>`
                    $('.typecho-login form').append(githubRender)
                </script>
                <?php
            }
        }
    }
}
