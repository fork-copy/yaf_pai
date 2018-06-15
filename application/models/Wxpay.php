<?php

    $base_path = __DIR__.'/../library/ThirdParty/Wxpay/lib/';
    require_once $base_path.'WxPay.Api.php';
    require_once $base_path.'WxPay.Config.php';
    require_once $base_path.'WxPay.Data.php';
    require_once $base_path.'WxPay.Exception.php';
    require_once $base_path.'WxPay.Notify.php';


    class WxpayModel extends BaseModel{
        public function get($ip){
            $rep = ThirdParty_Ip::find($ip);
            return $rep;
        }

        public function createbill($itemId, $user_id){

            $query = $this->_db->prepare("SELECT * FROM `item` WHERE id=?");
            $query->execute([$itemId]);
            $data = $query->fetchAll(PDO::FETCH_ASSOC)[0];

            if (!$data){
                $this->errno=-3004;
                $this->errmsg="找不到商品";
                return false;
            }
            if (strtotime($data['etime'])<=time()){
                $this->errno=-3004;
                $this->errmsg='商品已过期';
                return false;
            }
            if (intval($data['stock'])<=0){
                $this->errno=-5004;
                $this->errmsg='库存不足,不能购买';
            }

            //生成 bill
            $query = $this->_db->prepare('insert into `bill`(`itemid`,`uid`,`price`,`status`) VALUES (?,?,?,"unqaid")');
            $res = $query->execute([$itemId,$user_id,intval($data['price'])]);
            if (!$res){
                $this->errno=-3004;
                $this->errmsg=json_encode($query->errorInfo(), JSON_UNESCAPED_UNICODE);
                return false;
            }

            $lastId  =$this->_db->lastInsertId();
            //库存减一
            $query = $this->_db->prepare("update `item` set `stock`=`stock`-1  WHERE id=?");
            $res  =$query->execute([$itemId]);
            if (!$res){
                $this->errno = -4002;
                $this->errmsg=json_encode($query->errorInfo(), JSON_UNESCAPED_UNICODE);
                return false;
            }

            return $lastId;
        }

        public function qrcode($billid){
            $query = $this->_db->prepare('SELECT * FROM `bill` WHERE id=?');
            $query->execute([$billid]);
            $bill =$query->fetchAll()[0];
            if (!$bill){
                $this->errno=-3004;
                $this->errmsg=json_encode($query->errorInfo(), JSON_UNESCAPED_UNICODE);
                return false;
            }

            $query = $this->_db->prepare('SELECT * FROM `item` WHERE id=?');
            $query->execute([$bill['itemid']]);
            $item =$query->fetchAll()[0];
            if (!$item){
                $this->errno=-3007;
                $this->errmsg=json_encode($query->errorInfo(), JSON_UNESCAPED_UNICODE);
                return false;
            }

            $input = new WxPayUnifiedOrder();
            $input->SetBody($item['name']);
            $input->SetAttach($billid);
            $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
            $input->SetTotal_fee($bill['price']);
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 86400*3)); //$url 的过期时间
            $input->SetGoods_tag("test");
            $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php"); //设置回调地址
            $input->SetTrade_type("NATIVE");
            $input->SetProduct_id($billid);

            $notify = new Wxpay_NativePay();
            $result = $notify->GetPayUrl($input);
            $url = $result["code_url"];
            return $url;


        }

        public function callback(){
            $xmlData = file_get_contents("php://input");
            if(substr_count($xmlData, "<result_code><![CDATA[SUCCESS]></result_code>") != 1 || substr_count($xmlData,
                    "<return_code><![CDATA[SUCCESS]></return_code>") != 1) {
                $this->errno=-5002;
                $this->errmsg="回调错误";
                return false;
            }
            $preg = "/<attach>(.*)\[(\d+)\]</attach>/i";
            preg_match($preg,$xmlData,$match);
            if ( isset($match[2]) && is_numeric($match[2]) ){
                $billid= intval($match[2]);
            }
            preg_match("/<transaction_id>(.*)\[(\d+)\]</transaction_id>/i", $xmlData,$match);
            if (isset($match[2]) && is_numeric($match[2])){
                $transactionId = intval($match[2]);
            }

            if ( ! isset($billid) || ! isset($transactionId)) {
                $this->errno = -4003;
                $this->errmsg = "回调参数获取失败";
                return false;
            }

            $query = $this->_db->prepare('update `bill` set `transaction`=?,`ptime`=?,`status`="paid" WHERE id=?');
            $res = $query->execute([$transactionId, date('Y-m-d H:i:s'), $billid]);
            if ( ! $res) {
                $this->errno = -4003;
                $this->errmsg = "支付状态更新失败";
                return false;
            }

            return true;
        }
    }