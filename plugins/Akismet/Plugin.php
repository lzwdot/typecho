<?php

namespace TypechoPlugin\Akismet;

use Typecho\Common;
use Typecho\Http\Client;
use Typecho\Plugin\Exception;
use Typecho\Plugin\PluginInterface;
use Typecho\Request;
use Typecho\Widget\Helper\Form;
use Widget\Base;
use Widget\Comments\Edit;
use Widget\Feedback;
use Widget\Options;
use Widget\XmlRpc;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Akismet 反垃圾评论插件 for Typecho
 *
 * @package Akismet
 * @author joyqi
 * @version 1.2.0
 * @since 1.2.0
 * @link https://github.com/joyqi/typecho-plugins
 */
class Plugin implements PluginInterface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @throws Exception
     */
    public static function activate()
    {
        if (false == Client::get()) {
            throw new Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
        }

        Feedback::pluginHandle()->comment = __CLASS__ . '::filter';
        Feedback::pluginHandle()->trackback = __CLASS__ . '::filter';
        XmlRpc::pluginHandle()->pingback = __CLASS__ . '::filter';
        Edit::pluginHandle()->mark = __CLASS__ . '::mark';

        return _t('请配置此插件的API KEY, 以使您的反垃圾策略生效');
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
        $key = new Form\Element\Text(
            'key',
            null,
            null,
            _t('服务密钥'),
            _t('此密钥需要向服务提供商注册<br />它是一个用于表明您合法用户身份的字符串')
        );
        $form->addInput($key->addRule('required', _t('您必须填写一个服务密钥'))
            ->addRule([self::class, 'validate'], _t('您使用的服务密钥错误')));

        $url = new Form\Element\Text(
            'url',
            null,
            'https://rest.akismet.com',
            _t('服务地址'),
            _t('这是反垃圾评论服务提供商的服务器地址<br />
        我们推荐您使用 <a href="http://akismet.com">Akismet</a> 或者 <a href="http://antispam.typepad.com">Typepad</a> 的反垃圾服务')
        );
        $form->addInput($url->addRule('url', _t('您使用的地址格式错误')));
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
     * 验证api的key值
     *
     * @param string $key 服务密钥
     * @return boolean
     */
    public static function validate(string $key): bool
    {
        $options = Options::alloc();
        $url = Request::getInstance()->get('url');

        $data = [
            'key'  => $key,
            'blog' => $options->siteUrl
        ];

        $client = Client::get();
        if (false != $client) {
            $client->setData($data)
                ->setHeader('User-Agent', $options->generator . ' | Akismet/1.1')
                ->send(Common::url('/1.1/verify-key', $url));

            if ('valid' == $client->getResponseBody()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 标记评论状态时的插件接口
     *
     * @access public
     * @param array $comment 评论数据的结构体
     * @param Edit $commentWidget 评论组件
     * @param string $status 评论状态
     * @return void
     */
    public static function mark(array $comment, Edit $commentWidget, string $status)
    {
        if ('spam' == $comment['status'] && $status != 'spam') {
            self::filter($comment, $commentWidget, null, 'submit-ham');
        } elseif ('spam' != $comment['status'] && $status == 'spam') {
            self::filter($comment, $commentWidget, null, 'submit-spam');
        }
    }

    /**
     * 评论过滤器
     *
     * @param array $comment 评论结构
     * @param Base $post 被评论的文章
     * @param array $result 返回的结果上下文
     * @param string $api api地址
     * @return array
     */
    public static function filter(array $comment, Base $post, array $result, string $api = 'comment-check'): array
    {
        $comment = empty($result) ? $comment : $result;

        $options = Options::alloc();
        $url = $options->plugin('Akismet')->url;
        $key = $options->plugin('Akismet')->key;

        $allowedServerVars = 'comment-check' == $api ? [
            'SCRIPT_URI',
            'HTTP_HOST',
            'HTTP_USER_AGENT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_KEEP_ALIVE',
            'HTTP_CONNECTION',
            'HTTP_CACHE_CONTROL',
            'HTTP_PRAGMA',
            'HTTP_DATE',
            'HTTP_EXPECT',
            'HTTP_MAX_FORWARDS',
            'HTTP_RANGE',
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'SERVER_SIGNATURE',
            'SERVER_SOFTWARE',
            'SERVER_NAME',
            'SERVER_ADDR',
            'SERVER_PORT',
            'REMOTE_PORT',
            'GATEWAY_INTERFACE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'QUERY_STRING',
            'REQUEST_URI',
            'SCRIPT_NAME',
            'REQUEST_TIME'
        ] : [];

        $data = [
            'blog'                 => $options->siteUrl,
            'user_ip'              => $comment['ip'],
            'user_agent'           => $comment['agent'],
            'referrer'             => Request::getInstance()->getReferer(),
            'permalink'            => $post->permalink,
            'comment_type'         => $comment['type'],
            'comment_author'       => $comment['author'],
            'comment_author_email' => $comment['mail'],
            'comment_author_url'   => $comment['url'],
            'comment_content'      => $comment['text']
        ];

        foreach ($allowedServerVars as $val) {
            if (array_key_exists($val, $_SERVER)) {
                $data[$val] = $_SERVER[$val];
            }
        }

        try {
            $client = Client::get();
            if (false != $client && $key) {
                $params = parse_url($url);
                $url = $params['scheme'] . '://' . $key . '.' . $params['host'] . ($params['path'] ?? null);

                $client->setHeader('User-Agent', $options->generator . ' | Akismet/1.1')
                    ->setTimeout(5)
                    ->setData($data)
                    ->send(Common::url('/1.1/' . $api, $url));

                if ('true' == $client->getResponseBody()) {
                    $comment['status'] = 'spam';
                }
            }
        } catch (Client\Exception $e) {
            //do nothing
            error_log($e->getMessage());
        }

        return $comment;
    }
}
