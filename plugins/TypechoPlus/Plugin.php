<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

require_once __DIR__ . '/lib/Common.php';
require_once __DIR__ . '/plugin/Search.php';
require_once __DIR__ . '/plugin/Content.php';
require_once __DIR__ . '/plugin/Captcha.php';
require_once __DIR__ . '/plugin/GitHub.php';
require_once __DIR__ . '/Action.php';

/**
 * Typecho 多功能增强插件
 *
 * @package TypechoPlus
 * @author A.wei
 * @version 1.0.0
 * @link http://gravatar.cn
 */
class TypechoPlus_Plugin implements Typecho_Plugin_Interface
{
    use TypechoPlus_Lib_Common, TypechoPlus_Plugin_Search, TypechoPlus_Plugin_Content, TypechoPlus_Plugin_Captcha, TypechoPlus_Plugin_GitHub;

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        self::searchActivate();
        self::contentActivate();
        self::captchaActivate();
        self::githubActivate();

        TypechoPlus_Action::actionActivate();
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        TypechoPlus_Action::actionDeactivate();
    }

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        self::searchConfig($form);
        self::contentConfig($form);
        self::captchaConfig($form);
        self::githubConfig($form);
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
}
