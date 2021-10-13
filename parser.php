<?php

$logPath = $argv[1];

$parserCounter = array();
$parserCounter['traffic'] = 0;
$parserCounter['statusCodes'] = array();
$parserCounter['views'] = 0;
$parserCounter['crawlers'] = array('Google' => 0,
								   'Bing' => 0,
								   'Baidu' => 0,
								   'Yandex' => 0);

$logStream = fopen($logPath, 'r');

while(!feof($logStream)) {
	$logLine = trim(fgets($logStream));

	if($logLine !== ''){
		$urlStartPosition = strpos($logLine, ' /') + 1;
		$urlStartSubstring = substr($logLine, $urlStartPosition);

		$urlPlusProtocolEndPosition = strpos($urlStartSubstring, '" ');
		$urlPlusProtocolSubstring = substr($urlStartSubstring, 0, $urlPlusProtocolEndPosition);

		$urlEndPosition = strpos($urlPlusProtocolSubstring, ' ');
		$url = substr($urlPlusProtocolSubstring, 0, $urlEndPosition);

		$parserCounter['urls'][$url] = TRUE;
		
		$codeAndTrafficStartPosition = strpos($logLine, '" ') + 2;
		$codeAndTrafficSubstring = substr($logLine, $codeAndTrafficStartPosition);

		$codeAndTrafficEndPosition = strpos($codeAndTrafficSubstring, ' "');
		$codeAndTrafficSubstring = substr($codeAndTrafficSubstring, 0, $codeAndTrafficEndPosition);
		$codeAndTraffic = split(' ', $codeAndTrafficSubstring);
		$code = $codeAndTraffic[0];
		$traffic = $codeAndTraffic[1];

		if(!array_key_exists($code, $parserCounter['statusCodes'])){
			$parserCounter['statusCodes'][$code] = 0;
		}
		$parserCounter['statusCodes'][$code] = $parserCounter['statusCodes'][$code] + 1;
		$parserCounter['traffic'] = $parserCounter['traffic'] + $traffic;

		$googleCrawler = strpos($logLine, ' Googlebot');
		$baiduCrawler = strpos($logLine, ' Baiduspider');
		$bingCrawler = strpos($logLine, ' Bingbot');
		$yandexCrawler = strpos($logLine, ' YandexBot');

		if($googleCrawler >= 1){
			$parserCounter['crawlers']['Google'] = $parserCounter['crawlers']['Google'] + 1;
		}
		else if($baiduCrawler >= 1){
			$parserCounter['crawlers']['Bing'] = $parserCounter['crawlers']['Bing'] + 1;
		}
		else if($bingCrawler >= 1){
			$parserCounter['crawlers']['Baidu'] = $parserCounter['crawlers']['Baidu'] + 1;
		}
		else if($yandexCrawler >= 1){
			$parserCounter['crawlers']['Yandex'] = $parserCounter['crawlers']['Yandex'] + 1;
		}

		$parserCounter['views'] = $parserCounter['views'] + 1;
	}
}

fclose($logStream);

$logData = array();
$logData['views'] = $parserCounter['views'];
$logData['urls'] = count($parserCounter['urls']);
$logData['traffic'] = $parserCounter['traffic'];
$logData['statusCodes'] = $parserCounter['statusCodes'];
$logData['crawlers'] = $parserCounter['crawlers'];

echo json_encode($logData);
