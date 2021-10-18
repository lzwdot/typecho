<?php

use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form;
use Typecho\Request;
use Typecho\Plugin;
use Typecho\Common;
use Typecho\Cookie;
use Widget\Options;
use Typecho\Widget;

trait TypechoPlus_Plugin_Captcha
{
    /**
     * 激活
     */
    public static function captchaActivate()
    {
        Plugin::factory('admin/header.php')->header = [get_class(), 'headerRender'];
        Plugin::factory('admin/footer.php')->end = [get_class(), 'footerRender'];
        Plugin::factory('Widget_User')->login = [get_class(), 'captchaVerify'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function captchaConfig(Form $form)
    {
        $captcha = new Checkbox('captcha', [_t('登录验证码')], null, _t('登录验证码'));
        $form->addInput($captcha);
    }

    /**
     * hader 渲染
     * @param $header
     * @return mixed|string
     * @throws Plugin\Exception
     */
    public static function headerRender($header)
    {
        $request = Request::getInstance();

        if (!empty(self::myOptions()->captcha)) {
            if (preg_match('/\/login\.php/i', $request->getRequestUrl())) {
                $header .= '<link rel="stylesheet" href="' . Options::alloc()->pluginUrl . '/' . self::$pluginName . '/assets/css/slideJigsaw.css' . '">';
            }
        }
        return $header;
    }

    /**
     * footer 渲染
     * @throws Plugin\Exception
     */
    public static function footerRender()
    {
        $request = Request::getInstance();

        if (!empty(self::myOptions()->captcha)) {
            if (preg_match('/\/login\.php/i', $request->getRequestUrl())) {
                ?>
                <script
                    src="<?php Options::alloc()->pluginUrl(self::$pluginName . '/assets/js/slideJigsaw.js'); ?>"></script>
                <script>
                    const html = `<input type="hidden" name="captcha" id="captcha">
                        <div id="slideJigsaw" class="slide-jigsaw">
                            <canvas class="panel"></canvas>
                            <canvas class="jigsaw"></canvas>
                            <div class="refresh"><i class="icon"></i></div>
                            <div class="sloading">
                                <div class="wrap"><i class="icon"></i>
                                    <p>加载中...</p></div>
                            </div>
                            <div class="control">
                                <div class="indicator"></div>
                                <div class="slider"><i class="icon"></i></div>
                                <div class="tips">向右拖动滑块填充拼图</div>
                            </div>
                        </div>`
                    $('form').append(html)
                    $(function () {
                        const formEl = $('form')
                        const subBtn = $('.submit button')
                        const slideJigsawEl = $('#slideJigsaw')
                        const captchaEl = $('#captcha')
                        const topPos = subBtn.offset().top - slideJigsawEl.height()

                        subBtn.attr('type', 'button')
                        subBtn.on('click', function () {
                            slideJigsawEl.css('top', topPos)
                            slideJigsawEl.show()
                        })
                        slideJigsaw.init({}, () => {
                            captchaEl.val(Math.random())
                            setTimeout(() => {
                                formEl.submit()
                            }, 1000)
                        })
                    })
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
     * @throws Plugin\Exception
     */
    public static function captchaVerify($name, $password, $temporarily, $expire)
    {
        $request = Request::getInstance();
        $captcha = $request->get('captcha');

        if (!empty(self::myOptions()->captcha) && empty($captcha)) {
            self::msgNotice(_t('验证码无效'));
        }

        //暂时禁用插件，跳过插件执行
        Plugin::deactivate(self::$pluginName);
        return Widget::widget('Widget_User')->login($name, $password, $temporarily, $expire);
    }
}
