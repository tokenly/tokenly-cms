<?php
namespace API;
use \Blocktrail\SDK\BlocktrailSDK, BitWasp\BitcoinLib\BitcoinLib, BitWasp\BitcoinLib\RawTransaction;
/*
					COPYRIGHT

Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

This file is part of JSON-RPC PHP.

JSON-RPC PHP is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

JSON-RPC PHP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with JSON-RPC PHP; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * The object of this class are generic jsonRPC 1.0 clients
 * http://json-rpc.org/wiki/specification
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 */
class Bitcoin
{

	
	/**
	 * Debug state
	 *
	 * @var boolean
	 */
	private $debug;
	
	/**
	 * The server URL
	 *
	 * @var string
	 */
	private $url;
	/**
	 * The request id
	 *
	 * @var integer
	 */
	private $id = 0;
	/**
	 * If true, notifications are performed instead of requests
	 *
	 * @var boolean
	 */
	private $notification = false;
	
	public $modify_request = true;
	
	public $min_fee = 0.00002;
	public $dust_limit = 0.000055;
	public $wallet_timeout = 300;
	
	protected static $utxoset = false;
	
	/**
	 * Takes the connection parameters
	 *
	 * @param string $url
	 * @param boolean $debug
	 */
	public function __construct($url,$debug = false) {
		// server URL
		$this->url = $url;
		// proxy
		empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
		// debug state
		empty($debug) ? $this->debug = false : $this->debug = true;
		// message id
		$this->id = 1;
	}
	
	/**
	 * Sets the notification state of the object. In this state, notifications are performed, instead of requests.
	 *
	 * @param boolean $notification
	 */
	public function setRPCNotification($notification) {
		empty($notification) ?
							$this->notification = false
							:
							$this->notification = true;
	}
	
	/**
	 * Performs a jsonRCP request and gets the results as an array
	 *
	 * @param string $method
	 * @param array $params
	 * @return array
	 */
	public function __call($method,$params) {
		
		// check
		if (!is_scalar($method)) {
			throw new \Exception('Method name has no scalar value');
		}
		
		// check
		if (is_array($params)) {
			// no keys
			$params = array_values($params);
		} else {
			throw new \Exception('Params must be given as array');
		}
		
		// sets notification or request task
		if ($this->notification) {
			$currentId = NULL;
		} else {
			$currentId = $this->id;
		}

		// prepares the request
		$request = array(
						'method' => $method,
						'params' => $params,
						'jsonrpc' => '2.0',
						'id' => $currentId
						);
		$request = json_encode($request);
		if($this->modify_request){
			$request = str_replace(array('[{', '}]'), array('{', '}'), $request);
		}
	
		
		$this->debug && $this->debug.='***** Request *****'."\n".$request."\n".'***** End Of request *****'."\n\n";
		
		// performs the HTTP POST
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $this->url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array('Content-type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true
        ));
        
        $response = curl_exec($ch);
        if($response === false)
        {
			throw new \Exception('Unable to connect to '.$this->url);
		}
        
        $this->debug && $this->debug.='***** Server response *****'."\n".$response.'***** End of server response *****'."\n";
        $response = json_decode($response,true);
		
		// debug output
		if ($this->debug) {
			echo nl2br($debug);
		}
		
