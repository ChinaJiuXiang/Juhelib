<?php
namespace Juhelib\extend\email;
date_default_timezone_set("PRC");
class config
{
    static $engine = '1';
    static $nickname = null;
    static $server = null;
    static $port = '587';
    static $username = null;
    static $password = null;
    static $debug = 'false';
    static $sitename = null;
    static $secure = 'tls';
}