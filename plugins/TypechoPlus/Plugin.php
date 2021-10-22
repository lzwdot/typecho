<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

require_once __DIR__ . '/lib/Common.php';
require_once __DIR__ . '/plugin/Search.php';
require_once __DIR__ . '/plugin/Content.php';
require_once __DIR__ . '/plugin/Captcha.php';
require_once __DIR__ . '/plugin/GitHub.php';
require_once __DIR__ . '/plugin/Gravatar.php';
require_once __DIR__ . '/plugin/HtmlCode.php';
require_once __DIR__ . '/Action.php';

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Widget\Action;
use Typecho\Plugin\Exception;
use Utils\Helper;
use Typecho\Cookie;
use Typecho\Widget\Helper\Layout;

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
        TypechoPlus_Plugin_Gravatar,
        TypechoPlus_Plugin_HtmlCode;

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
        self::htmlcodeActivate();

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
        $layout = new Layout('ul', ['class' => 'typecho-option']);
        $layout->html('<li><p class="description">本插件采用 Cookie 缓存配置，重新启用后保存即可</p></li>');
        $form->addItem($layout);

        self::searchConfig($form);
        self::contentConfig($form);
        self::captchaConfig($form);
        self::gravatarConfig($form);
        self::githubConfig($form);
        self::htmlcodeConfig($form);
    }

    /**
     * 个人用户的配置面板
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 手动保存
     * @param $settings
     * @param $isInit
     */
    public static function configHandle($settings, $isInit)
    {
        // 缓存数据
        !$isInit && Cookie::set('__typecho_plugin:' . self::$pluginName, serialize($settings));
        Helper::configPlugin(self::$pluginName, $settings, false);
    }
}
