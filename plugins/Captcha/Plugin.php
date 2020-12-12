<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 基于 Luosimao 人机验证制作的 Typecho 登录验证码插件
 *
 * @package Captcha
 * @author A.wei
 * @version 1.0.0
 * @link http://gravatar.cn
 */
class Captcha_Plugin implements Typecho_Plugin_Interface
{
    private static $http_client;
    private static $plugin_name;
    private static $widget_notice;
    private static $widget_options;

    /**
     * 初始化参数
     * @throws Typecho_Exception
     */
    private static function _init()
    {
        //获取插件名称
        list(self::$plugin_name) = explode('_', get_class());

        self::$http_client = Typecho_Http_Client::get();
        self::$widget_notice = Typecho_widget::widget('Widget_Notice');
        self::$widget_options = Typecho_Widget::widget('Widget_Options')->plugin(self::$plugin_name);
    }

    /**
     * 消息通知
     * @param string $mgs
     * @throws Typecho_Exception
     */
    private static function msgNotice($mgs = '')
    {
        self::$widget_notice->set($mgs);
        self::$widget_notice->response->goBack('');
    }

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('admin/footer.php')->end = array(get_class(), 'addCaptcha');
        Typecho_Plugin::factory('Widget_User')->login = array(get_class(), 'verifyCaptcha');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        /** 分类名称 */
        $site_key = new Typecho_Widget_Helper_Form_Element_Text('site_key', NULL, '', _t('site key'));
        $api_key = new Typecho_Widget_Helper_Form_Element_Text('api_key', NULL, '', _t('api key'));
        $form->addInput($site_key);
        $form->addInput($api_key);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }


    /**
     * 添加验证码
     */
    public static function addCaptcha()
    {
        self::_init();

        if (preg_match('/\/login\.php/i', self::$widget_notice->request->getRequestUrl())) {
            ?>
            <script src="//captcha.luosimao.com/static/dist/api.js"></script>
            <script>
                const html = '<div class="l-captcha" data-site-key="<?php echo self::$widget_options->site_key; ?>"></div>'
                $('.typecho-login p.submit').before(html)
            </script>
            <?php
        }
    }

    /**
     * 校验验证码
     * @param $name
     * @param $password
     * @param $temporarily
     * @param $expire
     * @throws Typecho_Exception
     */
    public static function verifyCaptcha($name, $password, $temporarily, $expire)
    {
        $url = 'http://captcha.luosimao.com/api/site_verify';
        self::_init();

        if (self::$widget_options->site_key && self::$widget_options->api_key) {
            $luotest_response = self::$widget_notice->request->get('luotest_response');
            if (!$luotest_response) {
                self::msgNotice(_t('请点击验证码'));
            }

            if (!self::$http_client) {
                self::msgNotice(_t('抱歉，无可用的 HTTP 连接'));
            }

            $result = self::$http_client->setData(['api_key' => self::$widget_options->api_key, 'response' => $luotest_response])->send($url);
            $result = json_decode($result, true);
            if ($result['error'] != 0) {
                self::msgNotice(_t('验证码无效'));
            }
        }

        //暂时禁用插件，跳过插件执行
        Typecho_Plugin::deactivate(self::$plugin_name);
        return Typecho_Widget::widget('Widget_User')->login($name, $password, $temporarily, $expire);
    }
}
