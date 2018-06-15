<?php
    class Err_Map{
        const  ERRMAP=[
            1002=>'用户名与密码必须传递',
            1003=>'用户查询失败',
            1004=>'密码错误',
            1006=>'用户名与密码必须传递',
            /*...*/
        ];

        public function getCode($code){
            if (isset(self::ERRMAP[$code])){
                return ['code'=>$code,'errmsg'=>self::ERRMAP[$code]];
            }
            //未定义
            return ['code'=>$code,'errmsg'=>"undefined code"];
        }



    }

