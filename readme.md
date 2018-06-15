## 请先在安装 yaf
```bash
> https://github.com/laruence/yaf`
> cd tools/cg
> ./yaf_cg
> cd output
> tree  # 查看目录结构
```
## 配置伪静态化
Apache的Rewrite
```
#.htaccess, 当然也可以写在httpd.conf
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule .* index.php
```
nginx 的 Rewrite
```
server {
  listen ****;
  server_name  domain.com;
  root   document_root;
  index  index.php index.html index.htm;

  if (!-e $request_filename) {
    rewrite ^/(.*)  /index.php/$1 last;
  }
}
```
## php 代码补全

克隆 https://github.com/xudianyang/yaf.auto.complete 到本地
在 phpstrom 中引入文件设置->语言&框架->php->include path



## controller 目录

art.php  文章的增删改,列表,发布等功能
user.php  用户的注册和登陆
Mail.php  使用`Nette\Mail\Message` 发送邮箱


## 短信服务 使用 云信
www.sms.cn

## app 推送服务使用 个推
www.getui.com

## 微信支付
1. `wxpay/createbill?itemid=1` 生成订单 `itemid` 为商品 id,返回订单 id
2.`wxpay/qrcode?billid=14` 创建二维码 `billid` 为订单 id
```php
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
$url = $result["code_url"]; //返回 url
return $url;
```

通过 $url 生成二维码

`QRcode::png($data);`

回调接口


## apitest 测试目录
`user.php`  测试注册, 登陆用户
其他测试文件需要自己修改
