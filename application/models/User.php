<?php
/**
 * @name SampleModel
 * @desc sample数据获取类, 可以访问数据库，文件，其它系统等
 * @author 
 */
class UserModel extends BaseModel{


    //注册
    public function register($uname, $pwd){
        //判断是否有值
        $query = $this->_db->prepare("select count(*) as c from `user` WHERE `name`=?");
        $query->execute([$uname]);
        $count = $query->fetchAll();
        if ($count[0]['c'] != 0){
            $this->errno=-1005;
            $this->errmsg='用户名已存在';
            return false;
        }

        if (strlen($pwd)<8){
            $this->errno=-1005;
            $this->errmsg='密码不能短语8位';
            return false;
        }
        //加密
        $password  =Common_Password::pwdEncode($pwd);

        $query = $this->_db->prepare("insert  `user` (`id`,`name`,`pwd`,`reg_time`) values (null,?,?,?)");

        $ret = $query->execute([$uname,$password,date('Y-m-d H:i:s')]);
        if (!$ret){
            $this->errno=-1006;
            $this->errmsg=$query->errorInfo();
            return false;
        }

        return true;
    }

    //登陆
    public function login($name, $pwd){
        $query = $this->_db->prepare("SELECT `pwd`,`id` FROM `user` where `name` = ?");
        $query->execute([$name]);
        $ret = $query->fetchAll();
        if (!$ret || count($ret)!=1){
            $this->errno=-1003;
            $this->errmsg="用户查询失败";
            return  false;
        }

        $userInfo = $ret[0];
        //密码对比
        if (Common_Password::pwdEncode($pwd) != $userInfo['pwd']){
            $this->errno=-1004;
            $this->errmsg="密码错误";
            return  false;
        }
        //返回 id
        return intval($userInfo['id']);

    }
}
