<?php
namespace App\Tokenly;
use Core, UI, Util, API;
class Address_Model extends Core\Model
{
	public function getAddressForm()
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
	
	public function addAddress($data)
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
			$this->switchPrimary($data['userId'], $add);
		}
		
		$get = $this->get('coin_addresses', $add);
		return $get;
	}
	
	public function switchPrimary($userId, $addressId)
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
	
	public function editAddress($addressId, $data)
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
			$this->switchPrimary($getAddress['userId'], $addressId);
		}		
		return true;
	}
	
	public function getDepositAddress($address, $throw_except = false)
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
	
	public function checkAddressPayment($address)
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
	
	public function getSecretMessage($address)
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
	
	public function checkSecretMessage($address, $message = null)
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
				
		$getMessage = $this->getSecretMessage($address);
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
	
	public function checkAddressUnverifiable($address)
	{
		$getAddresses = $this->getAll('coin_addresses', array('address' => $address['address'], 'verified' => 1));
		if(count($getAddresses) > 0){
			return true;
		}
		return false;
	}
	
	public function getBroadcastText($address)
	{
		$site = currentSite();
		$parseDomain = strtoupper(preg_replace('/[^a-z]/i','',$site['domain']));
		$secret = substr(hash('sha256', $address['userId'].$address['address']), 0, 6);
		$text = $parseDomain.'-'.$secret;
		return $text;
	}
	
	public function checkAddressBroadcast($address)
	{
		if($address['verified'] == 1){
			return true;
		}
		$req_text = $this->getBroadcastText($address);
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
}
