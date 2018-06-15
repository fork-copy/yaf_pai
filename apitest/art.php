<?php
    require_once __DIR__ . '/../vendor/autoload.php';
    $curl = new \Curl\Curl();
    $host = "http://10.0.0.111:81/art/";

    $title = '测试文章 testId:' . rand(10000, 99999);
    $contents = str_repeat("测试内容" . rand(), 100);
    $author = "yi" . rand();
    $cate = 1;

    /**
     * 发布文章
     */
    $curl->post($host . "/add?submit=1", [
        'title'    => $title,
        'contents' => $contents,
        'author'   => $author,
        'cate'     => $cate,
    ]);
    if ($curl->error) {
        die('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
    } else {
        $rep = json_decode($curl->response, true);
        if ($rep['errno'] !== 0) {
            die('发布文章失败。错误信息:' . $rep['errmsg'] . "\n");
        }
        $artId = $rep['data']['lastId'];
        echo "发布文章成功，文章ID：{$artId}\n";
    }
    /**
     * 文章编辑
     */
    $curl->post($host . "/edit?submit=1&artId=" . $artId, [
        'title'    => $title . "Changed" . rand(100, 999),
        'contents' => $contents . rand(100, 999),
        'author'   => $author . rand(100, 999),
        'cate'     => $cate,
    ]);
    if ($curl->error) {
        die('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
    } else {
        $rep = json_decode($curl->response, true);
        if ($rep['errno'] !== 0) {
            die('修改文章失败，错误信息:' . $rep['errmsg'] . "\n");
        }
        echo "修改文章成功！\n";
    }
    /**
     * 修改文章状态
     */
    $curl->post($host . "/status?submit=1&artId=" . $artId . "&status=online", array());
    if ($curl->error) {
        die('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
    } else {
        $rep = json_decode($curl->response, true);
        if ($rep['errno'] !== 0) {
            die('修改文章状态失败，错误信息:' . $rep['errmsg'] . "\n");
        }
        echo '修改文章状态[online]成功！已修改为发布状态。' . "\n";
    }
    /**
     * 删除文章
     */
    $curl->post($host . "/del?submit=1&artId=" . $artId, array());
    if ($curl->error) {
        die('Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n");
    } else {
        $rep = json_decode($curl->response, true);
        if ($rep['errno'] !== 0) {
            die('删除文章失败。错误信息:' . $rep['errmsg'] . "\n");
        }
        echo '删除文章成功！删除文章ID:' . $artId . "\n";
    }
    echo "文章接口测试完毕。\n";



