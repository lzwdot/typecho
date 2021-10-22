<?php

use Typecho\Plugin;
use Widget\Archive;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Textarea;

trait TypechoPlus_Plugin_HtmlCode
{
    /**
     * 激活
     */
    public static function htmlcodeActivate()
    {
        Plugin::factory(Archive::class)->header = [get_class(), 'htmlcodeHeader'];
        Plugin::factory(Archive::class)->footer = [get_class(), 'htmlcodeFooter'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function htmlcodeConfig(Form $form)
    {
        // header code
        $headerCode = new Textarea('htmlcodeHeader', null, null, _t('自定义 HTML 代码'));
        $headerCode->input->setAttribute('placeholder', _t('填入主题 Header 代码'));
        $form->addInput($headerCode);

        // footer code
        $footerCode = new Textarea('htmlcodeFooter', null, null, '', _t('在这里填入 JS CSS 相关代码'));
        $footerCode->input->setAttribute('placeholder', _t('填入主题 Footer 代码'));
        $form->addInput($footerCode);
    }

    /**
     * hader 代码
     * @param $header
     * @return mixed|string
     * @throws Plugin\Exception
     */
    public static function htmlcodeHeader($header, $that)
    {
        $htmlcodeHeader = self::myOptions()->htmlcodeHeader;
        if ($htmlcodeHeader) {
            echo $htmlcodeHeader;
        };
    }

    /**
     * footer 代码
     * @param $that
     * @throws Plugin\Exception
     */
    public static function htmlcodeFooter($that)
    {
        $htmlcodeFooter = self::myOptions()->htmlcodeFooter;
        if ($htmlcodeFooter) {
            echo $htmlcodeFooter;
        };
    }
}
