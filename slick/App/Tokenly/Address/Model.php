<?php
namespace App\Tokenly;
use Core, UI, Util, API;
class Address_Model extends Core\Model
{
	protected function getAddressForm()
	{
		$form = new UI\Form;
		
		$address = new UI\Textbox('address');
		$address->setLabel('BTC Address');
		$address->addAttribute('required');
		$form->add($address);
		
		$label = new UI\Textbox('label');
		$label->setLabel('Label (optional)');
		$form->add($label);
		
		$isPrimary = new UI\Checkbox('isPrimary');
		$isPrimary->setBool(1);
		$isPrimary->setValue(1);
		$isPrimary->setLabel('Primary Address?');
		$form->add($isPrimary);
		
		$isXCP = new UI\Checkbox('isXCP');
		$isXCP->setBool(1);
		$isXCP->setValue(1);
		$isXCP->setLabel('Counterparty Compatible Address?');
		$form->add($isXCP);
		
		$public = new UI\Checkbox('public');
		$public->setBool(1);
		$public->setValue(1);
		$public->setLabel('Public Address?');
		$form->add($public);		
		return $form;
	}
	
	protected function addAddress($data)
	{
		if(!isset($data['userId'])){
			throw new \Exception('No User ID set');
		}
		
		$validTypes = array('btc');
		if(!in_array($data['type'], $validTypes)){
			throw new \Exception('Invalid coin type!');
		}
			
		$validate = new API\BTCValidate;
		if(!isset($data['address'])){
			throw new \Exception('No address set!');
		}
		$check = $validate->checkAddress($data['address']);
		if(!$check){
			throw new \Exception('Invalid Bitcoin address');
		}
		
		//check if they added this address already
		$checkAdded = $this->getAll('coin_addresses', array('userId' => $data['userId'], 'address' => $data['address']));
		if(count($checkAdded) > 0){
			throw new \Exception('Address already submitted');
		}
		
		$isXCP = 0;
		if(isset($data['isXCP']) AND intval($data['isXCP']) === 1){
			$isXCP = 1;
		}
		$isPrimary = 0;
		if(isset($data['isPrimary']) AND intval($data['isPrimary']) === 1){
			$isPrimary = 1;
		}	
		$public = 0;
		if(isset($data['public']) AND intval($data['public']) === 1){
			$public = 1;
		}		
		
		if(!isset($data['label'])){
			$data['label'] = null;
		}	
		
		$useData = array('type' => $data['type'], 'address' => $data['address'], 'submitDate' => timestamp(),
						'isXCP' => $isXCP, 'userId' => $data['userId'], 'label' => $data['label'], 'public' => $public);
		

		$add = $this->insert('coin_addresses', $useData);
		if(!$add){
			throw new \Exception('Error adding address');
		}
		
		if($isPrimary === 1){
			$this->container->switchPrimary($data['userId'], $add);
		}
		
		$get = $this->get('coin_addresses', $add);
		return $get;
	}
	
	protected function switchPrimary($userId, $addressId)
	{
		$sql = 'UPDATE coin_addresses SET isPrimary = 0 WHERE userId = :userId';
		$exec = $this->sendQuery($sql, array('userId' => $userId));
		if(!$exec){
			return false;
		}
		$edit = $this->edit('coin_addresses', $addressId, array('isPrimary' => 1));
		
		if(!$edit){
			return false;
		}
		
		$getAddress = $this->get('coin_addresses', $addressId);
		
		if($getAddress['isXCP'] != 0){
			//update token profile field
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $userId, ':fieldId' => PRIMARY_TOKEN_FIELD), 0, true);
										
			$getField = $this->get('profile_fields', PRIMARY_TOKEN_FIELD);

