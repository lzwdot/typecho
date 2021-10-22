<?php

namespace TypechoPlugin\HighlightJs;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Utils\Helper;
use Widget\Archive;
use Widget\Base\Comments;
use Widget\Base\Contents;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Highlight.JS插件，智能实现代码高亮
 *
 * @package Highlight Js
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
        Contents::pluginHandle()->contentEx = __CLASS__ . '::parse';
        Contents::pluginHandle()->excerptEx = __CLASS__ . '::parse';
        Comments::pluginHandle()->contentEx = __CLASS__ . '::parse';
        Archive::pluginHandle()->header = __CLASS__ . '::header';
        Archive::pluginHandle()->footer = __CLASS__ . '::footer';
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     *
     * @param Form $form 配置面板
     */
    public static function config(Form $form)
    {
        $compatibilityMode = new Form\Element\Radio('compatibilityMode', [
            0 => _t('不启用'),
            1 => _t('启用')
        ], 0, _t('兼容模式'), _t('兼容模式一般用于对以前没有使用Markdown语法解析的文章'));
        $form->addInput($compatibilityMode->addRule('enum', _t('必须选择一个模式'), [0, 1]));

        $styles = array_map('basename', glob(dirname(__FILE__) . '/res/styles/*.css'));
        $styles = array_combine($styles, $styles);
        $style = new Form\Element\Select('style', $styles, 'default.css',
            _t('代码配色样式'));
        $form->addInput($style->addRule('enum', _t('必须选择配色样式'), $styles));
    }

    /**
     * 个人用户的配置面板
     *
     * @param Form $form
     */
    public static function personalConfig(Form $form)
    {
    }

    /**
     * 输出头部css
     */
    public static function header()
    {
        $cssUrl = Helper::options()->pluginUrl . '/HighlightJs/res/styles/'
            . Helper::options()->plugin('HighlightJs')->style;
        echo '<link rel="stylesheet" type="text/css" href="' . $cssUrl . '" />';
    }

    /**
     * 输出尾部js
     */
    public static function footer()
    {
        $jsUrl = Helper::options()->pluginUrl . '/HighlightJs/res/highlight.pack.js';
        echo '<script type="text/javascript" src="' . $jsUrl . '"></script>';
        echo '<script type="text/javascript">window.onload = function () {
var codes = document.getElementsByTagName("pre"),
    hlNames = {
        actionscript : /^as[1-3]$/i,
        cmake : /^(make|makefile)$/i,
        cs : /^csharp$/i,
        css : /^css[1-3]$/i,
        delphi : /^pascal$/i,
        javascript : /^js$/i,
        markdown : /^md$/i,
        objectivec : /^objective\-c$/i,
        php  : /^php[1-6]$/i,
        sql : /^mysql$/i,
        xml : /^(html|html5|xhtml)$/i
    }, hlLangs = hljs.LANGUAGES;

for (var i = 0; i < codes.length; i ++) {
    var children = codes[i].getElementsByTagName("code"), highlighted = false;

    if (children.length > 0) {
        var code = children[0], className = code.className;

        if (!!className) {
            if (0 == className.indexOf("language-")) {
                var lang = className.substring(5).toLowerCase(), finalLang;
            
                if (hlLangs[lang]) {
                    finalLang = lang;
                } else {
                    for (var l in hlNames) {
                        if (lang.match(hlNames[l])) {
                            finalLang = l;
                        }
                    }
                }

                if (!!finalLang) {
                    var result = hljs.highlight(finalLang, code.textContent, true);
                    code.innerHTML = result.value;
                    highlighted = true;
                }
            }
        }

        if (!highlighted) {
            var html = code.innerHTML;
            code.innerHTML = html.replace(/<\/?[a-z]+[^>]*>/ig, "");
            hljs.highlightBlock(code, "", false);
        }
    }
}
}</script>';
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return string
     */
    public static function parse($text, $widget, $lastResult): string
    {
        $text = empty($lastResult) ? $text : $lastResult;

        if (!Helper::options()->plugin('HighlightJs')->compatibilityMode) {
            return $text;
        }

        if ($widget instanceof Archive || $widget instanceof Comments) {
            $isMarkdown = $widget instanceof Comments ? Helper::options()->commentsMarkdown : $widget->isMarkdown;

            return preg_replace_callback("/<(code|pre)(\s*[^>]*)>(.*?)<\/\\1>/is", function ($matches) use ($isMarkdown) {
                if ('code' == $matches[1] && !$isMarkdown) {
                    $language = $matches[2];

                    if (!empty($language)) {
                        if (preg_match("/^\s*(class|lang|language)=\"(?:lang-|language-)?([_a-z0-9-]+)\"$/i", $language, $out)) {
                            $language = ' class="' . trim($out[2]) . '"';
                        } elseif (preg_match("/\s*([_a-z0-9]+)/i", $language, $out)) {
                            $language = ' class="language-' . trim($out[1]) . '"';
                        }
                    }

                    return "<pre><code{$language}>" . htmlspecialchars(trim($matches[3])) . "</code></pre>";
                }

                return $matches[0];
            }, $text);
        } else {
            return $text;
        }
    }
}
