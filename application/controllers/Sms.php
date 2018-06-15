<?php
    
    class SmsController extends BaseController {
        public function indexAction(){
            
        }

        public function sendAction(){
            $this->_isSubmit();

            $uid = Common_Request::postRequest('uid',false);
            if ( ! $uid  ) {
                $this->send(-3002,'用户 id, 邮件标题,邮件内容不能为空');
            }

            $model = new SmsModel();
            if (!$model->send(intval($uid))) {
                $this->send($model->errno,$model->errmsg);
            }
            $this->send();
        }
    }
