<?php
error_reporting(E_ALL ^ E_WARNING);
header("Access-Control-Allow-Origin: *");
set_time_limit(0);
session_start();
$postData = $_SESSION['data'];

$responsedata = "";
$ch = curl_init();
$OPENAI_API_KEY = "";

print_r($postData);

session_write_close();
$headers  = [
    'Accept: application/json',
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
];

setcookie("errcode", ""); //EventSource无法获取错误信息，通过cookie传递
setcookie("errmsg", "");

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_URL, 'http://43.139.95.232:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // 设置连接超时时间为30秒
curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // 设置最大重定向次数为3次
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 允许自动重定向
curl_setopt($ch, CURLOPT_AUTOREFERER, true); // 自动设置Referer
//curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:1081");
$responsedata = curl_exec($ch);
echo $responsedata;
curl_close($ch);


session_start();
$questionarr = json_decode($postData, true);
$answer = json_decode($responsedata, true);
$goodanswer = '![IMG](' . $answer['data'][0]['url'] . ')';
$filecontent = $_SERVER["REMOTE_ADDR"] . " | " . date("Y-m-d H:i:s") . "\n";
$filecontent .= "Q:" . $questionarr['prompt'] .  "\nA:" . trim($goodanswer) . "\n----------------\n";
$myfile = fopen(__DIR__ . "/chat.txt", "a") or die("Writing file failed.");
fwrite($myfile, $filecontent);
fclose($myfile);
