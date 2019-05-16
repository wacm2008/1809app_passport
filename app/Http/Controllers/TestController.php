<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\UsersModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class TestController extends Controller
{
    //注册
    public function reg()
    {
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        $arr=json_decode($data,true);
        if(empty($arr['name'])||empty($arr['pwd'])){
            $response=[
                'errorno'=>50000,
                'msg'=>'名字或密码不能为空'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $e=DB::table('p_user')->where(['name'=>$arr['name']])->first();
        if($e){
            $response=[
                'errorno'=>50002,
                'msg'=>'名字存在'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $pwd=password_hash($arr['pwd'],PASSWORD_DEFAULT);
        $str=[
            'name'=>$arr['name'],
            'pwd'=>$pwd
        ];
        $add=DB::table('p_user')->insert($str);
        if($add){
            $response=[
                'errorno'=>0,
                'msg'=>'注册成功'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else{
            $response=[
                'errorno'=>50001,
                'msg'=>'注册失败'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }
    //登录
    public function log()
    {
        $data=file_get_contents('php://input');
        $arr=json_decode($data,true);
        $info=DB::table('p_user')->where(['name'=>$arr['name']])->first();
        if($info){
            if(password_verify($arr['pwd'],$info->pwd)){
                $token=substr(sha1($info->uid.time().str::random(10)),5,15);
                $key='token'.'-'.$info->uid;
                Redis::set($key,$token);
                Redis::expire($key,604800);
                $response=[
                    'errorno'=>0,
                    'msg'=>'登录成功',
                    'token'=>Redis::get($key),
                    'uid'=>$info->uid
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }else{
                $response=[
                    'errorno'=>50003,
                    'msg'=>'密码不正确',
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
        }else{
            $response=[
                'errorno'=>50004,
                'msg'=>'用户名或密码不正确'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }
}
