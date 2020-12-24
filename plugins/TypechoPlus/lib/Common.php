<?php

/**
 * Trait Common
 */
trait TypechoPlus_Lib_Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';

    /**
     * @return mixed
     * @throws Typecho_Plugin_Exception
     */
    public static function myOptions()
    {
        return Helper::options()->plugin(self::$pluginName);
    }

    /**
     * @return Typecho_Widget
     * @throws Typecho_Exception
     */
    public static function myAction()
    {
        return Typecho_Widget::widget('TypechoPlus_Action');
    }


    /**
     * 消息通知
     * @param string $mgs
     * @throws Typecho_Exception
     */
    public static function msgNotice($mgs = '')
    {
        $notice = Typecho_widget::widget('Widget_Notice');

        $notice->set($mgs);
        $notice->response->goBack('');
    }

    /**
     * 自动在新的窗口打开
     * @param $content
     * @return mixed
     */
    public static function autoBlank($content)
    {
        $content = str_replace('<a', '<a target="_blank"', $content);
        return $content;
    }
}
