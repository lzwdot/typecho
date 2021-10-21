<?php

use Typecho\Widget;
use Widget\Options;
use Widget\Notice;
use Typecho\Db;

/**
 * Trait Common
 */
trait TypechoPlus_Lib_Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';
    private static $pluginAction = 'TypechoPlus_Action';

    /**
     * 获取选项
     * @return mixed|\Typecho\Config
     * @throws \Typecho\Plugin\Exception
     */
    public static function myOptions()
    {
        return Options::alloc()->plugin(self::$pluginName);
    }

    /**
     * 获取方法
     * @return \#P#Ф\TypechoPlus_Lib_Common.pluginAction|Widget
     */
    public static function myAction()
    {
        return Widget::widget(self::$pluginAction);
    }


    /**
     * 消息通知
     * @param string $mgs
     * @param string $url
     */
    public static function msgNotice($mgs = '', $url = '')
    {
        $notice = Notice::alloc();
        $notice->set($mgs);

        $url ? $notice->response->redirect($url) : $notice->response->goBack();
    }

    /**
     * 自动在新的窗口打开
     * @param $content
     * @return string|string[]
     */
    public static function autoBlank($content)
    {
        $content = str_replace('<a', '<a target="_blank"', $content);

        return $content;
    }

    /**
     * 添加表字段
     * @param $columnName
     * @param $tableName
     * @throws Db\Exception
     */
    public static function addTableColumn($columnName, $tableName)
    {
        $db = Db::get();
        $select = $db->select('COLUMN_NAME')
            ->from('information_schema.COLUMNS')
            ->where('table_name = ?', $db->getPrefix() . 'users');
        $columns = $db->fetchAll($select);
        $columns = !empty($columns) ? array_column($columns, 'COLUMN_NAME') : $columns;

        if (!in_array($columnName, $columns)) {
            $db->query('ALTER TABLE  `' . $db->getPrefix() . $tableName . '` ADD COLUMN `' . $columnName . '` varchar(64) DEFAULT NULL', $db::WRITE);
        }
    }
}
