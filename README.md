# 自用型类库

#### 介绍
Self use php class library

#### 安装教程
```php
composer require chinajiuxiang/juhelib
```
#### email 类使用方法
```php
use Juhelib\email;
...
    email::setConfig([
        "engine" => "2", // 1、基于socket协议（不支持 ssl） 2、phpmailer（支持 tls / ssl）
        "nickname" => "xxxx", // 发信昵称
        "server" => "xxxx", // smtp 地址
        "port" => "587", // smtp 端口，默认 587（可选）
        "secure" => "tls", // 启用 tls / ssl 加密，默认 tls（可选）
        "username" => "xxxx", // 账户
        "password" => "xxxx", // 密码
        "debug" => "false", // 是否开启 debug（可选）
        "sitename" => "我的小站" // 站点名称（可选）
    ]);
    
    // 发送邮件，返回 bool（是否发送成功）
    var_dump(email::send('xxxx@qq.com','邮件标题','邮件内容'));
    
    // 发送验证码邮件（可使用标签），返回 bool（是否发送成功）
    $code = 123456; // 验证码
    $code_time = 120; // 验证码有效期（秒）
    var_dump(email::sendCode('xxxx@qq.com','[:sitename:] - 邮箱验证',
    '您的验证为：[:code:]，有效期为：[:codetime:]分钟，请尽快使用。当前时间：[:date:]',
    $code, $code_time));
    
    // 以上代码发送的邮件信息如下：
    // 邮件标题：我的小站 - 邮箱验证
    // 邮件内容：您的验证为：123456，有效期为：2分钟，请尽快使用。当前时间：2019-06-22
...
```
#### file 类使用方法
```php
use Juhelib\file;
...
    // 上传到七牛云
    file::setConfig([
        'engine' => 'qiniu', // 引擎
        'qiniu_url_agree' => 'http', // 协议
        'qiniu_domain' => 'domain', // 七牛云地址
        'qiniu_key_access' => '你的 accessKey',
        'qiniu_key_secret' => '你的 secretKey',
        'qiniu_bucket_name' => 'bucket_name' // 空间名称
    ]);
    // 上传到阿里云OSS
    file::setConfig([
        'engine' => 'alioss',
        'alioss_key_access' => '你的 accessKey',
        'alioss_key_secret' => '你的 secretKey',
        'alioss_endpoint' => '数据节点', // 我这里用的是杭州节点，http://oss-cn-hangzhou.aliyuncs.com
        'alioss_bucket_name' => '空间名称'
    ]);
    // 上传到腾讯COS
    file::setConfig([
        'engine' => 'qcloud',
        'qcloud_region' => '存储桶地域，例如：ap-chengdu',
        'qcloud_secret_id' => '你的 secretId',
        'qcloud_secret_key' => '你的 secretKey',
        'qcloud_bucket' => '格式：BucketName-APPID'
    ]);     
    // 上传到本地
    file::setConfig([
        'engine' => 'local'
    ]);
    
    // 开始文件上传，返回上传成功后的（数组）图片地址
    var_dump(file::upload([
        'upload_folder' => 'upload', // 这里填写本地存放目录
        'file_size' => '4096' // 这里可以限制文件大小，默认设置 4M 文件限制，默认单位为KB
    ]));
...
```
#### sms 类使用方法，发送短信验证码
```php
use Juhelib\sms;
...
    // 腾讯云短信
    sms::setConfig([
        'engine' => 'qcloud', // 短信引擎
        'qcloud_appid' => '你的 appid',
        'qcloud_appkey' => '你的 appkey',
        'qcloud_template_id' => '短信模板 ID',
        'qcloud_sign' => '短信签名',
    ]);
    // 发送短信，返回 bool（是否发送成功）
    var_dump(sms::sendSms('手机号',
        ['验证码'] // 短信参数
    )); 
    
    // 阿里短信
    sms::setConfig([
        'engine' => 'alisms', // 短信引擎
        'alisms_keyid' => '你的 keyid',
        'alisms_secret' => '你的 secret',
        'alisms_template_code' => '短信模板 code',
        'alisms_sign' => '短信签名',
    ]);
    // 发送短信，返回 bool（是否发送成功）
    var_dump(sms::sendSms('手机号',
        ['code' => '验证码'] // 短信参数
    ));
...
```
#### image 类使用方法
```php
use Juhelib\image;
...
    // 压缩图片
    image::compressedPictures('图片路径', '压缩后保存路径');
    // 判断是否 gif 动画
    image::check_gifcartoon('图片路径');
...
```
#### ip 类使用方法
```php
use Juhelib\ip;
...
    // 获取 IP 地址
    ip::getIP();
    // 获取 IP 数据，返回 [Array] 数据
    ip::getData('qqwry'); // 可选参数: taobao/ip138/qqwry
...
```
#### hook 类使用方法
```php
use Juhelib\hook;
...
    // 注册钩子
    hook::add('name', function() {
        echo 'hello world';
    });
    // 执行钩子
    hook::run('name');
...
```
#### excel 类使用方法
```php
use Juhelib\excel;
...
    // 读取 excel 文件，返回 [Array] 数据
    excel::getExcel('文件路径');
...
```
#### safe 类使用方法
```php
use Juhelib\safe;
...
    // 字符串加密模块
    safe::str_encrypt('源字符串', '密钥');
    // 字符串解密模块
    safe::str_decrypt('加密过的字符串', '密钥');
    // 文件加密模块
    safe::file_encrypt('源地址', '加密后的文件地址', '密钥');
    // 文件解密模块
    safe::file_decrypt('加密过的文件地址', '解密后的文件地址', '密钥');
...
```
#### system 类使用方法
```php
use Juhelib\system;
...
    // 获取物理 Mac 地址
    system::get_mac_addr();
    // 生成机器码
    system::machine_code('混淆码');
...
```
#### check 类使用方法
```php
use Juhelib\check;
...
    // 验证银行卡号是否有效（前提为16位或19位数字组合）
    check::isBankCard('银行卡号');
    // 验证身份证号是否正确
    check::isIdentityCard('身份证号');
    // 验证url地址
    check::isUrl('Url', '协议，默认 Http');
    // 验证电话号码
    check::isPhone('电话号码');
    // 验证邮箱
    check::isEmail('邮箱');
    // 验证邮编
    check::isZip('邮编');
    // 是否是数字
    check::isNumeric('传递值');
    // 是否超过最大长度
    check::isMaxLength('传递值', '最大长度');
    // 超过最大数
    check::isMaxValue('传递值', '最大值');
    // 验证用户名（只允许下划线+汉字+英文+数字（不支持其它特殊字符））
    check::isUsername('传递值', '最小长度', '最大长度');
    // 是否为空值
    check::isEmpty('传递值');
    // 数字验证
    check::isNum('传递值');
    // 是否是数字或英文组合
    check::isNumLetter('传递值');
    // 是否同时包含数字和英文，必须同时包含数字和英文
    check::isNumAndLetter('传递值');
    // 微信号验证
    check::isWebchat('微信号');
...
```
#### 后续
setConfig 方法可以一次性赋值，拿 sms 类为例
```php
use Juhelib\sms;
...
    sms::setConfig([
        'engine' => 'qcloud', // 短信引擎
        'qcloud_appid' => '你的 appid',
        'qcloud_appkey' => '你的 appkey',
        'qcloud_template_id' => '短信模板 ID',
        'qcloud_sign' => '短信签名',
        'alisms_keyid' => '你的 keyid',
        'alisms_secret' => '你的 secret',
        'alisms_template_code' => '短信模板 code',
        'alisms_sign' => '短信签名',
    ]);
...