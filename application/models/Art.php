<?php

    class ArtModel extends BaseModel{

        public function add($title, $contents, $author, $cate, $artId=0){

            $isEdit = false;
            if ($artId !=0 && is_numeric($artId)){

                /*edit*/
                $query = $this->_db->prepare("select count(*) from `art` where `id`=?");
                $query->execute([$artId]);
                $ret = $query->fetchAll();
                if (!$ret || count($ret)!=1){
                    $this->errno=-2004;
                    $this->errmsg="找不到你要编辑的文章";
                    return false;
                }

                $isEdit = true;
            }else{
                /*add*/

                $query = $this->_db->prepare('SELECT count(*) as c FROM `cate` WHERE `id` =?');
                $query->execute([$cate]);
                $ret = $query->fetchAll();
                if (!$ret || $ret[0]['c']==0){
                    $this->errno=-2005;
                    $this->errmsg="找不到对应的 Id 分类信息, cate id:".$cate.' 请先创建分类';
                    return false;
                }
            }

            $data=[
                $title,
                $contents,
                $author,
                intval($cate),
                date('Y-m-d H:i:s'),
            ];
            if (!$isEdit){
                $query = $this->_db->prepare('insert into `art` (`title`,`contents`,`author`,`cate`,`ctime`) VALUES  (?,?,?,?,?)');
            }else{

                $query = $this->_db->prepare('update `art` set `title`=?,`contents`=?,`author`=?,`cate`=?,`mtime`=? WHERE  `id`=?');
                $data[]=$artId;
            }
            $ret = $query->execute($data);
            if (!$ret){
                $this->errno = -2006;
                $this->errmsg='操作文章表失败, ERRORINFO'.end($query->errorInfo());
                return false;
            }
            //返回文章 id
            if (!$isEdit){
                return intval($this->_db->lastInsertId());
            }else{
                return intval($artId);
            }



        }

        public function del($artid){
            //判断文章是否存在
            $query = $this->_db->prepare("select `id` from `art` where `id`=?");
            $query->execute([$artid]);
            $ret = $query->fetchAll();
            if (!$ret || count($ret)!=1){
                $this->errno=-2004;
                $this->errmsg="找不到你要删除的文章";
                return false;
            }

            $query =$this->_db->prepare("delete from `art` where `id`=?");
            $ret = $query->execute([$artid]);
            if (!$ret){
                $this->errno=-2007;
                $this->errmsg='删除失败'.end($query->errorInfo());
                return false;
            }
            return true;


        }

        public function status($artId, $status){
            $query = $this->_db->prepare('update `art` set `status`=? where `id`=?');
            $res = $query->execute([$status,$artId]);
            if (!$res){
                $this->errno=-2008;
                $this->errmsg="更新文章状态失败, ErrINfo:".end($query->errorInfo());
                return false;
            }
            return true;
        }

        public function get($artId){
            $query = $this->_db->prepare('select `title`,`contents`,`author`,`cate`,`ctime`,`mtime`,`status` from `art` where `id` = ?');
            $status = $query->execute([intval($artId)]);
            $res = $query->fetchAll();
            if (!$status || empty($res)){
                $this->errno=-2009;
                $this->errmsg=-2010;
                return false;
            }
            $artInfo = $res[0];
            $query = $this->_db->prepare('SELECT `name` FROM `cate` WHERE `id` = ?');
            $query->execute([$artInfo['cate']]);
            $cateData = $query->fetchAll();
            if (!$cateData){
                $this->errno = -2010;
                $this->errmsg ="无法获取分类信息";
                return false;
            }
            $artInfo['cateName'] = $cateData[0]['name'];

            $data=[
                'id'=>intval($artId),
                'title'=>$artInfo['title'],
                'contents'=>$artInfo['contents'],
                'author'=>$artInfo['author'],
                'cateName'=>$artInfo['cateName'],
                'cateId'=>intval($artInfo['cate']),
                'ctime'=>$artInfo['ctime'],
                'mtime'=>$artInfo['mtime'],
                'status'=>$artInfo['status'],
            ];
            return $data;
        }

        public function list($pageNo=0, $pageSize=10, $cate=0, $status='online'){
            $pageNo = max(1,$pageNo)-1;
            $first = $pageNo*$pageSize;
            $queryData=[
                intval($cate),
                $status,
                $first,
                $pageSize,

            ];
            $query = $this->_db->prepare('select art.id,art.title,art.cate,art.author,art.ctime,art.contents,cate.name from `art`  JOIN `cate`  on art.cate=cate.id where art.cate =? and art.status=? LIMIT ?,? ');
            $res = $query->execute($queryData);
            if (!$res){
                $this->errno = -2010;
                $this->errmsg = json_encode($query->errorInfo());
                return false;
            }

            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            if (!$data){
                $this->errno = -2010;
                $this->errmsg = json_encode($query->errorInfo());
                return false;
            }
            array_walk($data, [$this,'sub_contents']);

            return $data;
        }

        //文章内容大于30的截取
        public function sub_contents(&$value){
            $contents = $value['contents'];
            if (mb_strlen($contents)>30){
                $value['contents'] = mb_substr($contents, 0, 30).'...';
            }
        }
    }