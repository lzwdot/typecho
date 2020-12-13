<?php
require_once __DIR__ . '/Common.php';

/**
 * Trait Search
 */
trait Search
{
    use Common;

    /**
     * 激活
     */
    public static function activate_search()
    {
        Typecho_Plugin::factory('Widget_Archive')->searchHandle = array(get_class(), 'searchHandle');
    }

    /**搜索处理
     * @param $that
     * @param $select
     */
    public static function searchHandle($that, $select)
    {
        $keywords = $that->request->keywords;

        $that->setKeywords($keywords);
        $that->setPageRow(array('keywords' => urlencode($keywords)));
        $that->setArchiveTitle($keywords);
        $that->setArchiveSlug($keywords);

        $searchQuery = '%' . str_replace(' ', '%', $keywords) . '%';
        $select->orWhere('table.contents.title LIKE ? OR table.contents.text LIKE ?', $searchQuery, $searchQuery);

        /** 仅输出文章 */
        $that->setCountSql(clone $select);
        $select->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->page($that->getCurrentPage(), $that->parameter->pageSize);
        $that->query($select);
    }
}