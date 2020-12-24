<?php

trait TypechoPlus_Plugin_Captcha
{
    /**
     * 激活
     */
    public static function captchaActivate()
    {
        Typecho_Plugin::factory('admin/footer.php')->end = array(get_class(), 'captchaRender');
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
        $siteKey = new Typecho_Widget_Helper_Form_Element_Text('captchaSiteKey', null, null, _t('登录验证码'));
        $siteKey->input->setAttribute('placeholder', _t('site key'));
        $form->addInput($siteKey);

        $apiKey = new Typecho_Widget_Helper_Form_Element_Text('captchaApiKey', null, null, '',_t('基于 Luosimao 人机验证制作，需要申请 site key 和 api key 才能使用'));
        $apiKey->input->setAttribute('placeholder', _t('api key'));
        $form->addInput($apiKey);
    }

    /**
     * 渲染
     */
    public static function captchaRender()
    {
        $options = Helper::options();

        if (!empty(self::myOptions()->captchaSiteKey) && !empty(self::myOptions()->captchaApiKey)) {

            if (preg_match('/\/login\.php/i', $options->request->getRequestUrl())) {
                ?>
                <script src="//captcha.luosimao.com/static/dist/api.js"></script>
                <script>
                    const captchaRender = '<div class="l-captcha" data-site-key="<?php echo htmlspecialchars(self::myOptions()->captchaSiteKey); ?>" data-callback="getResponse"></div>'
                    $('.typecho-login p.submit').before(captchaRender)
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
        $options = Helper::options();
        $httpClient = Typecho_Http_Client::get();
        $url = 'http://captcha.luosimao.com/api/site_verify';

        if (!empty(self::myOptions()->captchaSiteKey) && !empty(self::myOptions()->captchaApiKey)) {
            $luotest_response = $options->request->get('luotest_response');
            if (!$luotest_response) {
                self::msgNotice(_t('请点击验证码'));
            }

            $result = $httpClient->setData(['api_key' => self::myOptions()->captchaApiKey, 'response' => $luotest_response])->send($url);
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
