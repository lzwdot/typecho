<?php

use Typecho\Widget;
use Widget\Options;
use Widget\Notice;

/**
 * Trait Common
 */
trait TypechoPlus_Lib_Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';
    private static $pluginAction = 'TypechoPlus_Action';

    /**
     * 获取选项
     * @return mixed|\Typecho\Config
     * @throws \Typecho\Plugin\Exception
     */
    public static function myOptions()
    {
        return Options::alloc()->plugin(self::$pluginName);
    }

    /**
     * 获取方法
     * @return \#P#Ф\TypechoPlus_Lib_Common.pluginAction|Widget
     */
    public static function myAction()
    {
        return Widget::widget(self::$pluginAction);
    }


    /**
     * 消息通知
     * @param string $mgs
     * @param string $url
     */
    public static function msgNotice($mgs = '', $url = '')
    {
        $notice = Notice::alloc();

        $notice->set($mgs);
        $url ? $notice->response->redirect($url) : $notice->response->goBack();
    }

    /**
     * 自动在新的窗口打开
     * @param $content
     * @return string|string[]
     */
    public static function autoBlank($content)
    {
        $content = str_replace('<a', '<a target="_blank"', $content);
        return $content;
    }
}
