<?php

use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Typecho\Widget\Helper\Form\Element\Text;
use Widget\Base\Contents;
use Utils\Helper;

trait TypechoPlus_Plugin_Content
{
    /**
     * 激活
     */
    public static function contentActivate()
    {
        Plugin::factory(Contents::class)->filter = [get_class(), 'contentFilter'];
        Plugin::factory(Contents::class)->contentEx = [get_class(), 'contentExHandle'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function contentConfig(Form $form)
    {
        $contentCheckbox = new Checkbox('contentCheckbox',
            [
                'showTitle'   => _t('加密文章显示标题'),
                'moreSplit'   => _t('<--more--> 后面内容加密'),
                'targetBlank' => _t('内容链接以“_blank”方式打开'),
            ]
            , null, _t('内容显示'));
        $form->addInput($contentCheckbox->multiMode());

        $imageCdnUrl = new Text('imageCdnUrl', null, null, '');
        $imageCdnUrl->input->setAttribute('placeholder', _t('图片 CDN 地址，谨慎使用'));
        $form->addInput($imageCdnUrl);
    }

    /**
     * 处理
     * @param $value
     * @param $that
     * @return mixed
     * @throws Plugin\Exception
     */
    public static function contentFilter($value, $that)
    {
        $contentCheckbox = self::myOptions()->contentCheckbox;
        if ($contentCheckbox && in_array('showTitle', $contentCheckbox)) {
            if ($value['hidden']) {
                $value['hidden'] = false;
                $value['required_pwd'] = true;
            }
        }

        return $value;
    }

    /**
     * 处理
     * @param $text
     * @param $that
     * @return string
     */
    public static function contentExHandle($content, $that)
    {
        $security = Helper::security();
        $contentCheckbox = self::myOptions()->contentCheckbox;
        $imageCdnUrl = self::myOptions()->imageCdnUrl;

        if (empty($contentCheckbox) && empty($imageCdnUrl)) return $content;

        if (!empty($that->required_pwd)) {
            if (in_array('moreSplit', $contentCheckbox)) {
                $content = explode('<!--more-->', $content)[0];
            } else {
                $content = '';
            }

            $content .= '<form class="protected" action="' . $security->getTokenUrl($that->permalink)
                . '" method="post">' .
                '<p class="word">' . _t('请输入密码访问') . '</p>' .
                '<p><input type="password" class="text" name="protectPassword" />
                    <input type="hidden" name="protectCID" value="' . $that->cid . '" />
                    <input type="submit" class="submit" value="' . _t('提交') . '" /></p>' .
                '</form>';
        }

        if (in_array('targetBlank', $contentCheckbox)) {
            $content = self::autoBlank($content);
        }

        if ($imageCdnUrl) {
            $content = preg_replace_callback(
                '/<img.*?src="(.*?)".*?alt="(.*?)".*?\/?>/i',
                function ($matches) {
                    $url = $matches[1];

                    $url = (strpos($url, 'http') === false || strpos($url, $_SERVER['HTTP_HOST']) === false)
                        ? self::myOptions()->imageCdnUrl . substr($url, strpos($url, '/'))
                        : $url;
                    return str_replace($matches[1], $url, $matches[0]);
                },
                $content
            );
        }

        return $content;
    }
}
