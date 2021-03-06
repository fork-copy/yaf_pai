<?php
    
    class ArtController extends BaseController {
        public function indexAction(){
            return $this->listAction();
        }

        public function addAction($artId=0){
            $this->_isAdmin();

            $this->_isSubmit();

            //接受数据
            $title = Common_Request::postRequest('title',false);
            $contents = Common_Request::postRequest('contents',false);
            $author = Common_Request::postRequest('author',false);
            $cate = Common_Request::postRequest('cate',false);

            if (!$title || !$contents || !$author || !$cate ){
                $this->send(-2002,'标题,内容,作者,分类不能为空');
            }

            $model = new ArtModel();
            $lastId = $model->add(trim($title), trim($contents), trim($author), trim($cate),$artId);//有这个 artId 说明是修改
            if ( ! $lastId) {
                $this->send($model->errno, $model->errmsg, ['lastId' => $lastId]);
            }
            $this->send(0, '', ['lastId' => $lastId]);

        }


        public function editAction(){
            try{
                $this->_isAdmin();
                $artId = Common_Request::getRequest('artId', 0);
                if ( ! is_numeric($artId) || ! $artId) {
                    $this->send(-2003, '缺少必要的文章 ID 参数');
                }
            } catch (\Exception $e){
                $this->send($e->getCode(),$e->getMessage());
            }
            return $this->addAction($artId);
        }

        public function delAction(){
            $this->_isAdmin();
            $artId = Common_Request::getRequest('artId','0');
            if ( ! is_numeric($artId) || ! $artId) {
                $this->send(-2003, '缺少必要的参数');
            }

            $model = new ArtModel();
            if ( ! $model->del($artId)) {
                $this->send($model->errno, $model->errmsg);
            }
            $this->send(0, '');
        }

        public function statusAction(){
            $this->_isAdmin();
            $artId = Common_Request::getRequest('artId','0');
            $status = Common_Request::getRequest('status','offline');
            if ( ! is_numeric($artId) || ! $artId) {
                $this->send(-2003,'缺少必要的文章标题');
            }
            $model = new ArtModel();
            if (!$model->status($artId,$status)) {
                $this->send($model->errno,$model->errmsg);
            }
            $this->send(0);
            return true;
        }

        public function getAction(){
            $artId = Common_Request::getRequest('artId',0);
            if (!$artId || !is_numeric($artId)){
                $this->send(-2003,'缺少必要的文章标题参数');
            }
            $model = new ArtModel();
            $data = $model->get($artId);
            if (empty($data)){
                $this->send(-2009,'获取文章内容失败');
            }
            $this->send(0,'',$data);

        }

        public function listAction(){
            $pageNo = Common_Request::getRequest('pageNo','0');
            $pageSize = Common_Request::getRequest('pageSize','10');
            $cate = Common_Request::getRequest('cate','0');
            $status = Common_Request::getRequest('status','online');

            $model = new ArtModel();
            $data = $model->list($pageNo,$pageSize,$cate,$status);
            if (!$data){
                $this->send($model->errno,$model->errmsg);
            }
            $this->send(0,'',$data);
        }

        //判断是否是 管理员
        private function _isAdmin(){

            if (false){
                $this->send(-2000,'需要管理员权限才可以操作');
            }

        }

    }
