<?php

    class BaseModel{
        public $errno=0;
        public $errmsg=0;
        protected $_db=null;

        public function __construct() {
            $this->_db = new PDO("mysql:host=10.0.0.111;dbname=imooc;",'root','root');
            $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
    }