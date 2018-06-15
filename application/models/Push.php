<?php

    $pushLibPath = dirname(__FILE__).'/../library/ThirdParty/Getui';

    require_once($pushLibPath . '/' . 'IGt.Push.php');
    require_once($pushLibPath . '/' . 'igetui/IGt.AppMessage.php');
    require_once($pushLibPath . '/' . 'igetui/IGt.APNPayload.php');
    require_once($pushLibPath . '/' . 'igetui/template/IGt.BaseTemplate.php');
    require_once($pushLibPath . '/' . 'IGt.Batch.php');
    require_once($pushLibPath . '/' . 'igetui/utils/AppConditions.php');

    //http的域名
    define('HOST','http://sdk.open.api.igexin.com/apiex.htm');
    define('APPKEY','6Xo12tmdVh8rObG5dI2ac8');
    define('APPID','gsCxLApCtDAZ4Yzad8Jz83');
    define('MASTERSECRET','ZJVD3iig7jAzIjHKOIZid2');


    class PushModel extends BaseModel{

        public function single($cid=0,$msg=''){
            $igt = new IGeTui(HOST, APPKEY, MASTERSECRET);
            $this->_IgetTransmissionTemplateDemo($msg);



        }

        private function _IgetTransmissionTemplateDemo($msg){
        }
    }