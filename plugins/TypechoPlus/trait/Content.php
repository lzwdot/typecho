<?php

/**
 * Trait Content
 */
trait Content
{
    /**
     * 激活
     */
    public static function contentActivate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->filter = array(get_class(), 'contentFilter');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array(get_class(), 'contentExHandle');
    }

    /**
     * 配置
     * @param Typecho_Widget_Helper_Form $form
     */
    public static function contentConfig(Typecho_Widget_Helper_Form $form)
    {
        $content = new Typecho_Widget_Helper_Form_Element_Checkbox('content', array(_t('内容增强，支持 <em><--more--></em> 后面加密，链接自动以“_blank”打开')), null, null);
        $form->addInput($content);
    }

    /**
     * 处理
     * @param $value
     * @param $that
     * @return mixed
     */
    public static function contentFilter($value, $that)
    {
        self::instantiation();

        if (isset(self::$widgetOptions->plugin(self::$pluginName)->content)) {
            if ($value['hidden']) {
                $value['hidden'] = false;
                $value['required_pwd'] = true;
            }
        }

        return $value;
    }

    /**
     * 处理
     * @param $text
     * @param $that
     * @return string
     */
    public static function contentExHandle($content, $that)
    {
        self::instantiation();

        if (isset(self::$widgetOptions->plugin(self::$pluginName)->content)) {
            if (isset($that->required_pwd)) {
                $content = explode('<!--more-->', $content)[0] .
                    '<form class="protected" action="' . self::$widgetSecurity->getTokenUrl($that->permalink) . '" method="post">' .
                    '<p class="word">' . _t('请输入密码访问') . '</p>' .
                    '<p><input type="password" class="text" name="protectPassword" />' .
                    '<input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
                    '</form>';
            }
        }

        return self::autoBlank($content);
    }
}