			if($getField){
				$insertData = array('value' => $getAddress['address'], 'lastUpdate' => timestamp());
				if($getVal){
					$update = $this->edit('user_profileVals', $getVal['profileValId'], $insertData);
				}
				else{
					//insert new one
					$insertData['userId'] = $userId;
					$insertData['fieldId'] = PRIMARY_TOKEN_FIELD;
					$update = $this->insert('user_profileVals', $insertData);
				}
			}
		}
		
		return true;
	}
	
	protected function editAddress($addressId, $data)
	{
		$getAddress = $this->get('coin_addresses', $addressId);
		$isXCP = 0;
		if(isset($data['isXCP']) AND intval($data['isXCP']) === 1){
			$isXCP = 1;
		}
		$isPrimary = 0;
		if(isset($data['isPrimary']) AND intval($data['isPrimary']) === 1){
			$isPrimary = 1;
		}	
		$public = 0;
		if(isset($data['public']) AND intval($data['public']) === 1){
			$public = 1;
		}		
		$useData = array('label' => $data['label'], 'isXCP' => $isXCP, 'isPrimary' => $isPrimary, 'public' => $public);
		$edit = $this->edit('coin_addresses', $addressId, $useData);
		if(!$edit){
			throw new \Exception('Error editing address');
		}
		if($useData['isPrimary'] == 1){
			$this->container->switchPrimary($getAddress['userId'], $addressId);
		}		
		return true;
	}
	
	protected function getDepositAddress($address, $throw_except = false)
	{
		$btc = new API\Bitcoin(BTC_CONNECT);
		$account = 'VERIFY_'.$address['userId'].':'.$address['addressId'];
		try{
			$getAddress = $btc->getaccountaddress($account);
		}
		catch(\Exception $e){
			$getAddress = 'Error retrieving deposit address';
			if($throw_except){
				throw new \Exception($getAddress);
			}
		}
		return $getAddress;		
	}
	
	protected function checkAddressPayment($address)
	{
		if($address['verified'] != 0){
			return array('result' => 'verified');
		}
		$btc = new API\Bitcoin(BTC_CONNECT);
		$account = 'VERIFY_'.$address['userId'].':'.$address['addressId'];
		try{
			$txs = $btc->listtransactions($account);
			$verified = false;
			foreach($txs as $tx){
				if($tx['category'] == 'receive'){
					$getRaw = $btc->getrawtransaction($tx['txid']);
					$decodeRaw = $btc->decoderawtransaction($getRaw);
					$getRaw2 = $btc->getrawtransaction($decodeRaw['vin'][0]['txid']);
					$decodeRaw2 = $btc->decoderawtransaction($getRaw2);
					
					foreach($decodeRaw2['vout'] as $vout){
						$outAddress = $vout['scriptPubKey']['addresses'][0];
						if($outAddress == $address['address'] AND $tx['amount'] > 0){
							$verified = true;
							$btc->move($account, 'VERIFY_FUNDS', $tx['amount']);
						}
					}
				}
			}			
		}
		catch(\Exception $e){
			return array('error' => 'Error getting transactions');
		}

		if($verified){
			$update = $this->edit('coin_addresses', $address['addressId'], array('verified' => 1));
			if(!$update){
				return array('error' => 'Error updating address');
			}
			return array('result' => 'verified');
		}
		
		return array('result' => 'none');
	}
	
	protected function getSecretMessage($address)
	{
		$chars = 12;
		$hash = hash('sha256', md5($address['submitDate'].$address['address'].'_'.$address['addressId']));
		$hashFrag = substr($hash, 0, $chars);
		$output = '';
		$idx = 0;
		$max = ceil($chars / 3);
		for($i = 0; $i < 3; $i++){
			for($i2 = 0; $i2 < $max; $i2++){
				$output .= $hashFrag[$idx];
				$idx++;
			} 
			$output .= ' ';
		}
		$output = trim($output);
		return $output;
		
	}
	
	protected function checkSecretMessage($address, $message = null)
	{
		if($address['verified'] != 0){
			return array('error' => 'Already verified');
		}
		
		if($message == null){
			if(!isset($_POST['message'])){
				return array('error' => 'No message entered');
			}			
			$message = $_POST['message'];
		}
				
		$getMessage = $this->container->getSecretMessage($address);
		$btc = new API\Bitcoin(BTC_CONNECT);
		
		$inputMessage = extract_signature($message);
		
		$result = false;
		try{
			$checkMessage = $btc->verifymessage($address['address'], $inputMessage, $getMessage);
			if($checkMessage){
				$result = true;
			}
		}
		catch(\Exception $e){
			return array('error' => 'Invalid signature');
		}
		
		if($result){
			$update = $this->edit('coin_addresses', $address['addressId'], array('verified' => 1));
			if(!$update){
				return array('error' => 'Error updating address');
			}
			return array('result' => 'verified');
		}
		else{
			return array('result' => 'none');
		}
	}
	
	protected function checkAddressUnverifiable($address)
	{
		$getAddresses = $this->getAll('coin_addresses', array('address' => $address['address'], 'verified' => 1));
		if(count($getAddresses) > 0){
			return true;
		}
		return false;
	}
	
	protected function getBroadcastText($address)
	{
		$site = currentSite();
		$parseDomain = strtoupper(preg_replace('/[^a-z]/i','',$site['domain']));
		$secret = substr(hash('sha256', $address['userId'].$address['address']), 0, 6);
		$text = $parseDomain.'-'.$secret;
		return $text;
	}
	
	protected function checkAddressBroadcast($address)
	{
		if($address['verified'] == 1){
			return true;
		}
		$req_text = $this->container->getBroadcastText($address);
		try{
			$xcp = new API\Bitcoin(XCP_CONNECT);
			$broadcasts = $xcp->get_broadcasts(array('filters' => array('field' => 'source', 'op' => '=', 'value' => $address['address'])));
			$mempool = $xcp->get_mempool();
		}
		catch(\Exception $e){
			throw new \Exception('Error checking broadcasts');
		}
		$found = false;
		foreach($mempool as $pool){
			if($pool['category'] == 'broadcasts'){
				$parse = json_decode($pool['bindings'], true);
				if($parse['source'] == $address['address'] AND $parse['text'] == $req_text){
					$found = true;
					break;
				}
			}
		}			
		if(!$found){
			foreach($broadcasts as $cast){
				if($cast['text'] == $req_text){
					$found = true;
					break;
				}
			}
		}
		if($found){
			$update = $this->edit('coin_addresses', $address['addressId'], array('verified' => 1));
			if(!$update){
				return false;
			}		
		}
		return $found;
	}
	
	protected function getAddressTransactions($addressId, $andUpdate = false)
	{
		$get = $this->get('coin_addresses', $addressId);
		if(!$get OR $get['verified'] == 0){
			return false;
		}
		$meta = new \App\Meta_Model;
		\Core\Model::$cacheMode = false;
		$last_update = $meta->getUserMeta($get['userId'], 'address_tx_update_'.$addressId);
		\Core\Model::$cacheMode = true;
		$get_tx = $this->getAll('coin_addressTx', array('addressId' => $addressId));
		if($andUpdate){
			if($andUpdate === 2){
				//trigger script in background instead of normal retrieve&update
				$update_pid = exec('nohup php '.SITE_BASE.'/scripts/updateUserTransactions.php '.$get['userId'].' > /dev/null &');
				$meta->updateUserMeta($get['userId'], 'tx_list_updating', 1);
			}
			else{
				$diff = false;
				$trigger = 1800; //half an hour
				if($last_update){
					$diff = time() - intval($last_update);
				}		
				if(!$diff OR $diff > $trigger){
					$update_tx = $this->container->updateAddressTransactions($addressId);
					if(is_array($update_tx)){
						$get_tx =  array_merge($update_tx, $get_tx);
					}			
				}
			}
		}
		foreach($get_tx as $k => $row){
			$txInfo = $row['txInfo'];
			if(!is_array($txInfo)){
				$txInfo = json_decode($row['txInfo'], true);
			}
			$txInfo['to_user'] = false;
			if(isset($txInfo['to'])){
				$lookup_to = $this->container->lookupAddress($txInfo['to'], false, $get['userId']);
				if($lookup_to){
					$txInfo['to_user'] = $lookup_to;
				}
			}
			$txInfo['from_user'] = false;
			if(isset($txInfo['from'])){
				$lookup_from = $this->container->lookupAddress($txInfo['from'], false, $get['userId']);
				if($lookup_from){
					$txInfo['from_user'] = $lookup_from;
				}
			}
			$get_tx[$k]['txInfo'] = $txInfo;
			$time = 0;
			if(isset($txInfo['time'])){
				$time = $txInfo['time'];
			}
			$get_tx[$k]['time'] = $time;
			$get_tx[$k]['address'] = $get['address'];
		}
		aasort($get_tx, 'time');
		$get_tx = array_reverse($get_tx);
		return $get_tx;
	}
	
	protected function updateAddressTransactions($addressId)
	{
		$get = $this->get('coin_addresses', $addressId);
		if(!$get OR $get['verified'] == 0){
			return false;
		}
		$meta = new \App\Meta_Model;		
		$meta->updateUserMeta($get['userId'], 'address_tx_update_'.$get['addressId'], time());
		$btc = new \API\Bitcoin(BTC_CONNECT);
		$xcp_parse = new \Tokenly\CounterpartyTransactionParser\Parser;
		$raw_txParser = new \BitWasp\BitcoinLib\RawTransaction;
		$xcp = new \API\Bitcoin(XCP_CONNECT);
		$inventory = new Inventory_Model;		
	
		$get_btc = $btc->getaddresstxlist($get['address'],0, 10);
		if(!is_array($get_btc)){
			return false;
		}
		$tx_list = array();
		foreach($get_btc as $tx){
			$tx_list[] = array('address' => $get['address'], 'txId' => $tx['txId'], 'time' => intval($tx['time']),
							   'amount' => $tx['amount']/SATOSHI_MOD, 'asset' => 'BTC', 'info' => array(), 'type' => 'btc', 'inputs' => $tx['inputs'],
							   'outputs' => $tx['outputs']);
		}
		foreach($tx_list as $k => $tx){
			$check_exists = $this->get('coin_addressTx', $tx['txId'], array(), 'txId');
			if($check_exists){
				unset($tx_list[$k]);
				continue;
			}		
			try{
				$get_raw = $btc->getrawtransaction($tx['txId']);
				//$decode = $raw_txParser->decode($get_raw);
				$decode = $btc->decoderawtransaction($get_raw);
				$info = array();
				$use_n = $tx['outputs'][0]['index'];
				$info['to'] = false;
				$info['from'] = false;
				foreach($tx['outputs'] as $vout){
					if(isset($vout['type']) AND $vout['type'] == 'pubkeyhash'){
						$info['to'] = $vout['address'];
						break;
					}
				}
				
				foreach($tx['inputs'] as $vout){
					if($vout['index'] == $use_n){
						$info['from'] = $vout['address'];
					}
				}
				
				foreach($decode['vin'] as &$vin){
					$vin['addr'] = $info['from'];
				}
				$info['raw_tx'] = $get_raw;
				$tx_list[$k]['info'] = $info;
				$decode['addr'] = $get['address'];
				$tx_list[$k]['raw_decoded'] = $decode;
			}
			catch(\Exception $e){
				unset($tx_list[$k]);				
				continue;
			}
		}
		
		if($get['isXCP'] == 1){
			foreach($tx_list as $k => $tx){
				if(!isset($tx['raw_decoded'])){
					continue;
				}				
				$unpack = $xcp_parse->parseBitcoinTransaction($tx['raw_decoded']);
				if(is_array($unpack) AND isset($unpack['type'])){
					$tx_list[$k]['type'] = 'xcp';
					$tx_list[$k]['info']['xcp_data'] = $unpack;
					$getAsset = false;
					if(isset($unpack['asset'])){
						$tx_list[$k]['asset'] = $unpack['asset'];
						$getAsset = $inventory->getAssetData($unpack['asset']);
						$tx_list[$k]['info']['asset_divisible'] = $getAsset['divisible'];
					}
					if(isset($unpack['quantity'])){
						$quantity = $unpack['quantity'];
						if($getAsset AND $getAsset['divisible'] == 1){
							$quantity = round($quantity / SATOSHI_MOD, 8);
						}
						$tx_list[$k]['amount'] = $quantity;
					}					
				}
			}
		}
		$output_items = array();
		\Core\Model::$cacheMode = false;
		$current_transactions = $this->getAll('coin_addressTx', array('addressId' => $get['addressId']));
		foreach($tx_list as $tx){
			$check_exists = false;
			foreach($current_transactions as $ctx){
				if($tx['txId'] == $ctx['txId']){
					$check_exists = true;
					break;
				}
			}
			if(!$check_exists){
				$new_item = array();
				$new_item['addressId'] = $get['addressId'];
				$new_item['txId'] = $tx['txId'];
				$new_item['type'] = $tx['type'];
				$new_item['amount'] = $tx['amount'];
				$new_item['asset'] = $tx['asset'];
				$tx['info']['time'] = $tx['time'];
				$new_item['txInfo'] = json_encode($tx['info']);
				$save = $this->insert('coin_addressTx', $new_item);
				$item = $new_item;
				$item['addressTxId'] = $save;
				$item['txInfo'] = $tx['info'];
				$output_items[] = $item;
			}
		}
		\Core\Model::$cacheMode = true;
		$meta->updateUserMeta($get['userId'], 'address_tx_update', time());
		$meta->updateUserMeta($get['userId'], 'address_tx_update_'.$get['addressId'], time());
		return $output_items;
	}
	
	protected function lookupAddress($address, $public = false, $includeUser = false)
	{
		$values = array(':address' => $address, ':public' => intval($public));
		$orUser = '';
		if($includeUser){
			$values[':userId'] = $includeUser;
			$orUser = ' OR userId = :userId ';
		}
		$get = $this->fetchAll('SELECT * FROM coin_addresses WHERE address = :address AND (public = :public '.$orUser.')',
								$values);
		if($get AND count($get) > 0){
			$getUser = $this->get('users', $get[0]['userId'], array('userId', 'slug', 'username', 'email'));
			if($getUser){
				return $getUser;
			}
		}
		return false;
	}
}

