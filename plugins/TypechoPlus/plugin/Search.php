<?php

use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Typecho\Widget\Helper\Form\Element\Checkbox;
use Widget\Archive;

trait TypechoPlus_Plugin_Search
{
    /**
     * 激活
     */
    public static function searchActivate()
    {
        Plugin::factory(Archive::class)->searchHandle = [get_class(), 'searchHandle'];
    }

    /**
     * 配置
     * @param Form $form
     */
    public static function searchConfig(Form $form)
    {
        $searchCheckbox = new Checkbox('searchCheckbox',
            [
                'searchKey' => _t('支持空格搜索')
            ], null, _t('搜索增强'));
        $form->addInput($searchCheckbox->multiMode());
    }

    /**
     * 处理
     * @param $that
     * @param $select
     * @throws Typecho_Exception
     */
    public static function searchHandle($that, $select)
    {
        $searchCheckbox = self::myOptions()->searchCheckbox ?? [];
        if (in_array('searchKey', $searchCheckbox)) {
            $keywords = $that->request->keywords;

            $that->setKeywords($keywords);
            $that->setPageRow(['keywords' => urlencode($keywords)]);
            $that->setArchiveTitle($keywords);
            $that->setArchiveSlug($keywords);

            $searchQuery = '%' . str_replace(' ', '%', $keywords) . '%';
            $select->orWhere('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery);
        }

        /** 仅输出文章 */
        $that->setCountSql(clone $select);
        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->page($that->getCurrentPage(), $that->parameter->pageSize);
        $that->query($select);
    }
}
