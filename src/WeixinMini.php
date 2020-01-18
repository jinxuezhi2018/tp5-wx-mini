<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2018/12/28
 * Time: 14:01
 */

namespace xuezhitech\wx;

class WeixinMini
{
    protected $config = [
        'appid' => '',
        'secret' => '',
        'grant_type' => 'authorization_code'
    ];

    protected $result = [
        'status'=>false,
        'msg'=>'',
        'data'=>[]
    ];

    public function __construct( $config=[] ){
        $this->config = array_merge($this->config,$config);
    }

    /**
     *小程序 - 获得用户手机号
     */
    public function getUserPhone($encryptedData,$iv,$session_key){
        $wxBizDataCrypt = new WeixinBizDataCrypt($this->config['appid'], $session_key);
        return $wxBizDataCrypt->decryptData($encryptedData, $iv);
    }

    /**
     *小程序 - check用户openid
     */
    public function checkUserOpenId($encryptedData,$iv,$session_key){
        $wxBizDataCrypt = new WeixinBizDataCrypt($this->config['appid'], $session_key);
        return $wxBizDataCrypt->decryptData($encryptedData, $iv);
    }

    /**
     *小程序 - 用户登录
     *$code - 调用wx.login() 获取 临时登录凭证code
     */
    public function authCode2Session($code){
        //获得小程序-登陆后的code
        if ( empty($code) ) {
            $this->result['msg'] = 'code不能为空';
            return $this->result;
        }
        //请求auth.code2Session接口
        $url = 'https://api.weixin.qq.com/sns/jscode2session'.
               '?appid='.$this->config['appid'].
               '&secret='.$this->config['secret'].
               '&js_code='.$code.
               '&grant_type='.$this->config['grant_type'];
        $result = json_decode($this->getCurlInfo($url),true);
        $this->result['status'] = true;
        $this->result['data'] = $result;
        return $this->result;
    }

    private function getCurlInfo($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
