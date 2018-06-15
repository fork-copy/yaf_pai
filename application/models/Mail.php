<?php
    require_once __DIR__.'/../../vendor/autoload.php';

    class MailModel extends BaseModel{

        public function send($uid=0, $title='', $contents=''){

            $query = $this->_db->prepare('select `email` from `user` where id = ?');
            $query->execute([$uid]);
            $user =$query->fetchAll();
            if (!$user || count($user)!=1){
                $this->errno=-3003;
                $this->errmsg=json_encode($query->errorInfo());
                return false;
            }
            $userMail = $user[0]['email'];
            if (!filter_var($userMail,FILTER_VALIDATE_EMAIL)){
                $this->errno=-3004;
                $this->errmsg=json_encode($query->errorInfo());
                return false;
            }
            try{
                $mail = new \Nette\Mail\Message();
                $mail->setFrom('"这是邮箱的标题" <15726817105@163.com>')
                    ->addTo($userMail)
                    ->setSubject($title)
                    ->setBody
                ($contents);
                $mailer = new \Nette\Mail\SmtpMailer([
                    'host'     => 'smtp.163.com',
                    'username' => '157***@163.com',
                    'password' => 'a13757***',
                    'secure'   => 'ssl',
                ]);
                $mailer->send($mail);
            } catch (Exception $e){
                $this->errno=$e->getCode();
                $this->errmsg=$e->getMessage();
                return false;
            }
        }
    }