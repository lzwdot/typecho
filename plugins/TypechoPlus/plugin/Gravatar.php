<?php

use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Select;

trait TypechoPlus_Plugin_Gravatar
{
    /**
     * 激活
     */
    public static function gravatarActivate()
    {
        Plugin::factory('index.php')->begin = [get_class(), 'gravatarHandle'];
        Plugin::factory('admin/common.php')->begin = [get_class(), 'gravatarHandle'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function gravatarConfig(Form $form)
    {
        $gravatarSelect = new Select('gravatarPrefix',
            [
                ''                                  => _t('选择 Gravatar 头像加速镜像'),
                'https://cravatar.cn/avatar/'       => _t('https://cravatar.cn/avatar/'),
                'https://gravatar.loli.net/avatar/' => _t('https://gravatar.loli.net/avatar/'),
                'https://sdn.geekzu.org/avatar/'    => _t('https://sdn.geekzu.org/avatar/'),
                'https://sdn.geekzu.org/avatar/'    => _t('https://sdn.geekzu.org/avatar/'),
            ], 'ShowOther', _t('Gravatar 头像加速'));
        $form->addInput($gravatarSelect->multiMode());
    }

    /**
     * 处理
     */
    public static function gravatarHandle()
    {
        $gravatarPrefix = self::myOptions()->gravatarPrefix;

        if ($gravatarPrefix) {
            define('__TYPECHO_GRAVATAR_PREFIX__', $gravatarPrefix);
        }
    }
}
