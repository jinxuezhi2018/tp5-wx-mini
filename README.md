## tp5-wx-mini
thinkphp5.1 微信小程序后台接口
## 安装
composer require xuezhitech/tp5-wx-mini
## 使用

# 声明namespace
use xuezhitech\wx\WeixinMini;
# new新对象
$this->wxmini = new WeixinMini(['appid'=>$mini_appid,'secret'=>$mini_secret);

## 获得openid 
$this->wxmini->authCode2Session($code);

## 获得用户手机号
$this->wxmini->getUserPhone($code);



