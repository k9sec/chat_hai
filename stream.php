<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/event-stream");
header("X-Accel-Buffering: no");
set_time_limit(0);
session_start();
$postData = $_SESSION['data'];
$responsedata = "";
$ch = curl_init();

$line = 0;

session_write_close();
$headers = [
    'Accept: application/json',
    'Content-Type: application/json',
];

setcookie("errcode", ""); //EventSource无法获取错误信息，通过cookie传递
setcookie("errmsg", "");

$callback = function ($ch, $data) {
    global $responsedata;
    print_r($data);

    $complete = json_decode($data);
    if (isset($complete->error)) {
        $responsedata = $data;
    } else {
        $responsedata .= $data;
        flush();
    }
    return strlen($data);
};


curl_setopt_array($ch, array(
    CURLOPT_URL => 'http://43.139.95.232:8000/stream',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_WRITEFUNCTION => $callback,
    CURLOPT_POSTFIELDS => json_encode([
        "query" => $postData['messages']['msg'],
        "history" => [
            [
                "现在，你是一位算命大师师，善于进行六爻、梅花心易占卜。你会对我输入的内容进行六爻、梅花心易占卜，占卜内容会以我的占卜内容是开头，对于占卜的结果你会使用白话输出给用户。请生成内容替换以下内容中所有的XXX，并严格按照以下形式输出最终结果：  好的，您的占卜问题是 XXX，让我为您进行占卜，我的占卜的结果是： ",
                "好的，让我为您输出"
            ]
        ],
        "max_length" => 1,
        "top_p" => 0.7,
        "temperature" => 0.95
    ]),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
));

curl_exec($ch);
curl_close($ch);
