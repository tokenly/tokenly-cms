<?php
define('CAPTCHA_PUB', ''); //recaptcha public key
define('CAPTCHA_PRIV', ''); //recaptcha private key

define('SOUNDCLOUD_ID', ''); //soundcloud client ID
define('SOUNDCLOUD_SECRET', ''); //soundcloud secret key

define('XCP_USER', ''); //counterparty RPC user
define('XCP_PASS', ''); //counterparty RPC password
define('XCP_IP', ''); //counterparty IP:port
define('XCP_CONNECT', 'http://'.XCP_USER.':'.XCP_PASS.'@'.XCP_IP.'/api/');
define('XCP_WALLET', ''); //counterparty/bitcoin wallet pass
define('XCP_PREFIX', 'XCP_'); //prefix for counterparty related accounts on bitcoind
define('SATOSHI_MOD', 100000000); //100 million satoshis = 1 BTC
define('XCP_BASE_FEE', 10000); //in satoshis.. default base fee to use on transactions
define('XCP_FEE_MOD', (10860 * 2)); //default dust input size x2 for counterparty transactions

define('XCP_FUEL_ACCOUNT', 'XCP_DUST_FUEL'); //account on your bitcoind wallet to hold random dust funds for various purposes
define('XCP_DEFAULT_FUEL', 0.0001); //default amount of "fuel" to use, for things such as the payment collector

define('BTC_USER', ''); //bitcoind RPC user
define('BTC_PASS', ''); //bitcoind RPC password
define('BTC_IP', ''); //bitcoind IP:port
define('BTC_CONNECT', 'http://'.BTC_USER.':'.BTC_PASS.'@'.BTC_IP.'/');

//disqus API credentials
define('DISQUS_PUBLIC', '');
define('DISQUS_SECRET', '');
define('DISQUS_DEFAULT_FORUM', '');
define('DISQUS_ADMIN', '');

//amazon AWS API credentials
define('AWS_ACCESS_KEY', '');
define('AWS_SECRET_KEY', '');
define('AWS_CONFIG_PATH', SITE_PATH.'/resources/aws/Aws/Common/Resources/aws-config.php');
define('AWS_DEFAULT_BUCKET', '');

define('STOPFORUMSPAM_KEY', '');
define('STOPFORUMSPAM_API', 'http://api.stopforumspam.org/api');
