<?php

use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form;
use Typecho\Request;
use Typecho\Plugin;
use Widget\Options;
use Typecho\Widget;
use Widget\User;
use Widget\Archive;

trait TypechoPlus_Plugin_Captcha
{
    /**
     * 激活
     */
    public static function captchaActivate()
    {
        Plugin::factory('admin/header.php')->header = [get_class(), 'headerHtml'];
        Plugin::factory(Archive::class)->header = [get_class(), 'headerRender'];
        Plugin::factory('admin/footer.php')->end = [get_class(), 'footerRender'];
        Plugin::factory(Archive::class)->footer = [get_class(), 'footerRender'];
        Plugin::factory(User::class)->login = [get_class(), 'captchaVerify'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function captchaConfig(Form $form)
    {
        $checkbox = new Checkbox('captchaCheckbox',
            [
                'captchaLogin' => _t('登录验证码'),
                'captchaReg' => _t('注册验证码'),
                'captchaCmt' => _t('评论验证码'),
            ]
            , null, _t('验证码'));
        $form->addInput($checkbox->multiMode());
    }

    /**
     * hader 渲染
     * @param $header
     * @return mixed|string
     * @throws Plugin\Exception
     */
    public static function headerRender($header)
    {
        if (!empty(self::myOptions()->captchaCheckbox)) {
            echo self::headerHtml($header) . '<script src="' . Options::alloc()->adminStaticUrl('js', 'jquery.js', true) . '"></script>';
        };
    }

    /**
     * header html
     * @param $header
     * @return mixed|string
     * @throws Plugin\Exception
     */
    public static function headerHtml($header)
    {
        if (!empty(self::myOptions()->captchaCheckbox)) {
            $header .= '<link rel="stylesheet" href="' . Options::alloc()->pluginUrl . '/' . self::$pluginName . '/assets/css/slideJigsaw.css' . '">';
        };

        return $header;
    }

    /**
     * footer 渲染
     * @throws Plugin\Exception
     */
    public static function footerRender()
    {
        if (empty(self::myOptions()->captchaCheckbox)) return false;

        $captchaCheckbox = self::myOptions()->captchaCheckbox;
        $request = Request::getInstance();
        $requestUrl = $request->getRequestUrl();
        // 登录
        if (in_array('captchaLogin', $captchaCheckbox) && preg_match('/\/login\.php/i', $requestUrl)) {
            self::footHtml('button[type="submit"]', 'form[name="login"]');
        } else if (in_array('captchaReg', $captchaCheckbox) && preg_match('/\/register\.php/i', $requestUrl)) {
            self::footHtml('button[type="submit"]', 'form[name="register"]');
        } else if (in_array('captchaCmt', $captchaCheckbox) && Archive::alloc()->is('single')) {
            self::footHtml('#comment-form button[type="submit"]', '#comment-form');
        }

    }

    /**
     * 验证码 html
     * @param $subBtnEl
     * @param $formEl
     */
    public static function footHtml($subBtnEl, $formEl)
    {
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
          $('<?php echo $formEl; ?>').append(html)
          $(function () {
            const formEl = $('<?php echo $formEl; ?>')
            const subBtnEl = $('<?php echo $subBtnEl; ?>')
            const slideJigsawEl = $('#slideJigsaw')
            const captchaEl = $('#captcha')
            const topPos = subBtnEl.offset().top - slideJigsawEl.height()

            subBtnEl.attr('type', 'button')
            subBtnEl.on('click', function () {
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