		// final checks and return
		if (!$this->notification) {
			// check

			if (@$response['id'] != $currentId) {
				if(isset($response['data'])){
					throw new \Exception($response['data']);
				}
				throw new \Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');
			}
			if (isset($response['error']) AND !is_null($response['error'])) {
				if(isset($response['data'])){
					throw new \Exception($response['data']);
				}				
				throw new \Exception('Request error: '.$response['error']['message']);
			}
			
			return $response['result'];
			
		} else {
			return true;
		}
	}
	

	public function sendfromaddress($address, $amount, $to, $fee = 0.00001)
	{
		$unspent = $this->listunspent();
		$outputsFound = array();
		$totalFound = 0;
		foreach($unspent as $utxo){
			if($utxo['address'] == $address){
				$outputsFound[] = $utxo;
				$totalFound += $utxo['amount'];
				if($totalFound >= $amount){
					break;
				}
			}
		}
		
		if(count($outputsFound) == 0){
			throw new \Exception('No valid unspent outputs found for this address');
		}

		if($totalFound < ($amount + $fee)){
			throw new \Exception('Insufficient funds at this address (need '.(($amount + $fee) - $totalFound).')');
		}
		
		$rawInputs = array();
		foreach($outputsFound as $utxo){
			$item = array('txid' => $utxo['txid'], 'vout' => $utxo['vout']);
			$rawInputs[] = $item;
		}
		

		$rawAddresses = array($to => $amount);
		$leftover = $totalFound - $amount;
		$change = $leftover - $fee;
		if($change > 0.000055){
			$rawAddresses[$address] = $change;
		}
		
		$this->modify_request = false;
		$createRaw = $this->createrawtransaction($rawInputs, $rawAddresses);
		
		$signData = array();
		foreach($outputsFound as $utxo){
			$item = array('txid' => $utxo['txid'], 'vout' => $utxo['vout'], 'scriptPubKey' => $utxo['scriptPubKey']);
			$signData[] = $item;
		}
		
		$signRaw = $this->signrawtransaction($createRaw, $signData);
		$this->modify_request = true;
		
		return $this->sendrawtransaction($signRaw['hex']);
		
	}
	
	public function getaddressbalance($address, $min_conf = 1)
	{
		$unspent = $this->getaddressunspent($address, $min_conf);
		return $unspent['total'];
	}	
	
	public function updateutxoset()
	{
		$get = $this->listunspent(0);
		aasort($get, 'confirmations');
		$get = array_reverse($get);
		self::$utxoset = $get;
		return self::$utxoset;
	}
	
	public function getaddresstxlist($address, $level = 0, $limit = false)
	{
		$output = array();
		try{
			$blocktrail = new BlocktrailSDK(BLOCKTRAIL_KEY, BLOCKTRAIL_SECRET, "BTC", BLOCKTRAIL_TESTNET);
			$tx_list = $blocktrail->addressTransactions($address, $level, $limit, 'desc');
		}
		catch(\Exception $e){
			return array();
		}

		if($tx_list AND isset($tx_list['data']) AND count($tx_list['data']) > 0){
			$tx_count = $tx_list['total'];
			if(!$limit){
				$pages = ceil($tx_count / $tx_list['per_page']);
			}
			foreach($tx_list['data'] as $tx){
				$item = array();
				$item['txId'] = $tx['hash'];
				$item['time'] = strtotime($tx['time']);
				$item['amount'] = 0;
				$item['inputs'] = $tx['inputs'];
				$item['outputs'] = $tx['outputs'];
				foreach($tx['inputs'] as $input){
					if($input['address'] == $address){
						$item['amount'] -= $input['value'];
					}
				}
				foreach($tx['outputs'] as $out){
					if(isset($out['address']) AND $out['address'] == $address){
						$item['amount'] += $out['value'];
					}
				}
				$output[] = $item;
			}
			
			if($level == 0 AND !$limit){
				$level_tx = array();
				for($i = 1; $i <= $pages; $i++){
					$level_tx = array_merge($level_tx, $this->getaddresstxlist($address, $i, $tx_list['per_page']));
				}
				$output = array_merge($output, $level_tx);
				aasort($output, 'time');
				$output = array_reverse(array_values($output));
			}
		}
		return $output;
	}
	
	public function getutxoset()
	{
		if(!self::$utxoset){
			$this->updateutxoset();
		}
		return self::$utxoset;
	}
	
	public function getaddressunspent($address, $min_conf = 1)
	{
		$unspent = $this->getutxoset();
		$output = array('total' => 0, 'min_conf_total' => 0, 'count' => 0, 'utxos' => array());
		foreach($unspent as $tx){
			if($tx['confirmations'] < $min_conf){
				continue;
			}
			if($tx['address'] == $address AND $tx['spendable'] == 1){
				$output['total'] += $tx['amount'];
				$output['count']++;
				$output['utxos'][] = $tx;
				if($tx['confirmations'] == $min_conf){
					$output['min_conf_total'] += $tx['amount'];
				}
			}
		}
		return $output;
	}
	
	public function getprimingfee($num, $per_input, $per_batch = 25)
	{
		$num_batches = ceil($num / $per_batch);
		$batch_fee = ($per_batch * $this->min_fee) + ($this->min_fee * 3);
		$total_batch_fee = $batch_fee * $num_batches;
		$total_required = ($num * $per_input) + $total_batch_fee;
		return $total_required;
	}
	
	public function primeaddressinputs($address, $num, $per_input, $per_batch = 25, $use_stage = 1)
	{
		$unspent = $this->getaddressunspent($address);
		$num_batches = ceil($num / $per_batch);
		$total_required = (float)bcmul((string)$this->getprimingfee($num, $per_input, $per_batch), "1", "8");
		if(round($unspent['total'], 8) < $total_required){
			throw new \Exception('Not enough coin available for priming - need '.($total_required - $unspent['total']).' BTC more ('.$total_required.' total)'."\n");
		}
		$this->modify_request = false;			
		if($num > $per_batch){
			if($use_stage === 1){
				//do "first stage priming" - create initial outputs for batch set of priming transactions
				$batch_fee = (float)bcadd(bcmul(bcadd((string)convertFloat($per_input), (string)convertFloat($this->min_fee), "8"), (string)$per_batch, "8"), (string)convertFloat($this->min_fee), "8");
				$total_required = (float)bcmul(bcadd((string)convertFloat($batch_fee), (string)convertFloat($this->min_fee), "8"), (string)$num_batches, "8");
				$raw_outputs = array();
				for($i = 0; $i < $num_batches; $i++){
					$raw_outputs[] = array('address' => $address, 'value' => $batch_fee);
				}
				$stage = 1;
			}
			else{
				//do second stage priming and create outputs for each batch
				$total_needed = (float)bcmul((string)convertFloat($per_input), (string)$per_batch, "8");
				$output = array('stage' => 2, 'txs' => array());
				for($i = 0; $i < $num_batches; $i++){	
					$raw_outputs = array();
					for($i2 = 0; $i2 < $per_batch; $i2++){
						$raw_outputs[] = array('address' => $address, 
											   'value' => $per_input
											   );
					}

					$raw_inputs = array();
					$total_used = 0;
					foreach($unspent['utxos'] as $k => $tx){
						$raw_inputs[] = array('txid' => $tx['txid'], 'vout' => $tx['vout']);
						$total_used = (float)bcadd((string)convertFloat($total_used), (string)convertFloat($tx['amount']), "8");
						unset($unspent['utxos'][$k]);
						if($total_used >= $total_needed){
							break;
						}
					}
					$unused_amount = (float)bcsub((string)convertFloat($total_used),
													bcadd((string)convertFloat($total_needed), bcmul((string)count($raw_outputs), (string)convertFloat($this->min_fee), "8"), "8")
									          , "8");
					if($unused_amount > $this->dust_limit){
						$raw_outputs[] = array('address' => $address, 'value' => $unused_amount);
					}							
					
					$output['txs'][] = $this->sendcustomrawtransaction($raw_inputs, $raw_outputs);
					sleep(2);
					
				}
				return $output;
			}

		}
		else{
			//prime as normal, no need for batches
			$raw_outputs = array(); 
			for($i = 0; $i < $num; $i++){
				$raw_outputs[] = array('address' => $address, 'value' => $per_input);
			}
			$stage = 2;
		}
		
		$raw_inputs = array();
		$total_used = 0;
		foreach($unspent['utxos'] as $tx){
			$raw_inputs[] = array('txid' => $tx['txid'], 'vout' => $tx['vout']);
			$total_used = (float)bcadd((string)convertFloat($total_used), (string)convertFloat($tx['amount']), "8");
			if($total_used >= $total_required){
				break;
			}
		}

		$total_required = (float)bcadd((string)convertFloat($total_required), (string)convertFloat($this->min_fee), "8");
		$unused_amount = (float)bcsub((string)convertFloat($total_used), (string)convertFloat($total_required), "8");
		if($unused_amount > $this->dust_limit){
			$raw_outputs[] = array('address' => $address, 'value' => $unused_amount);
		}		

		$output = $this->sendcustomrawtransaction($raw_inputs, $raw_outputs);
		$output['stage'] = $stage;	
		if($stage === 2){
			$output = array('stage' => 2, 'txs' => array($output));
		}
		return $output;
	}
	
	public function sendcustomrawtransaction($raw_inputs, $raw_outputs)
	{
		$create_raw = $this->bitwaspcreate($raw_inputs, $raw_outputs);
		$this->walletpassphrase(XCP_WALLET, $this->wallet_timeout);
		$sign = $this->signrawtransaction($create_raw);
		$this->walletlock();
		$broadcast = false;
		if($sign['complete']){
			$broadcast = $this->sendrawtransaction($sign['hex']);
		}	
		return array('inputs' => $raw_inputs, 'outputs' => $raw_outputs, 'raw' => $create_raw, 'signed' => $sign, 'txid' => $broadcast);	
	}
	
	public function combineaddressutxos($address)
	{
		$unspent = $this->getaddressunspent($address);
		if($unspent['count'] == 0){
			return false;
		}
		$total_send = $unspent['total'] - ($this->min_fee * $unspent['count']);
		$raw_inputs = array();
		foreach($unspent['utxos'] as $tx){
			$raw_inputs[] = array('txid' => $tx['txid'], 'vout' => $tx['vout']);
		}
		$raw_outputs[$address] = $total_send;
		$create_raw = $this->bitwaspcreate($raw_inputs, $raw_outputs);
		$this->walletpassphrase(XCP_WALLET, $this->wallet_timeout);
		$sign = $this->signrawtransaction($create_raw);
		$this->walletlock();
		$broadcast = false;
		if($sign['complete']){
			$broadcast = $this->sendrawtransaction($sign['hex']);
		}	
		return array('inputs' => $raw_inputs, 'outputs' => $raw_outputs, 'raw' => $create_raw, 'signed' => $sign, 'txid' => $broadcast);	
	}
	
	public function checkaddresspriming($address)
	{
		$unspent = $this->getaddressunspent($address, 0);
		$is_priming = false;
		foreach($unspent['utxos'] as $tx){
			if($tx['confirmations'] == 0){
				$get_raw = $this->getrawtransaction($tx['txid']);
				$decode = $this->decoderawtransaction($get_raw);
				$get_raw2 = $this->getrawtransaction($decode['vin'][0]['txid']);
				$decode2 = $this->decoderawtransaction($get_raw2);
				if(isset($decode2['vout'][0]['scriptPubKey']['addresses'][0])){
					if($decode2['vout'][0]['scriptPubKey']['addresses'][0] == $address){
						$is_priming = true;
						break;
					}
				}
			}
		}
		return $is_priming;
	}
	
	
	/**
	* modified version of raw transaction creation from BitWasp library to allow for multiple outputs going to same address
	*
	*/
    public static function bitwaspcreate($inputs, $outputs, $magic_byte = null, $magic_p2sh_byte = null)
    {
        $magic_byte = BitcoinLib::magicByte($magic_byte);
        $magic_p2sh_byte = BitcoinLib::magicP2SHByte($magic_p2sh_byte);

        $tx_array = array('version' => '1');

        // Inputs is the set of [txid/vout/scriptPubKey]
        $tx_array['vin'] = array();
        foreach ($inputs as $input) {
            if (!isset($input['txid']) || strlen($input['txid']) !== 64
                || !isset($input['vout']) || !is_numeric($input['vout'])
            ) {
                return false;
            }

            $tx_array['vin'][] = array('txid' => $input['txid'],
                'vout' => $input['vout'],
                'sequence' => (isset($input['sequence'])) ? $input['sequence'] : 4294967295,
                'scriptSig' => array('hex' => '')
            );
        }

        // Outputs is the set of [address/amount]
        $tx_array['vout'] = array();
        foreach ($outputs as $k => $o) {
			if(!is_array($o)){
				$address = $k;
				$value = $o;
			}
			else{
				$address = $o['address'];
				$value = $o['value'];	
			}
            if (!BitcoinLib::validate_address($address, $magic_byte, $magic_p2sh_byte)) {
                return false;
            }

            $decode_address = BitcoinLib::base58_decode($address);
            $version = substr($decode_address, 0, 2);
            $hash = substr($decode_address, 2, 40);

            if ($version == $magic_p2sh_byte) {
                // OP_HASH160 <scriptHash> OP_EQUAL
                $scriptPubKey = "a914{$hash}87";
            } else {
                // OP_DUP OP_HASH160 <pubKeyHash> OP_EQUALVERIFY OP_CHECKSIG
                $scriptPubKey = "76a914{$hash}88ac";
            }

            $tx_array['vout'][] = array('value' => $value,
                'scriptPubKey' => array('hex' => $scriptPubKey)
            );
        }

        $tx_array['locktime'] = 0;

        return RawTransaction::encode($tx_array);

    }	
	
}
