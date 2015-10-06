<?php
ini_set('display_errors', 0);
$noForceSSL = true;
$_SERVER['HTTP_HOST'] = '';
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$model = new \Core\Model;
$btc = new \API\Bitcoin(BTC_CONNECT);
$xcp = new \API\Bitcoin(XCP_CONNECT);

$btc->walletpassphrase(XCP_WALLET, 300);

$pubkey = null;
if(isset($argv[5])){
	$pubkey = $argv[5];
}

$sendXCP = $xcp->create_send(array('source' => $argv[1],
									'destination' => $argv[2],
									'asset' => $argv[3],
									'quantity' => intval($argv[4])*SATOSHI_MOD,
									'encoding' => 'multisig',
									'allow_unconfirmed_inputs' => true,
									'pubkey' => $pubkey));

$sign = $btc->signrawtransaction($sendXCP);
$send = $btc->sendrawtransaction($sign['hex']);
echo $send."\n";
