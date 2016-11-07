<?php
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$tokenly_app = get_app('tokenly');
$meta = new \App\Meta_Model;


$bit_history = new \Util\ParseCSV(file_get_contents('https://api.bitcoinaverage.com/history/USD/per_day_all_time_history.csv'));
$bit_history = $bit_history->data;

$ltbc_data = json_decode(file_get_contents('https://poloniex.com/public?command=returnChartData&currencyPair=BTC_LTBC&start=1405699200&end=9999999999&period=86400'), true);
if(!is_array($ltbc_data) OR count($ltbc_data) == 0){
	die("Error getting LTBC history \n");
}
$ltbc_history = array();
$prev_btc_price = false;
foreach($ltbc_data as $row){
	$item = array();
	$item['date'] = date('Y/m/d', $row['date']);
	$item['btc_rate'] = $row['weightedAverage'];
	$item['usd_rate'] = false;
	$item['btc_price'] = false;
	if($prev_btc_price){
		$item['btc_price'] = $prev_btc_price;
		$item['usd_rate'] = $prev_btc_price * $item['btc_rate'];
	}
	foreach($bit_history as $btc_row){
		$btc_date = date('Y/m/d', strtotime($btc_row['DateTime']));
		if($btc_date == $item['date']){
			$item['btc_price'] = $btc_row['Average'];
			$item['usd_rate'] = $btc_row['Average'] * $item['btc_rate'];
			$prev_btc_price = $btc_row['Average'];
			break;
		}
	}
	$ltbc_history[] = $item;
}

$ltbc_history = array_reverse($ltbc_history);

$limit = 60; //60 days
$num = 0;
foreach($ltbc_history as $k => $row){
	$num++;
	if($num > $limit){
		unset($ltbc_history[$k]);
		continue;
	}
}

$bit_history = array_reverse($bit_history);
$num = 0;
foreach($bit_history as $k => $row){
	$num++;
	if($num > $limit){
		unset($bit_history[$k]);
		continue;
	}
}

$save_path = SITE_BASE.'/data/cache';
$save = file_put_contents($save_path.'/ltbc_price_history.json', json_encode($ltbc_history));
if(!$save){
	echo "Error saving LTBC price history \n";
}
else{
	echo "LTBC price history saved \n";
}
$save = file_put_contents($save_path.'/btc_price_history.json', json_encode($bit_history));
if(!$save){
	echo "Error saving BTC price history \n";
}
else{
	echo "BTC price history saved \n";
}
