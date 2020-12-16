<?php

/**
 * Trait Common
 */
trait Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';

    private static $httpClient;
    private static $widgetNotice;
    private static $widgetOptions;
    private static $widgetSecurity;

    /**
     * 实例化
     * @throws Typecho_Exception
     */
    public static function instantiation()
    {
        self::$httpClient = Typecho_Http_Client::get();
        self::$widgetNotice = Typecho_widget::widget('Widget_Notice');
        self::$widgetOptions = Typecho_Widget::widget('Widget_Options')->plugin(self::$pluginName);
        self::$widgetSecurity = Typecho_Widget::widget('Widget_Security');
    }

    /**
     * 消息通知
     * @param string $mgs
     * @throws Typecho_Exception
     */
    public static function msgNotice($mgs = '')
    {
        self::instantiation();

        self::$widgetNotice->set($mgs);
        self::$widgetNotice->response->goBack('');
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
