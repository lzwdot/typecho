<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeInit($archive)
{
    if ($archive->is('index')) {
        if ($archive->request->page) {
            $archive->setThemeFile('archive.php');
        } else {
            $archive->parameter->pageSize = 10; // 自定义条数
        }
    }
}

function themeConfig($form)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点 LOGO 地址'),
        _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO')
    );

    $form->addInput($logoUrl);

    $sidebarBlock = new \Typecho\Widget\Helper\Form\Element\Checkbox(
        'sidebarBlock',
        [
            'ShowRecentPosts'    => _t('显示最新文章'),
            'ShowRecentComments' => _t('显示最近回复'),
            'ShowCategory'       => _t('显示分类'),
            'ShowArchive'        => _t('显示归档'),
            'ShowOther'          => _t('显示其它杂项')
        ],
        ['ShowRecentPosts', 'ShowRecentComments', 'ShowCategory', 'ShowArchive', 'ShowOther'],
        _t('侧边栏显示')
    );

    $form->addInput($sidebarBlock->multiMode());

    //友情链接
    $links = new \Typecho\Widget\Helper\Form\Element\Textarea('links', NULL, NULL, _t('友情链接'), _t('在这里填入友情链接的 HTML 代码'));
    $form->addInput($links);

    //备案号
    $icpNum = new \Typecho\Widget\Helper\Form\Element\Text('icpNum', NULL, NULL, _t('ICP 备案号'), _t('在这里填入 ICP 备案号'));
    $form->addInput($icpNum);

    //统计代码
    $statistics = new \Typecho\Widget\Helper\Form\Element\Textarea('statistics', NULL, NULL, _t('统计代码'), _t('在这里填入统计的代码'));
    $form->addInput($statistics);
}

/*
function themeFields($layout)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点LOGO地址'),
        _t('在这里填入一个图片URL地址, 以在网站标题前加上一个LOGO')
    );
    $layout->addItem($logoUrl);
}
*/
