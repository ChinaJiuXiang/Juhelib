<?php
namespace Juhelib;
use Juhelib\extend\email\config;
class email
{
    /**
     * 邮箱参数配置
     * @param $config
     */
    public static function setConfig($config)
    {
        config::$engine = empty($config['engine']) ? config::$engine : $config['engine'];
        config::$nickname = empty($config['nickname']) ? config::$nickname : $config['nickname'];
        config::$server = empty($config['server']) ? config::$server : $config['server'];
        config::$port = empty($config['port']) ? config::$port : $config['port'];
        config::$username = empty($config['username']) ? config::$username : $config['username'];
        config::$password = empty($config['password']) ? config::$password : $config['password'];
        config::$debug = empty($config['debug']) ? config::$debug : $config['debug'];
        config::$sitename = empty($config['sitename']) ? config::$sitename : $config['sitename'];
        config::$secure = empty($config['secure']) ? config::$secure : $config['secure'];
    }

    /**
     * 邮件发送
     * @param string $to 需要发送的邮箱
     * @param string $title 邮件标题
     * @param string $content 邮件内容
     * @return bool 是否发送成功
     */
    public static function send($to, $title, $content)
    {
        // 拼接类库名称
        $object_name = "engine_".config::$engine;
        // 静态化调用类库函数
        $return = call_user_func_array(
            array("\\Juhelib\\extend\\email\\".$object_name, 'send'),
            array($to, $title, mb_convert_encoding(htmlspecialchars_decode($content), "utf-8"))
        );
        // 返回 bool 值
        return ($return == "success") ? true : false;
    }

    /**
     * 发送验证码
     * @param string $to 需要发送的邮箱
     * @param string $title 邮件标题 - 模板
     * @param string $content 邮件内容 - 模板
     * @param int $code 验证码
     * @param int $code_time 验证码有效期（秒）
     * @return bool 是否发送成功
     */
    public static function sendCode($to, $title, $content, $code, $code_time)
    {
        // 处理标题中的标签
        $title = batch_str_replace(
            $title, array('[:sitename:]'), array(config::$sitename)
        );
        // 处理内容中的标签
        $content = batch_str_replace(
            $content, array('[:code:]', '[:codetime:]', '[:date:]'),
            array($code, strval($code_time / 60), date("Y-m-d", time())
            ));
        // 发送邮件
        return self::send($to, $title, $content);
    }
}
?>