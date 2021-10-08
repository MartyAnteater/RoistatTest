<?php

$logPath = $argv[1];

$log = file($logPath);
$logString = implode('', $log);

$parserCounter = array();
$parserCounter['traffic'] = 0;
$parserCounter['statusCodes'] = array();

foreach ($log as $key => $value) {
	$urlStartPosition = strpos($value, ' /') + 1;
	$urlStartSubstring = substr($value, $urlStartPosition);

	$urlPlusProtocolEndPosition = strpos($urlStartSubstring, '" ');
	$urlPlusProtocolSubstring = substr($urlStartSubstring, 0, $urlPlusProtocolEndPosition);

	$urlEndPosition = strpos($urlPlusProtocolSubstring, ' ');
	$url = substr($urlPlusProtocolSubstring, 0, $urlEndPosition);

	$parserCounter['urls'][$url] = TRUE;
	
	$codeAndTrafficStartPosition = strpos($value, '" ') + 2;
	$codeAndTrafficSubstring = substr($value, $codeAndTrafficStartPosition);

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
}

preg_match_all('/ Googlebot/', $log_string, $googleCrawler);
preg_match_all('/ Baiduspider/', $log_string, $baiduCrawler);
preg_match_all('/ Bingbot/', $log_string, $bingCrawler);
preg_match_all('/ YandexBot/', $log_string, $yandexCrawler);


$log_data = array();
$log_data['views'] = count($log);
$log_data['urls'] = count($parserCounter['urls']);
$log_data['traffic'] = $parserCounter['traffic'];
$log_data['statusCodes'] = $parserCounter['statusCodes'];
$log_data['crawlers'] = array('Google' => count($googleCrawler[0]),
                              'Bing' => count($bingCrawler[0]),
                              'Baidu' => count($baiduCrawler[0]),
							  'Yandex' => count($yandexCrawler[0]));

echo json_encode($log_data);
