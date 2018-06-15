<?php
    
    class PushController extends BaseController {
       
        public function singleAction(){
            $this->_isSubmit();

            $cid = Common_Request::postRequest('cid',false);
            $msg = Common_Request::postRequest('msg',false);
            if ( ! $cid || !$msg ) {
                $this->send(-3002,'请输入推送用户的设备 id 与要推送的内容');
            }

            $model = new PushModel();
            if (!$model->single($cid,$msg)) {
                $this->send($model->errno,$model->errmsg);
            }
            $this->send();
        }

        public function toolAction(){

        }
    }
