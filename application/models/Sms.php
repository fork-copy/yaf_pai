<?php
    require_once __DIR__.'/../../vendor/autoload.php';

    class SmsModel extends BaseModel{

        public function send($uid=0){

            //获取
            $query = $this->_db->prepare('select `mobile` from `user` where id = ?');
            $query->execute([$uid]);
            $user =$query->fetchAll();
            if (!$user || count($user)!=1){
                $this->errno=-3003;
                $this->errmsg=json_encode($query->errorInfo());
                return false;
            }
            //验证
            $userMobile = $user[0]['mobile'];
            if (!$userMobile || !is_numeric($userMobile) || strlen($userMobile)!=11){
                $this->errno=-4004;
                $this->errmsg="用户手机号信息不符合标准,手机号为:".($userMobile?:'空');
                return false;
            }

            //发送,使用云信
            $smsUid = "a260083304";
            $smsPwd = '13757547812-';
            $sms = new ThirdParty_Sms($smsUid,$smsPwd);

            $contents = ['code'=>rand(1000,9999)];
            $template = '100006';
            $result = $sms->send($userMobile, $contents,$template);
            if ($result['stat']!='100'){
                $this->errno=-4005;
                $this->errmsg='发送失败'.$result['stat'].':'.$result['message'];
                return false;
            }

            //发送成功,记录数据库
            $query = $this->_db->prepare('INSERT INTO `sms_record` (`uid`,`contents`,`template`,`ctime`) VALUES (?,?,?,?)');
            $res = $query->execute([
                $uid,json_encode($contents,JSON_UNESCAPED_UNICODE),$template,date('Y-m-d H:i:s')
            ]);
            if (!$res){
                $this->errno=-3004;
                $this->errmsg='消息发送陈宫,但是发送记录失败';
                return false;
            }

            return true;





        }
    }