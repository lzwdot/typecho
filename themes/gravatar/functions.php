<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('站点 LOGO 地址'), _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO'));
    $form->addInput($logoUrl);

    $sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox('sidebarBlock',
        array('ShowRecentPosts' => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory' => _t('显示分类'),
            'ShowArchive' => _t('显示归档'),
            'ShowOther' => _t('显示其它杂项')),
        array('ShowRecentComments', 'ShowOther'), _t('侧边栏显示'));

    $form->addInput($sidebarBlock->multiMode());

    //友情链接
    $links = new Typecho_Widget_Helper_Form_Element_Textarea('links', NULL, NULL, _t('友情链接'), _t('在这里填入友情链接的 HTML 代码'));
    $form->addInput($links);

    //备案号
    $icpNum = new Typecho_Widget_Helper_Form_Element_Text('icpNum', NULL, NULL, _t('ICP 备案号'), _t('在这里填入 ICP 备案号'));
    $form->addInput($icpNum);

    //统计代码
    $statistics = new Typecho_Widget_Helper_Form_Element_Textarea('statistics', NULL, NULL, _t('统计代码'), _t('在这里填入统计的代码'));
    $form->addInput($statistics);
}

/**
 * 自动在新的窗口打开
 * @param $content
 * @return mixed
 */
function autoBlank($content)
{
    $content = str_replace('<a', '<a target="_blank"', $content);
    return $content;
}

/**
 * 主题初始化
 * @param $that
 */
function themeInit($that)
{
    //自动blank
    $that->content = autoBlank($that->content);
}

