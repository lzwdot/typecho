<?php

namespace TypechoPlugin\IPLocation;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Widget\Base\Comments;
use Widget\Comments\Admin;
use Widget\Comments\Archive;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 显示评论IP所对应的真实地址
 *
 * @package IPLocation
 * @author joyqi
 * @version 1.1.0
 * @since 1.2.0
 * @link https://github.com/joyqi/typecho-plugins
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        Admin::pluginHandle()->callIp = __CLASS__ . '::location';
        Archive::pluginHandle()->callLocation = __CLASS__ . '::location';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     */
    public static function config(Form $form)
    {
    }

    /**
     * 个人用户的配置面板
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @param Comments $comments 评论
     */
    public static function location(Comments $comments)
    {
        $addresses = IP::find($comments->ip);
        $address = 'unknown';

        if (!empty($addresses)) {
            $addresses = array_unique($addresses);
            $address = implode('', $addresses);
        }

        if ($comments instanceof Admin) {
            echo $comments->ip . '<br>' . $address;
        } else {
            echo $address;
        }
    }
}
