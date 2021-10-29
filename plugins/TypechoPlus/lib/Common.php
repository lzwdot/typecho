<?php

use Widget\Options;
use Widget\Notice;
use Typecho\Db;
use Typecho\Cookie;
use Typecho\Plugin\Exception;

/**
 * Trait Common
 */
trait TypechoPlus_Lib_Common
{
    //插件名称
    private static $pluginName = 'TypechoPlus';
    private static $pluginAction = 'TypechoPlus_Action';
    private static $mcrypt_key = 'a!takA:dlmcldEv,e';

    /**
     * 获取选项
     * @return mixed|\Typecho\Config|void
     */
    public static function myOptions()
    {
        $options = Options::alloc();
        $settings = Cookie::get('__typecho_plugin:' . self::$pluginName);
        // 使用 cookie 缓存配置，或 ”配置信息没有找到“异常
        $options->push(['name' => 'plugin:' . self::$pluginName, 'value' => $settings ?? serialize([])]);

        return $options->plugin(self::$pluginName);
    }

    /**
     * 获取方法
     * @return \#P#Ф\TypechoPlus_Lib_Common.pluginAction|Widget
     */
    public static function myAction()
    {
        return self::$pluginAction;
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

    /**
     * 加密
     * @param $string
     * @param string $key
     * @param int $expiry
     * @return array|string|string[]
     */
    public static function enMcrypt($string, $key = '', $expiry = 0)
    {
        $ckeyLength = 4;
        $key = md5($key ? $key : self::$mcrypt_key); //解密密匙
        $keya = md5(substr($key, 0, 16));         //做数据完整性验证
        $keyb = md5(substr($key, 16, 16));         //用于变化生成的密文 (初始化向量IV)
        $keyc = substr(md5(microtime()), -$ckeyLength);
        $cryptkey = $keya . md5($keya . $keyc);
        $keyLength = strlen($cryptkey);
        $string = sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $stringLength = strlen($string);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }

        $box = range(0, 255);
        // 打乱密匙簿，增加随机性
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 加解密，从密匙簿得出密匙进行异或，再转成字符
        $result = '';
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        $result = $keyc . str_replace('=', '', base64_encode($result));
        $result = str_replace(array('+', '/', '='), array('-', '_', '.'), $result);
        return $result;
    }

    /**
     * 解密
     * @param $string
     * @param string $key
     * @return false|string
     */
    public static function deMcrypt($string, $key = '')
    {
        $string = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
        $ckeyLength = 4;
        $key = md5($key ? $key : self::$mcrypt_key); //解密密匙
        $keya = md5(substr($key, 0, 16));         //做数据完整性验证
        $keyb = md5(substr($key, 16, 16));         //用于变化生成的密文 (初始化向量IV)
        $keyc = substr($string, 0, $ckeyLength);
        $cryptkey = $keya . md5($keya . $keyc);
        $keyLength = strlen($cryptkey);
        $string = base64_decode(substr($string, $ckeyLength));
        $stringLength = strlen($string);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $keyLength]);
        }

        $box = range(0, 255);
        // 打乱密匙簿，增加随机性
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 加解密，从密匙簿得出密匙进行异或，再转成字符
        $result = '';
        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0)
            && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)
        ) {
            return substr($result, 26);
        } else {
            return '';
        }
    }
}
