<?php


trait TypechoPlus_Plugin_Content
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
        $content = new Typecho_Widget_Helper_Form_Element_Checkbox('content',
            array(
                'showTitle' => _t('加密文章显示标题'),
                'moreSplit' => _t('<--more--> 后面内容加密'),
                'targetBlank' => _t('内容链接以“_blank”方式打开'),
            )
            , null, _t('内容显示'));
        $form->addInput($content->multiMode());
    }

    /**
     * 处理
     * @param $value
     * @param $that
     * @return mixed
     */
    public static function contentFilter($value, $that)
    {
        if (isset(self::myOptions()->content) && in_array('showTitle', self::myOptions()->content)) {
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
        $security = Helper::security();

        if (isset($that->required_pwd)) {

            if (isset(self::myOptions()->content) && in_array('moreSplit', self::myOptions()->content)) {
                $content = explode('<!--more-->', $content)[0];
            } else {
                $content = '';
            }

            $content .= '<form class="protected" action="' . $security->getTokenUrl($that->permalink) . '" method="post">' .
                '<p class="word">' . _t('请输入密码访问') . '</p>' .
                '<p><input type="password" class="text" name="protectPassword" />' .
                '<input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
                '</form>';
        }

        if (isset(self::myOptions()->content) && in_array('targetBlank', self::myOptions()->content)) {
            $content = self::autoBlank($content);
        }

        return $content;
    }
}
