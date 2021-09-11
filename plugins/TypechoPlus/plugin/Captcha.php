<?php

use Typecho\Widget\Helper\Form\Element\Checkbox as Typecho_Widget_Helper_Form_Element_Checkbox;
use Typecho\Widget\Helper\Form as Typecho_Widget_Helper_Form;
use Typecho\Request as Typecho_Request;
use Typecho\Plugin as Typecho_Plugin;
use Typecho\Common as Typecho_Common;
use Typecho\Cookie as Typecho_Cookie;

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
        $captcha = new Typecho_Widget_Helper_Form_Element_Checkbox('captcha', array(_t('登录验证码')), null, _t('登录验证码'));
        $form->addInput($captcha);
    }

    /**
     * 渲染
     */
    public static function captchaRender()
    {
        $request = Typecho_Request::getInstance();
        $randStr = strtolower(Typecho_Common::randString(2));

        if (!empty(self::myOptions()->captcha)) {
            if (preg_match('/\/login\.php/i', $request->getRequestUrl())) {
                session_start();

                $_SESSION['__typecho_captcha_rand_str'] = $randStr;
                ?>
                <script>
                    const captchaRender = `<p><input type="text" name="captcha" placeholder="验证码：<?php echo $randStr; ?>" class="text-l w-100"></p>`
                    $('.typecho-login .submit').prepend(captchaRender)
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
        $request = Typecho_Request::getInstance();

        if (!empty(self::myOptions()->captcha)) {
            session_start();

            $randStr = $_SESSION['__typecho_captcha_rand_str'];
            $captcha = $request->get('captcha');

            if (strtolower($captcha) != $randStr) {
                self::msgNotice(_t('验证码无效'));
            }
            session_destroy();
        }

        //暂时禁用插件，跳过插件执行
        Typecho_Plugin::deactivate(self::$pluginName);
        return Typecho_Widget::widget('Widget_User')->login($name, $password, $temporarily, $expire);
    }
}
