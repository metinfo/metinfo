<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::mod_class('user/web/class/other');

class facebook extends other
{
    /**
     * Facebook
     * google constructor.
     */
    public function __construct()
    {
        global $_M;
        if (!$_M['config']['met_google_open']) {
            die("Google login cloased");
        }
        $this->table = $_M['table']['user_other'];
        $this->type = 'facebook';
        $this->client_id = $_M['config']['met_facebook_appid'];
        $this->client_secret = $_M['config']['met_facebook_appsecret'];
        $this->redirect_uri = $_M['url']['web_site'] . "member/login.php?a=doother_login&type={$this->type}&lang={$_M['lang']}";
    }

    /**
     * @return string
     */
    public function get_login_url()
    {
        $redirect_uris = urlencode($this->redirect_uri);
        $state = $this->get_state();
        $url = "https://www.facebook.com/v10.0/dialog/oauth?client_id={$this->client_id}&redirect_uri={$redirect_uris}&state={$state}&response_type=code";
        return $url;
    }

    public function get_access_token_by_curl($code = '')
    {
        $code = $_GET['code'];
        $res = $this->getToken($code);
        if (empty($res['access_token'])) {
            return false;
        }

        //通过code获取access_token
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
     * @param $postData
     * @param string $purl
     * @return bool
     */
    protected function getToken($code = array())
    {
        $data = array();
        $data['client_id'] = $this->client_id;
        $data['client_secret'] = $this->client_secret;
        $data['redirect_uri'] = $this->redirect_uri;
        $data['code'] = $code;

        $para = http_build_query($data);
        $api = "https://graph.facebook.com/v10.0/oauth/access_token";
        $url = $api . "?" . $para;

        $response = file_get_contents($url);
        $res = json_decode($response, true);
        return $res;
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @return array
     */
    protected function getUserInfo($user_token = '')
    {
        $access_token = "{$this->client_id}|{$this->client_secret}";
        $url = "https://graph.facebook.com/debug_token?access_token={$access_token}&input_token={$user_token}";
        $response = file_get_contents($url);
        $info = json_decode($response, true);
        if ($info['data'] && $info['data']['is_valid'] == 1) {
            $userInfo = array();
            $userInfo['openid'] = $info['data']['user_id'];  //ID
            return $userInfo;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function deletionUser()
    {
        global $_M;
        $signed_request = $_POST['signed_request'];
        $user = self::parse_signed_request($signed_request);
        if (!$user) {
            return false;
        }
        $redata = array();
        //$redata['url'] = $_M['url']['web_site'] . "/member/other/?type=facebook&action=del&other_id={$user['user_id']}";
        $redata['url'] = $_M['url']['web_site'];
        $redata['confirmation_code'] = $user['user_id'];

        return $redata;
    }

    /**
     * @param $signed_request
     * @return mixed|null
     */
    protected function parse_signed_request($signed_request = '')
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        $secret = $this->client_secret; // Use your app secret here

        // decode the data
        $sig = self::base64_url_decode($encoded_sig);
        $data = json_decode(self::base64_url_decode($payload), true);

        // confirm the signature
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    protected function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
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