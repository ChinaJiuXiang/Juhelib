<?php
namespace Juhelib;
use Qcloud\Sms\SmsSingleSender;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
class sms
{
    private static $config = [];

    /**
     * 读取数据配置
     * @param $name
     * @return mixed|string
     */
    private static function getConfig($name) {
        return empty(self::$config[$name]) ? null : self::$config[$name];
    }

    /**
     * 设置数据配置
     * @param $config
     */
    public static function setConfig($config) {
        foreach ($config as $key => $value) {
            self::$config[$key] = empty($value) ? !empty(self::$config[$key]) ? self::$config[$key] : null : $value;
        }
    }

    /**
     * （国内）短信发送
     * @param string $phone 手机号
     * @param array $params 参数
     * @return bool 返回 bool 值
     * @throws ClientException
     * @throws ServerException
     */
    public static function sendSms($phone, $params)
    {
        // 判断短信引擎
        if(self::getConfig('engine') == null){  // 禁止使用短信
            return false;
        }elseif(self::getConfig('engine') == 'qcloud'){  // 腾讯云短信
            if(empty($params)) return false;
            $sender = new SmsSingleSender(self::getConfig('qcloud_appid'), self::getConfig('qcloud_appkey'));
            $result = $sender->sendWithParam("86", $phone, self::getConfig('qcloud_template_id'), $params, self::getConfig('qcloud_sign'), "", "");
            $rsp = json_decode($result, true);
            // 返回 bool 值
            return ($rsp['errmsg'] == 'OK') ? true : false;
        }elseif(self::getConfig('engine') == 'alisms'){  // 阿里云短信
            if(empty($params)) return false;
            AlibabaCloud::accessKeyClient(self::getConfig('alisms_keyid'), self::getConfig('alisms_secret'))
                ->regionId('cn-hangzhou')->asGlobalClient();
            $result = AlibabaCloud::rpcRequest()->product('Dysmsapi')->version('2017-05-25')
                ->action('SendSms')->method('POST')->options(['query' => [
                    'RegionId' => 'cn-hangzhou',
                    'PhoneNumbers' => $phone,
                    'SignName' => self::getConfig('alisms_sign'),
                    'TemplateCode' => self::getConfig('alisms_template_code'),
                    'TemplateParam' => json_encode($params, JSON_UNESCAPED_UNICODE)
                ]])->request();
            $rsp = $result->toArray();
            // 返回 bool 值
            return ($rsp['Code'] == 'OK') ? true : false;
        }
    }

}
?>