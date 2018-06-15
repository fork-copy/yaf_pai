<?php
    
    class UserController extends BaseController {


        /**
         * 登陆
         *  post :
         * @param string uname 姓名
         * @param string pwd  密码
         *
         */
        public function loginAction(){
            $this->_isSubmit();
            //获取参数
            $uname = Common_Request::postRequest('uname');
            $pwd = Common_Request::postRequest('pwd',false);
            if (! $uname || !$pwd){
                $this->send(-1002,'用户名与密码必须传递');
            }
            $model = new UserModel();

            $uid = $model->login(trim($uname),trim($pwd));
            if ( ! $uid) {
                $this->send($model->errno,$model->errmsg);
            }
            session_start();

            $_SESSION['user_token'] = md5('salt' . $_SERVER['REQUEST_TIME'] . $uid);
            $_SESSION['user_token_time'] = $_SERVER['REQUEST_TIME']; //设置有效期
            $_SESSION['user_id'] = $uid;
            $this->send(0,'',['name'=>$uname]);
        }

        /**
         * 注册
         * post :
         *  uname 姓名
         *  pwd   密码
         */
        public function registerAction(){
            $uname = Common_Request::postRequest('uname',false);
            $pwd = Common_Request::postRequest('pwd',false);
            if (!$uname || !$pwd){
               $this->send(-1002,'用户与密码必须传递');
            }
            
            $model = new UserModel();
            if ( ! $model->register(trim($uname), trim($pwd))) {
                $this->send($model->errno, $model->errmsg);
            }
            $this->send(0, '', ['name' => $uname]);

        }

    }
