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
        $github = new Typecho_Widget_Helper_Form_Element_Checkbox('github', array(_t('使用 GitHub 登录')), null, _t('GitHub 登录'));
        $form->addInput($github);

        $clientId = new Typecho_Widget_Helper_Form_Element_Text('githubClientId', null, _t('Client ID'), '');
        $clientSecret = new Typecho_Widget_Helper_Form_Element_Text('githubClientSecret', null, _t('Client Secret'), '');
        $form->addInput($clientId);
        $form->addInput($clientSecret);
    }

    /**
     * 渲染
     */
    public static function githubRender()
    {
        $options = Helper::options();

        if (isset(self::myOptions()->github) && isset(self::myOptions()->githubClientId)) {
            if (preg_match('/\/login\.php/i', $options->request->getRequestUrl())) {
                ?>
                <script>
                    const html = `<p><button type="button" class="btn btn-l w-100" onclick="location.href='<?php echo self::myAction()->getOauthUrl('github') ?>'">GitHub 登录</button></p>`
                    $('.typecho-login form').append(html)
                </script>
                <?php
            }
        }
    }
}
