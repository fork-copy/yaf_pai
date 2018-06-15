<?php

    class IpModel extends BaseModel{
        public function get($ip){
            $rep = ThirdParty_Ip::find($ip);
            return $rep;
        }
    }