<?php

namespace app\index\controller;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

use think\Cache;
use think\Controller;
use think\facade\Session;
use think\Request;
use think\Validate;

use app\common\model\User as UserModel;


class Login extends Base
{

    public function register(Request $request)
    {
        $data = $request->post();

        $userModel = new UserModel();
        $rules = [
            'phone'     => 'require|max:11',
            'code'      => 'require|max:4',
            'password'  => 'require|max:30',
            're_password' => 'require|max:30',
        ];

        $messages = [
            'phone.require'     => '手机号必须填写',
            'phone.max'           => '手机号最大长度为 11',
            'code.require'      => '验证码必须填写',
            'code.max'      => '验证码最大长度为 4',
            'password.require'  => '密码必须填写',
            'password.max'  => '密码最大长度为 30',
            're_password.require'  => '密码必须填写',
            're_password.max'  => '密码最大长度为 30',
        ];

        $validate = new Validate($rules,$messages);
        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        if ($userModel->where(['phone'=>$data['phone']])->find()){
            return json(['code'=>0,'msg'=>'该手机已注册']);
        }

        $cache = new Cache([
            'type'      => 'file',
        ]);
        $real_code = $cache->get($data['phone']);
//        $real_code = 1111;
        if (!$real_code){
            return json(['code'=>0,'msg'=>'验证码不正确']);
        }
        if ($real_code['code'] != $data['code']){
            return json(['code'=>0,'msg'=>'验证码不正确']);
        }

        if ($data['password'] != $data['re_password']){
            return json(['code'=>0,'msg'=>'两次密码不一致']);
        }


        $userModel->insert([
            'phone'    => $data['phone'],
            'password' => md5($data['password']),
            'create_time'   => time(),
//            'last_login_time' => time(),
        ]);
        return json(['code'=>1,'msg'=>'success']);

    }

    public function login(Request $request)
    {
        $data = $request->post();

        $userModel = new UserModel();
        $rules = [
            'phone'     => 'require|max:11',
            'password'  => 'require|max:30',
        ];

        $messages = [
            'phone.require'     => '手机号必须填写',
            'phone.max'           => '手机号最大长度为 11',
            'password.require'  => '密码必须填写',
            'password.max'  => '密码最大长度为 30',
        ];

        $validate = new Validate($rules,$messages);
        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $userInfo = $userModel->where(['phone'=>$data['phone'],'password'=>md5($data['password'])])->find();
        if (!$userInfo){
            return json(['code'=>0,'msg'=>'账号或密码错误,请重新登陆']);
        }
        if ($userInfo['status'] == 1) return json(['code'=>0,'msg'=>'您的账号被冻结,具体请联系官方客服']);
//        $userInfo->where(['id'=>$userInfo['id']])->update([
//            'last_login_time' => time(),
//        ]);
        $info = $userInfo->where(['id'=>$userInfo['id']])->find();
        Session::set($this->userInfoSessionPath,$info);
        return json(['code'=>1,'msg'=>'success']);
    }

    public function rePwd(Request $request)
    {
        $rules = [
            'phone'     => 'require',
            'password'  => 'require',
            'code'      => 'require',
        ];

        $messages = [
            'phone.require'         => '用户名必须填写',
            'password.require'      => '密码必须填写',
            'code.require'          => '验证码必须填写',
        ];

        $data = $request->post();

        $validate = new Validate($rules,$messages);
        if (!$validate->check($data)){
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        //查询是否有此用户
        $userInfo = (new UserModel())->where(['phone'=>$data['phone']])->find();
        if (!$userInfo){
            return json(['code'=>0,'msg'=>'不存在这个用户']);
        }

        //判断验证码
        $real_code = (new Cache(['type'=>config('cache.type')]))->get($data['phone']);
        if (!$real_code || $real_code['code'] != $data['code']){
            return json(['code'=>0,'msg'=>'验证码不正确']);
        }
        //绑定用户
        $userInfo->save([
            'password'  => md5($data['password']),
        ]);
        (new Cache(['type'=>config('cache.type')]))->set($data['phone'],null);
        //返回
        return json(['code'=>1,'msg'=>'success']);
    }

    public function logout()
    {
        Session::set($this->userInfoSessionPath,null);
        return $this->redirect('/index');
    }

    //短信发送接口
    public function getCode(Request $request)
    {
        $cache = new Cache([
            // 驱动方式
            'type'   => 'File',
            // 缓存保存目录
            'path'   => '',
            // 缓存前缀
            'prefix' => '',
            // 缓存有效期 0表示永久缓存
            'expire' => 3600,
        ]);
        $phone = $request->post('phone');

//        $userInfo = (new UserModel())->where(['phone'=>$phone])->find();
//        if ($userInfo){
//            return json(['code'=>0,'msg'=>'手机号已被注册']);
//        }
        $ip = $this->getIP();
        if ($ip){
            $ipRequestNum = $cache->get($ip);
            if ($ipRequestNum){
                if ($ipRequestNum > 10){
                    return json(['code'=>0,'msg'=>'同一ip24小时内只能获取10次验证码']);
                }else{
                    $cache->inc($ip);
                }
            }else{
                $cache->set($ip,0,86400);
            }
        }

        if (!$phone) return json(['code'=>0,'msg'=>'error1']);
        if ($cache->has($phone)){
            $after = $cache->get($phone)['timestamp'];
            if (time() - $after <= 60){
                return json(['code'=>0,'msg'=>'短信已发信,请耐心等待']);
            }
        }
        $result = $this->sendPhoneMsg($phone);

        if ($result['status']){
            if ($ip){       //记录ip请求
                $cache->inc($ip);
            }
            //记录发送时间 记录手机号
            $cache->set($phone,['code'=>$result['code'],'timestamp'=>time()],3600);
            return json(['code'=>1,'msg'=>'success']);
        }else{
            return json(['code'=>0,'msg'=>'发送失败,请联系网站管理员']);
        }
    }

    protected function sendPhoneMsg($phone)
    {
        $code = mt_rand(1000,9999);
        $access_key_id = env('SMS.ACCESS_KEY_ID');
        $access_key_secret = env('SMS.ACCESS_KEY_SECRET');
        $sign_name = env('SMS.SIGN_NAME');
        $template_code = env('SMS.TEMPLATE_CODE');
        AlibabaCloud::accessKeyClient($access_key_id, $access_key_secret)->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->regionId('cn-beijing')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'PhoneNumbers'  => $phone,
                        'SignName'  => $sign_name,
                        'TemplateCode'  => $template_code,
                        'TemplateParam' => json_encode(['code'=>$code]),
                    ],
                ])
                ->request();
            return ['status'=>1,'data'=>$result->toArray(),'code'=>$code];
        } catch (ClientException $e) {
//            echo $e->getErrorMessage() . PHP_EOL;
            return ['status'=>0,'msg'=>$e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['status'=>0,'msg'=>$e->getErrorMessage()];
        }
    }

    //获取用户真实ip
    protected function getIP()
    {
        $ip = false;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        return $ip;
    }
//
//    //生成token
//    protected function makeToken($password)
//    {
//        $rand = mt_rand(1000000,9999999);
//        $salt = env('LOGIN.SALT');
//        return md5(md5($password) . $rand . $salt .  microtime() );
//    }
}
