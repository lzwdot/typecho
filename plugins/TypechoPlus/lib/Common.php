<?php

use Typecho\Widget as Typecho_widget;
use Widget\Options;

/**
 * Trait Common
 */
trait TypechoPlus_Lib_Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';
    private static $pluginAction = 'TypechoPlus_Action';

    /**
     * @return mixed
     * @throws Typecho_Plugin_Exception
     */
    public static function myOptions()
    {
        return Options::alloc()->plugin(self::$pluginName);
    }

    /**
     * @return Typecho_Widget
     * @throws Typecho_Exception
     */
    public static function myAction()
    {
        return Typecho_Widget::widget(self::$pluginAction);
    }


    /**
     * 消息通知
     * @param string $mgs
     * @param string $url
     * @throws Typecho_Exception
     */
    public static function msgNotice($mgs = '', $url = '')
    {
        $notice = Typecho_widget::widget('Widget_Notice');

        $notice->set($mgs);
        $notice->response->goBack('',$url);
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
