<?php
    require_once __DIR__.'/../vendor/autoload.php';

    $host = "http://10.0.0.111:81";
    $uname = 'apitest_uname'.rand();
    $pwd = 'apitest_pwd'.rand();
    $curl = new \Curl\Curl();

    echo "============登陆注册测试开始=========".PHP_EOL;
    /**
     * 注册接口验证
     */

    $curl->post($host.'/user/register',[
        'uname'=>$uname,
        'pwd'=>$pwd,
    ]);
    if ($curl->error){
        die("Error".$curl->error_code.':'.$curl->error_message.PHP_EOL);
    }else{
        $rep = json_decode($curl->response,true);
        if ($rep['errno']!==0){
            die("Error:注册用户失败"  .$rep['errmsg'].PHP_EOL);
        }
        echo "注册用户接口测试成功:       ".$uname.PHP_EOL;
    }
    /**
     * 登陆接口验证
     */



    $curl->post($host.'/user/login?submit=1',[
        'uname'=>$uname,
        'pwd'=>$pwd,
    ]);

    if ($curl->error){
        die("Error".$curl->error_code.':'.$curl->error_message.PHP_EOL);
    }else{
        $rep = json_decode($curl->response,true);
        if ($rep['errno']!==0){
            die("Error:用户登陆失败  ".$rep['errmsg'].PHP_EOL);
        }
        echo "登陆接口测试成功      用户名:".$uname."  密码:".$pwd.PHP_EOL;
    }



    echo "============登陆注册测试结束=============".PHP_EOL;

