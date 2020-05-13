<?php
namespace Juhelib;
class hook
{
    private static $list = [];

    /**
     * 注册钩子
     * @param $name
     * @param $func
     */
    public static function add($name, $func)
    {
        self::$list[$name][] = $func;
    }

    /**
     * 执行钩子
     * @param $name
     * @param null $params
     */
    public static function run($name, $params = null)
    {
        foreach ((empty(self::$list[$name]) ? [] : self::$list[$name]) as $k => $v) {
            call_user_func($v, $params);
        }
    }
}