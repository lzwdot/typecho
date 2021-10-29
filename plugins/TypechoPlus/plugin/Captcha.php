<?php

use Typecho\Common;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form;
use Typecho\Request;
use Typecho\Plugin;
use Widget\Options;
use Widget\User;
use Widget\Archive;
use Widget\Register;
use Widget\Feedback;
use Typecho\Widget\Exception;
use Typecho\Cookie;

trait TypechoPlus_Plugin_Captcha
{
    /**
     * 激活
     */
    public static function captchaActivate()
    {
        Plugin::factory('admin/header.php')->header = [get_class(), 'captchaHtml'];
        Plugin::factory(Archive::class)->header = [get_class(), 'captchaHeader'];
        Plugin::factory('admin/footer.php')->end = [get_class(), 'captchaFooter'];
        Plugin::factory(Archive::class)->footer = [get_class(), 'captchaFooter'];
        Plugin::factory(User::class)->login = [get_class(), 'captchaLogin'];
        Plugin::factory(Register::class)->register = [get_class(), 'captchaReg'];
        Plugin::factory(Feedback::class)->comment = [get_class(), 'captchaCmt'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function captchaConfig(Form $form)
    {
        $captchaCheckbox = new Checkbox('captchaCheckbox',
            [
                'captchaLogin' => _t('登录验证码'),
                'captchaReg'   => _t('注册验证码'),
                'captchaCmt'   => _t('评论验证码'),
            ], null, _t('验证码'));
        $form->addInput($captchaCheckbox->multiMode());
    }

    /**
     * header 渲染
     * @param $header
     * @param $that
     * @throws Plugin\Exception
     */
    public static function captchaHeader($header, $that)
    {
        $captchaCheckbox = self::myOptions()->captchaCheckbox;
        if ($captchaCheckbox) {
            echo self::captchaHtml($header) . '<script src="' . Options::alloc()->adminStaticUrl('js', 'jquery.js', true) . '"></script>';
        };
    }

    /**
     * header html
     * @param $header
     * @return mixed|string
     * @throws Plugin\Exception
     */
    public static function captchaHtml($header)
    {
        $captchaCheckbox = self::myOptions()->captchaCheckbox;
        if ($captchaCheckbox) {
            $header .= '<link rel="stylesheet" href="' . Options::alloc()->pluginUrl . '/' . self::$pluginName . '/assets/css/slideJigsaw.css' . '">';
        };

        return $header;
    }

    /**
     *  footer 渲染
     * @param $that
     * @return false|void
     * @throws Plugin\Exception
     */
    public static function captchaFooter($that)
    {
        $request = Request::getInstance();
        $requestUrl = $request->getRequestUrl();
        $captchaCheckbox = self::myOptions()->captchaCheckbox;

        if (empty($captchaCheckbox)) return false;

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
        $captchaName = Common::randString(7);
        $captchaVal = Common::randString(7);

        Cookie::set('__typecho_captcha_key', self::enMcrypt($captchaName . '|' . $captchaVal));
        ?>

      <script
          src="<?php Options::alloc()->pluginUrl(self::$pluginName . '/assets/js/slideJigsaw.js'); ?>"></script>
      <script>
        const html = `<input type="hidden" name="captcha<?php echo $captchaName; ?>" id="captcha">
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

          subBtnEl.attr('type', 'button')
          subBtnEl.on('click', function () {
            slideJigsawEl.css('top', subBtnEl.offset().top - slideJigsawEl.height())
            slideJigsawEl.show()
          })
          slideJigsaw.init({}, () => {
            captchaEl.val('<?php echo $captchaVal; ?>')
            setTimeout(() => {
              formEl.submit()
            }, 1000)
          })
        })
      </script>

        <?php
    }

    /**
     * 登录验证码
     * @param $name
     * @param $password
     * @param $temporarily
     * @param $expire
     * @return bool
     * @throws Plugin\Exception
     * @throws \Typecho\Db\Exception
     */
    public static function captchaLogin($name, $password, $temporarily, $expire)
    {
        $captchaCheckbox = self::myOptions()->captchaCheckbox ?? [];
        if (in_array('captchaLogin', $captchaCheckbox)) {
            self::captchaVerify();
        }

        //暂时禁用插件，跳过插件执行
        Plugin::deactivate(self::$pluginName);
        return User::alloc()->login($name, $password, $temporarily, $expire);
    }

    /**
     * 注册验证码
     * @param $dataStruct
     * @return mixed
     */
    public static function captchaReg($dataStruct)
    {
        $captchaCheckbox = self::myOptions()->captchaCheckbox ?? [];
        if (in_array('captchaReg', $captchaCheckbox)) {
            self::captchaVerify();
        }

        return $dataStruct;
    }

    /**
     * 评论验证码
     * @param $comment
     * @param $content
     * @return mixed
     */
    public static function captchaCmt($comment, $content)
    {
        $captchaCheckbox = self::myOptions()->captchaCheckbox ?? [];
        if (in_array('captchaCmt', $captchaCheckbox)) {
            self::captchaVerify(true);
        }

        return $comment;
    }

    /**
     * 验证码校验
     * @param false $throw
     */
    public static function captchaVerify($throw = false)
    {
        $captchaKey = Cookie::get('__typecho_captcha_key');
        Cookie::delete('__typecho_captcha_key');

        [$captchaName, $captchaVal] = explode('|', self::deMcrypt($captchaKey));
        $request = Request::getInstance();
        $captcha = $request->get('captcha' . $captchaName);

        if ($captcha !== $captchaVal) {
            $msg = _t('验证码无效');
            if ($throw) {
                throw new Exception($msg);
            }
            self::msgNotice($msg);
        }
    }
}
