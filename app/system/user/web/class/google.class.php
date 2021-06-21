<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::mod_class('user/web/class/other');

class google extends other
{
    /**
     * Google
     * google constructor.
     */
    public function __construct()
    {
        global $_M;
        if (!$_M['config']['met_google_open']) {
            die("Google login cloased");
        }
        $this->table = $_M['table']['user_other'];
        $this->type = 'google';
        $this->client_id = $_M['config']['met_google_appid'];
        $this->client_secret = $_M['config']['met_google_appsecret'];
        $this->redirect_uri = $_M['url']['web_site'] . "member/login.php?a=doother_login&type={$this->type}&lang={$_M['lang']}";
    }

    /**
     * @return string
     */
    public function get_login_url()
    {
        $redirect_uris = urlencode($this->redirect_uri);

        $scope = urlencode('https://www.googleapis.com/auth/userinfo.profile');
        $url = "https://accounts.google.com/o/oauth2/auth?response_type=code&access_type=offline&client_id={$this->client_id}&redirect_uri={$redirect_uris}&state&scope={$scope}&approval_prompt=auto";
        return $url;
    }

    public function get_access_token_by_curl($code = '')
    {
        $postData = array(
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code',
        );

        //通过code获取access_token
        $res = $this->getToken($postData);
        if (empty($res['access_token'])) {
            return false;
        }

        $info = $this->getUserInfo($res['access_token']);
        $info['access_token'] = $res['access_token'];
        $info['expires_in'] = $res['expires_in'];
        return $info;
    }

    public function get_info_by_curl($openid = '')
    {
        global $_M;
        $other_user = $this->get_other_user($openid);
        $access_token = $other_user['access_token'];
        $info = self::getUserInfo($access_token);

        if ($info['openid']) {
            return false;
        } else {
            return $info;
        }
    }


    /**
     * 抓取TOKEN
     * @param $postData
     * @param string $purl
     * @return bool
     */
    protected function getToken($postData = '', $purl = 'https://accounts.google.com/o/oauth2/token')
    {
        $fields = (is_array($postData)) ? http_build_query($postData) : $postData;
        $curlHeaders = [
            'content-type: application/x-www-form-urlencoded;CHARSET=utf-8',
            'Content-Length: ' . strlen($fields),
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $purl);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response && $responseCode == 200) {
            $res = json_decode($response, true);
            return $res;
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @return array
     */
    protected function getUserInfo($access_token = '')
    {
        $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . $access_token;
        $userInfo = json_decode(file_get_contents($url), true);
        if ($userInfo) {
            $userInfo['openid'] = $userInfo['id'];         //ID
            return $userInfo;
        }
        return false;
    }

    public function error_curl($data = array())
    {
        if ($data['errcode']) {
            $this->errorno = $data['errmsg'] ? $data['errmsg'] : $data['errcode'];
            return true;
        } else {
            return false;
        }
    }
}


# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>