<?php

    class Common_Request{
        /**
         * 获取值
         * @param        $key
         * @param null   $default
         * @param string $type
         * @return null|string
         */
        static public function request($key, $default=null,$type=null){
            switch ($type) {
                case 'get':
                    $result = isset($_GET[$key])?trim($_GET[$key]):null;
                    break;
                case 'post':
                    $result = isset($_POST[$key])?trim($_POST[$key]):null;
                    break;
                default:
                    $result = isset($_REQUEST[$key])?trim($_REQUEST[$key]):null;
                    break;
            }
            if ($result==null && $default!=null){
                $result= $default;
            }
            return $result;

        }

        static public function postRequest($key, $default=null){
            return  self::request($key,$default,'post');
        }

        static public function getRequest($key, $default=null){
            return self::request($key,$default,'get');
        }

        static public function response($errno, $errmsg, $data=[]){
            $rep = [
                'errno'=>$errno,
                'errmsg'=>$errmsg,
            ];
            if ($data){
                $rep['data']=$data;
            }
            echo json_encode($rep,JSON_UNESCAPED_UNICODE);

        }
    }

