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
     *小程序 - subscribeMessage.send
     */
    public function subscribeMessage($token,$data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.$token;
        return json_decode($this->getCurlInfo($url,'POST','',$data),true);
    }

    /**
     *小程序 - 获取小程序全局唯一后台接口调用凭据（access_token）
     */
    public function getAccessToken(){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->config['appid'].'&secret='.$this->config['secret'];
        return json_decode($this->getCurlInfo($url,'GET','',''),true);
    }

    /**
     *小程序 - 获得用户手机号
     */
    public function getUserPhone($code){
        $token = $this->getAccessToken();
        if ( $token ) {
            $data = [
                'code'=>$code
            ];
            $headers = [
                'Accept'=>'application/json',
                'Content-type'=>'application/json'
            ];
            $url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token='.$token['access_token'];
            return json_decode($this->getCurlInfo($url,'POST',$headers,json_encode($data)),true);
        }else{
            $this->result['status'] = false;
            $this->result['data'] = '调用获得用户手机号接口失败';
            return $this->result;
        }
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
        $result = json_decode($this->getCurlInfo($url,'GET','',''),true);
        $this->result['status'] = true;
        $this->result['data'] = $result;
        return $this->result;
    }

    /**
     * 小程序 - 获取关联服务号素材列表
     * type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * count 返回素材的数量，取值在1到20之间
     */
    public function getFreepublish($access_token,$offset=0,$count=10,$no_content=0){

        $url = 'https://api.weixin.qq.com/cgi-bin/freepublish/batchget?access_token='.$access_token;
        $data = [
            'offset'=>$offset,
            'count'=>$count,
            'no_content'=>$no_content
        ];
        $result = json_decode($this->getCurlInfo($url,'POST','',json_encode($data)),true);
        $this->result['status'] = true;
        $this->result['data'] = $result;
        return $this->result;
    }

    private function getCurlInfo($url,$type='GET',$headers=[],$data=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, false);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if ( $headers ) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ( $type=='POST' ){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
