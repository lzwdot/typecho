<?php

/**
 * Trait Captcha
 */
trait Captcha
{
    /**
     * 激活
     */
    public static function captchaActivate()
    {
        Typecho_Plugin::factory('admin/footer.php')->end = array(get_class(), 'captchaHtml');
        Typecho_Plugin::factory('Widget_User')->login = array(get_class(), 'captchaVerify');
    }

    /**
     * 构造函数
     *
     * @access public
     * @param string $name 表单输入项名称
     * @param array $options 选择项
     * @param mixed $value 表单默认值
     * @param string $label 表单标题
     * @param string $description 表单描述
     * @return void
     */

    /**
     * 配置
     * @param Typecho_Widget_Helper_Form $form
     */
    public static function captchaConfig(Typecho_Widget_Helper_Form $form)
    {

        $content = new Typecho_Widget_Helper_Form_Element_Checkbox('captcha', array(_t('基于 Luosimao 人机验证制作')), null, _t('登录验证码'));
        $form->addInput($content);

        $site_key = new Typecho_Widget_Helper_Form_Element_Text('site_key', null, _t('site key'), '');
        $api_key = new Typecho_Widget_Helper_Form_Element_Text('api_key', null, _t('api key'), '');
        $form->addInput($site_key);
        $form->addInput($api_key);
    }

    /**
     * 添加
     */
    public static function captchaHtml()
    {
        self::instantiation();

        if (isset(self::$widgetOptions->plugin(self::$pluginName)->captcha)
            && isset(self::$widgetOptions->plugin(self::$pluginName)->site_key)) {

            if (preg_match('/\/login\.php/i', self::$widgetNotice->request->getRequestUrl())) {
                ?>
                <script src="//captcha.luosimao.com/static/dist/api.js"></script>
                <script>
                    const html = '<div class="l-captcha" data-site-key="<?php echo htmlspecialchars(self::$widgetOptions->plugin(self::$pluginName)->site_key); ?>" data-callback="getResponse"></div>'
                    $('.typecho-login p.submit').before(html)
                    $('button[type="submit"]').prop('disabled', true).html('<?php _e('请先进行人机验证'); ?>')

                    function getResponse(resp) {
                        if (resp !== undefined) {
                            $('button[type="submit"]').prop('disabled', false).html('<?php _e('登录'); ?>')
                        }
                    }
                </script>
                <?php
            }
        }
    }

    /**
     * 处理
     * @param $name
     * @param $password
     * @param $temporarily
     * @param $expire
     * @return mixed
     * @throws Typecho_Exception
     */
    public static function captchaVerify($name, $password, $temporarily, $expire)
    {
        self::instantiation();

        if (isset(self::$widgetOptions->plugin(self::$pluginName)->captcha)
            && isset(self::$widgetOptions->plugin(self::$pluginName)->site_key)
            && isset(self::$widgetOptions->plugin(self::$pluginName)->api_key)) {

            $url = 'http://captcha.luosimao.com/api/site_verify';
            $luotest_response = self::$widgetNotice->request->get('luotest_response');
            if (!$luotest_response) {
                self::msgNotice(_t('请点击验证码'));
            }

            if (!self::$httpClient) {
                self::msgNotice(_t('抱歉，无可用的 HTTP 连接'));
            }

            $result = self::$httpClient->setData(['api_key' => self::$widgetOptions->plugin(self::$pluginName)->api_key, 'response' => $luotest_response])->send($url);
            $result = json_decode($result, true);
            if ($result['error'] != 0) {
                self::msgNotice(_t('验证码无效'));
            }
        }

        //暂时禁用插件，跳过插件执行
        Typecho_Plugin::deactivate(self::$pluginName);
        return Typecho_Widget::widget('Widget_User')->login($name, $password, $temporarily, $expire);
    }
}
