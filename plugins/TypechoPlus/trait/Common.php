<?php

/**
 * Trait Common
 */
trait Common
{
    //插件名称
    private static $plugin_name = 'TypechoPlus';

    private static $http_client;
    private static $widget_notice;
    private static $widget_options;

    /**
     * 实例化
     * @throws Typecho_Exception
     */
    public static function instantiation()
    {
        self::$http_client = Typecho_Http_Client::get();
        self::$widget_notice = Typecho_widget::widget('Widget_Notice');
        self::$widget_options = Typecho_Widget::widget('Widget_Options');
    }

    /**
     * 消息通知
     * @param string $mgs
     * @throws Typecho_Exception
     */
    public static function msgNotice($mgs = '')
    {
        self::instantiation();

        self::$widget_notice->set($mgs);
        self::$widget_notice->response->goBack('');
    }


}