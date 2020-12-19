<?php


trait TypechoPlus_Plugin_Search
{
    /**
     * 激活
     */
    public static function searchActivate()
    {
        Typecho_Plugin::factory('Widget_Archive')->searchHandle = array(get_class(), 'searchHandle');
    }

    /**
     * 配置
     * @param Typecho_Widget_Helper_Form $form
     */
    public static function searchConfig(Typecho_Widget_Helper_Form $form)
    {
        $search = new Typecho_Widget_Helper_Form_Element_Checkbox('search', array(_t('支持空格搜索')), null, _t('搜索增强'));
        $form->addInput($search);
    }

    /**
     * 处理
     * @param $that
     * @param $select
     * @throws Typecho_Exception
     */
    public static function searchHandle($that, $select)
    {
        if (!empty(self::myOptions()->search)) {
            $keywords = $that->request->keywords;

            $that->setKeywords($keywords);
            $that->setPageRow(array('keywords' => urlencode($keywords)));
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
