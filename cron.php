#!/usr/bin/env php
<?php
require_once "init.php";

//get webpage

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.rb.ua/daily");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = "Pragma: no-cache";
$headers[] = "Accept-Encoding: gzip, deflate, br";
$headers[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4";
$headers[] = "Upgrade-Insecure-Requests: 1";
//$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36";
$headers[] = "User-Agent: @rbua_dailybot (https://telegram.me/rbua_dailybot)";
$headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
$headers[] = "Referer: https://www.rb.ua/";
$headers[] = "Connection: keep-alive";
$headers[] = "Cache-Control: no-cache";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$html=curl_exec($ch);

//rip using simplexml
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->strictErrorChecking = false;
$doc->loadHTML($html);
libxml_use_internal_errors(false);
$se = simplexml_import_dom($doc);
$elements = $se->xpath('//div[@class="daily_buy"]/parent::div');
$url = 'https:'.$elements[0]->a['href'];
$img = 'https://rb.ua/'.$elements[0]->a->img['src'];
$price = intval($elements[0]->div[1]->b);
$oldPrice = intval($elements[0]->div[2]->s);

// check cache
$cachedId = file_exists(CACHEFILE)? chop(file(CACHEFILE)[0]) : '';
$cacheId = md5($url);
if($cachedId == $cacheId) {
    die("No need to notify: $cachedId = $cacheId");
    //no need to notify
}

$bot = new \TelegramBot\Api\Client(TOKEN);
$message = "New sale: $oldPrice --> $price\nURL: $url";
foreach($subscribers->get() as $subscriber) {
    $bot->sendPhoto($subscriber, $img, $message);
}

file_put_contents(CACHEFILE, md5($url)."\n".$message);