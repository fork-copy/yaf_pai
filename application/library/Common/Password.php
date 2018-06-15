<?php
    class Common_Password{
        const salt = "I-love-iMooc";

        static public function pwdEncode($pwd){
            return md5(self::salt.$pwd);

        }
    }

