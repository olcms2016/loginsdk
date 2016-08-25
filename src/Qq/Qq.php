<?php

namespace olcms\Qq;

/**
 * QQ登陆类
 */
class Qq
{
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
    
    private $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    /**
     * 登陆
     */
    public function login()
    {
        //生成oauth授权地址

        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION['state'] = $state;

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->config['app_id'],
            "redirect_uri" => $this->config['callback'],
            "state" => $state,
        );

        $url = self::GET_AUTH_CODE_URL.'?'.http_build_query($keysArr);

        header("Location:$url");
    }
    
    /**
     * 获取用户信息
     * @param type $param
     */
    public function getUserInfo($param)
    {
        if($param['state'] == $_SESSION['state']){
            //获取access_token
            $access_token = $this->getAccessToken($param);

            //获取open_id
            $open_id = $this->getOpenId($access_token['access_token']);

            //获取用户信息
            $keysArr = array(
                "access_token" => $access_token['access_token'],
                "oauth_consumer_key" => $this->config['app_id'],
                "openid" => $open_id,
            );

            $url = 'https://graph.qq.com/user/get_user_info'.'?'.http_build_query($keysArr);
            $request = \Requests::get($url);
            
            $data = [
                'access_token' => $access_token,
                'open_id' => $open_id,
                'user_info' => json_decode($request->body)
            ];
//            dump($data);
            return $data;
        } 
    }
    
    /**
     * 获取access_token
     * @param type $param
     * @return array
     */
    private function getAccessToken($param)
    {
        //-------构造请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->config['app_id'],
            "client_secret" => $this->config['app_key'],
            "redirect_uri" => $this->config['callback'],
            "code" => $param['code'],
        );

        $url = self::GET_ACCESS_TOKEN_URL.'?'.http_build_query($keysArr);
        $request = \Requests::get($url);

        $params = [];
        parse_str($request->body, $params);

        return $params;
    }
    
    /**
     * 获取open_id
     * @param type $access_token
     * @return type
     */
    private function getOpenId($access_token)
    {
        $url = self::GET_OPENID_URL.'?access_token='.$access_token; 
        $request = \Requests::get($url);

        $response = $request->body;
        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);

        return $user->openid;
    }
    
}
