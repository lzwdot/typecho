<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

require_once __DIR__ . '/lib/Common.php';
require_once __DIR__ . '/action/Oauth.php';

use Widget\ActionInterface;
use Utils\Helper;
use Widget\Base\Users;
use Typecho\Plugin\Exception;

class TypechoPlus_Action extends Users implements ActionInterface
{
    use TypechoPlus_Lib_Common,
        TypechoPlus_Action_Oauth;

    /**
     * 激活
     */
    public static function actionActivate()
    {
        Helper::addAction('typecho-plus', get_class());
        Helper::addRoute('typecho-plus-oauth', '/oauth', get_class(), 'oauth');
        Helper::addRoute('typecho-plus-callback', '/callback', get_class(), 'callback');
        Helper::addRoute('typecho-plus-reset', '/typecho-plus-reset', get_class(), 'reset');
    }

    /**
     * 禁用
     */
    public static function actionDeactivate()
    {
        Helper::removeAction('typecho-plus');
        Helper::removeRoute('typecho-plus-oauth');
        Helper::removeRoute('typecho-plus-callback');
        Helper::removeRoute('typecho-plus-reset');
    }

    /**
     * 重置
     */
    public static function reset()
    {
        Helper::removePlugin(self::$pluginName);

        throw new Exception(_t('插件%s已经被重置', self::$pluginName), 500);
    }

    /**
     * 必须实现
     */
    public function action()
    {
        // 这里不用写了
    }
}
