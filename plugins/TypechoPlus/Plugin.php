<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

require_once __DIR__ . '/lib/Common.php';
require_once __DIR__ . '/plugin/Search.php';
require_once __DIR__ . '/plugin/Content.php';
require_once __DIR__ . '/plugin/Captcha.php';
require_once __DIR__ . '/plugin/GitHub.php';
require_once __DIR__ . '/plugin/Gravatar.php';
require_once __DIR__ . '/Action.php';

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Widget\Action;
use Typecho\Plugin\Exception;
use Utils\Helper;

/**
 * Typecho 多功能增强插件
 *
 * @package TypechoPlus
 * @author lzw.
 * @version 1.0.1
 * @link http://lzwdot.com
 */
class TypechoPlus_Plugin implements PluginInterface
{
    use TypechoPlus_Lib_Common,
        TypechoPlus_Plugin_Search,
        TypechoPlus_Plugin_Content,
        TypechoPlus_Plugin_Captcha,
        TypechoPlus_Plugin_GitHub,
        TypechoPlus_Plugin_Gravatar;

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        self::searchActivate();
        self::contentActivate();
        self::captchaActivate();
        self::githubActivate();
        self::gravatarActivate();

        TypechoPlus_Action::actionActivate();

        return _t('插件%s已经被启用', self::$pluginName);
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
        TypechoPlus_Action::actionDeactivate();

        return _t('插件%s已经被禁用', self::$pluginName);
    }

    /**
     * 获取插件配置面板
     * @param Form $form
     */
    public static function config(Form $form)
    {
        self::searchConfig($form);
        self::contentConfig($form);
        self::captchaConfig($form);
        self::gravatarConfig($form);
        self::githubConfig($form);
    }

    /**
     * 个人用户的配置面板
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }
}
