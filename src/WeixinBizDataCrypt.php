<?php

namespace xuezhitech\wx;

class WeixinBizDataCrypt
{
    private $appid;
    private $sessionKey;
    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function __construct($appid, $sessionKey){
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
    }
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     *
     * @return bool|array 揭秘失败返回false，成功返回解密后的数据
     */
    public function decryptData($encryptedData, $iv){
        if (strlen($this->sessionKey) != 24) {
            return false;
        }
        $aesKey = base64_decode($this->sessionKey);
        if (strlen($iv) != 24) {
            return false;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result, true);
        if ($dataObj  == NULL) {
            return false;
        }
        if ($dataObj['watermark']['appid'] != $this->appid) {
            return false;
        }
        return $dataObj;
    }
}
