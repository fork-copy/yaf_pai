<?php
    
    class BaseController extends Yaf_Controller_Abstract {
        public function send($errno = 0, $errmsg = '', $data=[]){
            Common_Request::response($errno, $errmsg,$data);
            die();

        }

        protected function _isSubmit(){
            $submit = Common_Request::getRequest('submit',0);

            if ($submit != '1') {
                $this->send(-1001, '请通过渠道提交');
            }
        }

        //是否登陆
        public function isLogin(){

            //检查是否登陆
            session_start();

            if (!isset($_SESSION['user_token_time']) || !isset($_SESSION['user_token']) || !isset
                ($_SESSION['user_id']) || md5('salt'.$_SESSION['user_token_time'].$_SESSION['user_id'])
                !=$_SESSION['user_token']){
                $this->send(-4002,'请登陆后操作');
            }

        }
    }
